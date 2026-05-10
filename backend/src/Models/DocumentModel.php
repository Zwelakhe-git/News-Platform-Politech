<?php
namespace Thunderpc\Vkurse\Models;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\Database;
use Thunderpc\Vkurse\Utils\Log;

Log::init();
class DocumentModel extends Database {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function createDocument($url, $name, $mime, $owner_id){
        try{
            $sql = "INSERT INTO documents (url, name, mime, owner_id) VALUES (?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$url, $name, $mime, $owner_id]);

            return $this->pdo->lastInsertId();
        } catch(PDOException $e){
            Log::error($e->getMessage());
            return -1;
        }
    }

    public function deleteDocument($id, $owner_id){
        try{
            $sql = "DELETE FROM documents WHERE id = ? AND owner_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $owner_id]);
            return true;
        } catch(PDOException $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getDocumentById($id, $owner_id){
        try{
            $sql = "SELECT * FROM documents WHERE id = ? AND owner_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $owner_id]);
            return $stmt->fetch();
        } catch(PDOException $e){}
    }
}
?>