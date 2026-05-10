<?php
namespace Thunderpc\Vkurse\Models;
use Thunderpc\Vkurse\Models\Database;

class SiteSettingModel extends Database {
    public function __construct(){
        parent::__construct();
    }
    
    public function getAllSettings() {
        $stmt = $this->pdo->query("
            SELECT * FROM site_settings 
            ORDER BY setting_group ASC, setting_key ASC
        ");
        return $stmt->fetchAll();
    }
    
    public function getSettingById($id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM site_settings 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getSettingByKey($key) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM site_settings 
            WHERE setting_key = ?
        ");
        $stmt->execute([$key]);
        return $stmt->fetch();
    }
    
    public function createSetting($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO site_settings 
            (setting_key, setting_value, setting_type, setting_group, description, is_public, display_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['setting_key'],
            $data['setting_value'],
            $data['setting_type'],
            $data['setting_group'],
            $data['description'] ?? null,
            $data['is_public'] ?? 1,
            $data['display_order'] ?? 0
        ]);
    }
    
    public function updateSetting($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE site_settings 
            SET setting_key = ?, 
                setting_value = ?, 
                setting_type = ?, 
                setting_group = ?, 
                description = ?, 
                is_public = ?, 
                display_order = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['setting_key'],
            $data['setting_value'],
            $data['setting_type'],
            $data['setting_group'],
            $data['description'] ?? null,
            $data['is_public'] ?? 1,
            $data['display_order'] ?? 0,
            $id
        ]);
    }
    
    public function deleteSetting($id) {
        $stmt = $this->pdo->prepare("
            DELETE FROM site_settings 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
    
    public function getSettingsByGroup($group) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM site_settings 
            WHERE setting_group = ? 
            ORDER BY display_order ASC
        ");
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    }
    
    public function getGroups() {
        $stmt = $this->pdo->query("
            SELECT DISTINCT setting_group 
            FROM site_settings 
            ORDER BY setting_group ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function updateValueByKey($key, $value) {
        $stmt = $this->pdo->prepare("
            UPDATE site_settings 
            SET setting_value = ? 
            WHERE setting_key = ?
        ");
        return $stmt->execute([$value, $key]);
    }
    
    public function getPublicSettings() {
        $stmt = $this->pdo->query("
            SELECT setting_key, setting_value 
            FROM site_settings 
            WHERE is_public = 1 
            ORDER BY setting_group, display_order
        ");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public function keyExists($key) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM site_settings 
            WHERE setting_key = ?
        ");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
