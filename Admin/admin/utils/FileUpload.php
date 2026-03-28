<?php
namespace Thunderpc\Vkurse\Admin\Utils;
require_once __DIR__ . '/../../../config/config.php';

class FileUploader {
    // use the default MEDIA_ROOT instead
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    
    public function __construct($uploadDir = UPLOAD_DIR, $allowedTypes = [], $maxSize = 50000000) {
        $this->uploadDir = $uploadDir;
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize;
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function upload($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log('Ошибка загрузки файла');
            throw new Exception('Ошибка загрузки файла');
        }
        
        if ($file['size'] > $this->maxSize) {
            error_log('Файл слишком большой: ' . $file['size']);
            throw new Exception('Файл слишком большой: ' . $file['size']);
        }
        
        $fileType = mime_content_type($file['tmp_name']);
        if (!empty($this->allowedTypes) && !in_array($fileType, $this->allowedTypes)) {
            error_log('Тип файла не разрешен: ' . $file['name'] . ' - ' . $fileType);
            throw new Exception('Тип файла не разрешен: ' . $file['name'] . ' - ' . $fileType);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $filePath = $this->uploadDir . '' . $fileName;
        $fileUrl = substr($filePath, strpos($filePath,'/admin'));
        //error_log('File "' . $file['name'] . '" accepted. Path:'.$filePath.', Url: '.$fileUrl, 'info');
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // consider filepath = MEDIA_URL . $filename
            return [
                'filename' => $fileName,
                'filepath' => $fileUrl,
                'mime_type' => $fileType,
                'size' => $file['size']
            ];
        }
        error_log('Не удалось сохранить файл');
        throw new Exception('Не удалось сохранить файл');
    }
    
    public function delete_file($path, $path_type="url"){
        /*
        	deletes a file from the server.
            path - the file's path. if its a url, then normalise. It should be relative to the server base dir.
            		If it's a full path, then do nothing.
            path_type - values [url, path]. indicates the type of the file path.
        */
        if($path_type === 'url' || $path[0] === '/'){
            if(!defined('BASE_DIR')){
                // reject if base dir is not defined. for security it should be defined in the config file.
                error_log('Failed to delete file from url. BASE_DIR not defined');
                return false;
                // define('BASE_DIR', __DIR__ . '/../..');
            }
            $path = BASE_DIR . $path;
        }
        if(file_exists($path)){
            unlink($path);
            error_log('File (' . basename($path) . ') successfully deleted', 'info');
            return true;
        } else {
            error_log('Failed to delete file (' . basename($path) . '). File Not Found', 'warn');
            return false;
        }
    }
}
?>