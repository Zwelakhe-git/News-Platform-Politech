<?php
namespace Thunderpc\Vkurse\Admin\Utils;
require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Utils\Log;
use Exception;

Log::init();
class FileUploader {
    // use the default MEDIA_ROOT instead
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    
    public function __construct($uploadDir = UPLOAD_DIR, $allowedTypes = []) {
        $this->uploadDir = $uploadDir;
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = 50000000;
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function upload($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Log::error('Ошибка загрузки файла');
            throw new Exception('Ошибка загрузки файла');
        }
        Log::info("saving uploaded file {$file['name']}");
        
        if ($file['size'] > $this->maxSize) {
            Log::error('Файл слишком большой: ' . $file['size']);
            throw new Exception('Файл слишком большой: ' . $file['size']);
        }
        
        $fileType = mime_content_type($file['tmp_name']);
        if (!empty($this->allowedTypes) && !in_array($fileType, $this->allowedTypes)) {
            Log::error('Тип файла не разрешен: ' . $file['name'] . ' - ' . $fileType);
            throw new Exception('Тип файла не разрешен: ' . $file['name'] . ' - ' . $fileType);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $realName = $file['name'];
        $filePath = $this->uploadDir . "/$fileName";
        $fileUrl = $filePath;

        if(preg_match("/htdocs(.*)/", str_replace("\\","/",$filePath), $matches)){
            $fileUrl = $matches[1];
        }
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // consider filepath = MEDIA_URL . $filename
            return [
                'filename' => $fileName,
                'realname' => $realName,
                'filepath' => $fileUrl,
                'mime_type' => $fileType,
                'size' => $file['size']
            ];
        }
        Log::error('Не удалось сохранить файл');
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
            if(!defined('HTDOCS')){
                Log::error('Failed to delete file from url. BASE_DIR not defined');
                return false;
            }
            $path = HTDOCS . $path;
        }
        if(file_exists($path)){
            unlink($path);
            Log::info('File (' . basename($path) . ') successfully deleted');
            return true;
        } else {
            Log::error('Failed to delete file (' . basename($path) . '). File Not Found');
            return false;
        }
    }
}
?>