
<?php
define('ROOT_PATH', __DIR__);
define('ADMIN_PAGE_ROOT_PATH', '/admin');
require_once ROOT_PATH . '/../models/SiteSettingModel.php';

class SiteSettingController {
    private $model;
    
    public function __construct() {
        $this->model = new SiteSettingModel();
    }
    
    public function index() {
        $settings = $this->model->getAllSettings();
        $groups = $this->model->getGroups();
        
        // Группируем настройки по группам
        $groupedSettings = [];
        foreach ($settings as $setting) {
            $groupedSettings[$setting['setting_group']][] = $setting;
        }
        
        require_once ROOT_PATH . '/../views/settings/list.php';
    }
    
    public function create() {
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Валидация
                if (empty($_POST['setting_key'])) {
                    throw new Exception("Ключ обязателен");
                }
                
                if ($this->model->keyExists($_POST['setting_key'])) {
                    throw new Exception("Ключ уже существует");
                }
                
                $data = [
                    'setting_key' => $_POST['setting_key'],
                    'setting_value' => $_POST['setting_value'] ?? '',
                    'setting_type' => $_POST['setting_type'],
                    'setting_group' => $_POST['setting_group'],
                    'description' => $_POST['description'] ?? null,
                    'is_public' => isset($_POST['is_public']) ? 1 : 0,
                    'display_order' => $_POST['display_order'] ?? 0
                ];
                
                if ($this->model->createSetting($data)) {
                    header('Location: ?action=settings&success=created');
                    exit;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $groups = $this->model->getGroups();
        require_once ROOT_PATH . '/../views/settings/create.php';
    }
    
    public function edit($id) {
        $error = null;
        $setting = $this->model->getSettingById($id);
        
        if (!$setting) {
            header('Location: ?action=settings&error=not_found');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Проверяем, не занят ли новый ключ другим элементом
                if ($_POST['setting_key'] !== $setting['setting_key']) {
                    if ($this->model->keyExists($_POST['setting_key'])) {
                        throw new Exception("Ключ уже используется другим параметром");
                    }
                }
                
                $data = [
                    'setting_key' => $_POST['setting_key'],
                    'setting_value' => $_POST['setting_value'] ?? '',
                    'setting_type' => $_POST['setting_type'],
                    'setting_group' => $_POST['setting_group'],
                    'description' => $_POST['description'] ?? null,
                    'is_public' => isset($_POST['is_public']) ? 1 : 0,
                    'display_order' => $_POST['display_order'] ?? 0
                ];
                
                if ($this->model->updateSetting($id, $data)) {
                    header('Location: ?action=settings&success=updated');
                    exit;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $groups = $this->model->getGroups();
        require_once ROOT_PATH . '/../views/settings/edit.php';
    }
    
    public function delete($id) {
        $setting = $this->model->getSettingById($id);
        
        if (!$setting) {
            header('Location: ?action=settings&error=not_found');
            exit;
        }
        
        // Запрещаем удаление системных настроек
        if ($setting['setting_group'] === 'system') {
            header('Location: ?action=settings&error=system_protected');
            exit;
        }
        
        if ($this->model->deleteSetting($id)) {
            header('Location: ?action=settings&success=deleted');
            exit;
        } else {
            header('Location: ?action=settings&error=delete_failed');
            exit;
        }
    }
    
    public function bulkUpdate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'setting_') === 0) {
                        $settingId = substr($key, 8); // Убираем 'setting_'
                        $setting = $this->model->getSettingById($settingId);
                        
                        if ($setting) {
                            $this->model->updateSetting($settingId, [
                                'setting_key' => $setting['setting_key'],
                                'setting_value' => $value,
                                'setting_type' => $setting['setting_type'],
                                'setting_group' => $setting['setting_group'],
                                'description' => $setting['description'],
                                'is_public' => $setting['is_public'],
                                'display_order' => $setting['display_order']
                            ]);
                        }
                    }
                }
                
                header('Location: ?action=settings&success=bulk_updated');
                exit;
            } catch (Exception $e) {
                header('Location: ?action=settings&error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }
    
    public function getSettingsByGroup($group){
        $settings = $this->model->getSettingsByGroup($group);
        if($settings){
            return [
                'success' => true,
                'settings' => $settings
            ];
        } else {
            return [
                'success' => false,
                'message' => 'group not found'
            ];
        }
    }
    // Метод для получения настройки через AJAX
    public function getSettingValue($key) {
        $setting = $this->model->getSettingByKey($key);
        if ($setting) {
            /*echo json_encode([
                'success' => true,
                'value' => $setting['setting_value']
            ]);*/
            return [
                'success' => true,
                'value' => $setting['setting_value']
            ];
        } else {
            /*echo json_encode([
                'success' => false,
                'message' => 'Setting not found'
            ]);*/
            return [
                'success' => false,
                'message' => 'Setting not found'
            ];
        }
        exit;
    }
    
    // Метод для обновления настройки через AJAX
    public function updateSettingValue($key, $value) {
        if ($this->model->updateValueByKey($key, $value)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}
?>
