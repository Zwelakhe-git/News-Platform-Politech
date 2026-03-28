<?php
namespace Thunderpc\Vkurse\Admin\Models;
require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;

class CategoryModel extends Database {
    public function __construct(){
        parent::__construct();
    }

    public function createCategory($data){
        try{
            $sql = 'INSERT INTO categories (name, description) VALUES (?,?)';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($data);

            return [
                'success' => $res ? true : false,
                'category_id' => $res ? $this->pdo->lastInsertId() : -1
            ];
        } catch(Exception $e){
            file_put_contents(LOG_PATH, date('Y-m-d H:i:s') . " Failed to create category [{$e->getMessage()}] at CategoryModel - createCategory");
            return [
                'success' => false,
                'category_id' => -1
            ];
        }
    }

    public function deleteCategory($id){
        try{
            $sql = 'DELETE FROM categories WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute([$id]);
            return [
                'success' => $res ? true : false
            ];
        } catch(PDOException $e){
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        }
    }
    public function getCategoryId($name){
        $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $res = $stmt->execute($name);
        
        return $res ? $res->fetchColumn() : -1;
    }
}
?>