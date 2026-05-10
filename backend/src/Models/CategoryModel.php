<?php
namespace Thunderpc\Vkurse\Models;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\Database;
use Thunderpc\Vkurse\Utils\Log;

Log::init();
class CategoryModel extends Database {
    public function __construct(){
        parent::__construct();
    }

    public function createCategory($name, $description=null){
        try{
            $columns = ['name'];
            $values = [$name];
            if($description) { 
                $columns .= ',description'; 
                $values[] = $description; 
            }
            $columnList = implode(',', $columns);
            $placeholders = implode(',', array_fill(0, count($values), "?"));
            
            $sql = "INSERT INTO categories ($columnList) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $res = $stmt->execute($values);

            return [
                'success' => $res,
                'category_id' => $this->pdo->lastInsertId()
            ];
        } catch(\Exception $e){
            Log::error(" Failed to create category [{$e->getMessage()}]");
            return [
                'success' => false,
                'message' => 'Server error'
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
        } catch(\PDOException $e){
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Server Error'];
        }
    }
    public function getCategoryId($name){
        $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $res = $stmt->execute([$name]);
        
        return $res ? ($stmt->fetch())['id'] : -1;
    }
}
?>