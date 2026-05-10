<?php
namespace Thunderpc\Vkurse\Models;

error_reporting(E_ALL); 
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../log/log.log');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/servers.php';
require_once HTDOCS . '/vendor/autoload.php';

use PDO;
use Redis;
use Predis\Client;
use Predis\Connection\ConnectionException;
use Thunderpc\Vkurse\Utils\Log;

//use Thunderpc\Vkurse\Admin\Models\Elasticsearch;

Log::init();
class Database {
    protected static $sharedPdo = null; // Static shared connection
    protected static $sharedRedisClient = null;
    protected $pdo;
    protected $redisClient;
    private $lastConnectTime = 0;
    private $queryCount = 0;
    
    private const MAX_RETRIES = 2;
    private const CONNECT_TIMEOUT = 5;
    private const QUERY_TIMEOUT = 10;
    private const RECONNECT_DELAY = 2;
    private const MAX_QUERIES_PER_CONNECTION = 100;
    
    public function __construct() {
        // Use shared connection instead of creating new one
        if (self::$sharedPdo === null) {
            self::$sharedPdo = $this->createConnection();
        }
        if(self::$sharedRedisClient === null){
            self::$sharedRedisClient = $this->createRedisConnection();
        }
        $this->pdo = self::$sharedPdo;
        $this->redisClient = self::$sharedRedisClient;
    }
    
    private function createConnection() {
        $retryCount = 0;
        while ($retryCount <= self::MAX_RETRIES){
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER, 
                    DB_PASS,
                    [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => false,
                        PDO::ATTR_TIMEOUT => self::CONNECT_TIMEOUT,
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    ]
                );
                
                $this->lastConnectTime = time();
                Log::info("Database connection established successfully");
                return $pdo;

            } catch(\PDOException $e) {
                $errorMessage = $e->getMessage();
                Log::error("Database connection attempt " . ($retryCount + 1) . " failed: " . $errorMessage);
                
                if (strpos($errorMessage, 'max_user_connections') !== false) {
                    $retryCount++;
                    if ($retryCount <= self::MAX_RETRIES) {
                        Log::error("Waiting " . self::RECONNECT_DELAY . " seconds before retry...");
                        sleep(self::RECONNECT_DELAY);
                        continue;
                    }
                }

                if ($retryCount === 0) {
                    sleep(1);
                    $retryCount++;
                    continue;
                }

                $this->handleCriticalError($errorMessage);
            }
        }
    }
    private function createRedisConnection(){
        $retryCount = 0;
        
        while ($retryCount <= self::MAX_RETRIES){
            try {
                // $client = new Redis();
                // $client->connect(REDIS_HOST, REDIS_PORT);
                $client = new Client([
                    'scheme' => 'tcp',
                    'host'   => REDIS_HOST,
                    'port'   => 6379,
                    'timeout' => 10,
                    'read_write_timeout' => 30,
                    'persistent' => true,
                    'retries' => 3,
                    'retry_interval' => 100
                ]);
                // $client = new Redis([
                //     'host' => REDIS_HOST,
                //     'port' => REDIS_PORT,
                //     'connectTimeout' => 2.5,
                //     'auth' => ['phpredis', 'phpredis'],
                //     'ssl' => ['verify_peer' => false],
                //     'backoff' => [
                //         'algorithm' => Redis::BACKOFF_ALGORITHM_DECORRELATED_JITTER,
                //         'base' => 500,
                //         'cap' => 750,
                //     ]
                // ]);
                Log::info("Redis connection established successfully");
                return $client;
            } catch(ConnectionException $e){
                $errorMessage = $e->getMessage();
                Log::error("Redis connection attempt " . ($retryCount + 1) . " failed: " . $errorMessage);
                
                //if (strpos($errorMessage, 'max_user_connections') !== false) {
                $retryCount++;
                if ($retryCount <= self::MAX_RETRIES) {
                    Log::error("Waiting " . self::RECONNECT_DELAY . " seconds before retry...");
                    sleep(self::RECONNECT_DELAY);
                    continue;
                }
                //}

                if ($retryCount === 0) {
                    sleep(1);
                    $retryCount++;
                    continue;
                }
            } catch(\Exception $e){
                Log::error($e->getMessage());
                $retryCount++;
            } catch(\Throwable $e){
                Log::error($e->getMessage());
                $retryCount++;
            }
        }
        
    }
    
    public static function closeSharedConnection() {
        if (self::$sharedPdo !== null) {
            self::$sharedPdo = null;
            Log::info("Shared database connection closed");
        }
        if (self::$sharedRedisClient !== null) {
            self::$sharedRedisClient = null;
            Log::info("Shared sharedRedisClient connection closed");
        }
    }

    public function getPdo(){
        return $this->pdo;
    }

    public function getSharedPdo(){
        return self::$sharedPdo;
    }
    
    private function handleCriticalError($errorMessage) {
        Log::error("CRITICAL DB ERROR: " . $errorMessage);
        
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            die("Database connection failed: " . htmlspecialchars($errorMessage));
        }
        
        if (!headers_sent()) {
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Retry-After: 30');
        }
        
        if (file_exists(TEMPLATES_DIR . '/errors/database_error.html')) {
            include TEMPLATES_DIR . '/errors/database_error.html';
        } else {
            Log::warn('Service Temporarily Unavailable');
            Log::warn('The database is currently experiencing high load. Please try again in a few moments.');
        }
        exit;
    }

}
?>