<?php
namespace Thunderpc\Vkurse\Tests;

require_once __DIR__ . '/../../vendor/autoload.php';

use Thunderpc\Vkurse\Models\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
    private Database $db;

    protected function setUp(): void{
        $this->db = new Database();
    }

    public function testConnectionSuccess(): void{
        $this->assertTrue($this->db->getSharedPdo() !== null);
    }

    public function testCloseSharedConnection(): void{
        if(!$this->db->getSharedPdo()){
            echo 'pdo is supposed to be non-null before closing connection';
            $this->assertTrue(false);
        } else {
            $this->db->closeSharedConnection();
            $this->assertTrue($this->db->getSharedPdo() === null);
        }
    }

    public function testQuery(): void{
        try{
            $pdo = $this->db->getPdo();
            $stmt = $pdo->query("SELECT * FROM users");
            $res = $stmt->fetchAll();
            $this->assertTrue(is_array($res), 'PDO did not return array: ' . is_array($res));
        } catch (PDOException $e){
            $this->assertTrue(false, 'Error: ' . $e->getMessage());
        }
    }
}
?>