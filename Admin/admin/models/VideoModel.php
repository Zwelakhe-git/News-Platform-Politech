<?php
require_once 'Database.php';

class VideoModel extends Database {
    public function __construct(){
        parent::__construct();
    }
    
    public function getAllVideos() {
        $stmt = $this->pdo->query("
            SELECT v.*, i.location as image_location 
            FROM Videos v 
            LEFT JOIN Images i ON v.vidImg = i.id 
            ORDER BY v.id DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getVideoById($id) {
        $stmt = $this->pdo->prepare("
            SELECT v.*, i.location as image_location 
            FROM Videos v 
            LEFT JOIN Images i ON v.vidImg = i.id 
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createVideo($vidImg, $vidTitle, $location, $mime_type) {
        try{
            $stmt = $this->pdo->prepare("
                INSERT INTO Videos (vidImg, vidTitle, location, mime_type) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $vidImg,
                $vidTitle,
                $location,
                $mime_type
            ]);
            return $this->pdo->lastInsertId();
        } catch(PDOException $e){
            error_log($e->getMessage());
            return -1;
        }
    }
    
    public function updateVideo($id, $vidImg, $vidTitle, $location, $mime_type) {
        $stmt = $this->pdo->prepare("
            UPDATE Videos 
            SET vidImg = ?, vidTitle = ?, location = ?, mime_type = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $vidImg,
            $vidTitle,
            $location,
            $mime_type,
            $id
        ]);
    }
    
    public function deleteVideo($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Videos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>