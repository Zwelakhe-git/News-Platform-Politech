<?php
require_once 'Database.php';

class MainPageContentModel extends Database {
    public function __construct(){
        parent::__construct();
    }
    public function getAllItems(){
        $stmt = $this->pdo->query("SELECT * FROM mainpagecontent");
        return $stmt->fetchAll();
    }
    
    public function getItemsByType($type){
        try{
            $stmt = $this->pdo->prepare("SELECT DISTINCT ? FROM mainpagecontent WHERE $type IS NOT NULL");
            $stmt->execute([
                $type
            ]);
            return $stmt->fetchAll();
        } catch(PDOException $e){}
    }
    
    public function itemExists($type, $id){
        try{
            $query = "SELECT $type FROM mainpagecontent WHERE $type = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll();
            logMessage("MPCModel - " . $query,'info');
            logMessage("MPCModel - $type - $id already exists: " . count($result), 'info');
            return !empty($result);
        } catch(PDOException $e){
            error_log($e->getMessage());
            return false;
        }
    }
    public function addItem($type, $id){
        try{
            if($this->itemExists($type, $id)){
                logMessage("MP content " . $type . " already inserted");
                return;
            }
            logMessage("inserting " . $type . " into MPC", "info");
            // first slide into an empty slot
            $query = "UPDATE mainpagecontent SET $type = ? WHERE $type IS NULL LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute([$id]);
            
            if($result){
                return true;
            }
            
            // make a new record if there isnt an empty slot
            $query = "INSERT INTO mainpagecontent ($type) VALUES (?)";
            $stmt = $this->pdo->prepare($query);
        	return $stmt->execute([$id]);
        } catch(PDOException $e){
            error_log($e->getMessage());
            return false;
        } catch(Exception $e){
            error_log($e->getMssage());
            return false;
        }
    }
    
    public function deleteItem($type, $id){
        try{
            // empty an existing slot
            $query = "UPDATE mainpagecontent SET $type = NULL WHERE $type = ?";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$id]);
        } catch(PDOException $e){
            error_log($e->getMessage());
            return false;
        }
    }
}

?>