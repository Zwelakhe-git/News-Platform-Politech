<?php
namespace Thunderpc\Vkurse\Api;
require_once __DIR__ . '/../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Models\ImageModel;
use Thunderpc\Vkurse\Models\DocumentModel;
use Thunderpc\Vkurse\Models\AdminModel;
use Thunderpc\Vkurse\Utils\Utils;
use Thunderpc\Vkurse\Auth;
use Thunderpc\Vkurse\Utils\Log;
use Dotenv\Dotenv;

use function curl_init;
use function curl_setopt;
use function curl_exec;
use function curl_close;
use function curl_getinfo;
use function curl_setopt_array;
use function curl_errno;
use function curl_error;

$dotenv = Dotenv::createImmutable(BASE_DIR, '.env');
$dotenv->load();
Log::init();

class UploadApi{
    private $adminModel;
    private $imageModel;
    private $documentModel;
    private $utils;
    public function __construct(){
        $this->adminModel = new AdminModel();
        $this->imageModel = new ImageModel();
        $this->documentModel = new DocumentModel();
        $this->utils = new Utils();
    }

    public function saveFile($req, $res){
        try{
            $type = $req->params->type;
            $id = null;
            $fileUrl = null;
            $file = null;
            $fileInfo = null;

            $apiKey = $req->query->key;
            header("Access-Control-Allow-Origin: *");

            if(!$apiKey){
                $res->status(500)->json([
                    'success' => false,
                    'message' => 'Missing api key. See documentation for api usage'
                ]);
                return;
            }

            if(!$this->adminModel->acceptAPIKey($apiKey)){
                $res->status(200)->json([
                    'success' => false,
                    'message' => 'invalid api key'
                ]);
                return;
            }
            $owner_id = $this->adminModel->getApiKeyOwnerId($apiKey);
            if(!empty($_FILES)){
                if($type === 'image'){
                    $fileInfo = $this->utils->upload(IMAGES_PATH, [], $_FILES['image']);
                    $id = $this->imageModel->createImage($fileInfo['filepath'], $fileInfo['mime_type'], $owner_id);
                } else{
                    $fileInfo = $this->utils->upload(DOCUMENTS_PATH, [], $_FILES['file']);
                    $id = $this->documentModel->createDocument($fileInfo['filepath'], $_FILES['file']['name'], $fileInfo['mime_type'], $owner_id);
                }
                $res->status(200)->json([
                    'success' => true,
                    'data' => [
                        'id' => $id,
                        'url' => $fileInfo['filepath']
                    ]
                ]);
                return;
            } else {
                $res->json([
                    'success' => false,
                    'message' => 'File not received'
                ]);
                return;
            }
        } catch(\Exception $e){
            Log::error($e->getMessage());
            $res->json([
                'success' => false,
                'message' => 'Server Error'
            ]);
            return;
        }
    }

    public function deleteFile($req, $res){
        
        try{
            $type = $req->params->type;
            $id = $req->query->id;
            $apiKey = $req->query->key;

            if(!$apiKey){
                $res->status(500)->json([
                    'success' => false,
                    'message' => 'Missing api key. See documentation for api usage'
                ]);
                return;
            }
            
            if(!$this->adminModel->acceptAPIKey($apiKey)){
                $res->status(200)->json([
                    'success' => false,
                    'message' => 'invalid api key'
                ]);
                return;
            }
            $owner_id = $this->adminModel->getApiKeyOwnerId($apiKey);
            if($type === 'image'){
                $data = $this->imageModel->getImagebyId($id, $owner_id);
                if(empty($data)){
                    $res->json([
                        'success' => false,
                        'message' => "No File with the given id or owner: $id, $owner_id"
                    ]);
                } else {
                    $this->utils->deleteFile($data['url']);
                    $this->imageModel->deleteImage($id, $owner_id);
                }
            } else {
                $data = $this->documentModel->getDocumentById($id, $owner_id);
                if(empty($data)){
                    $res->json([
                        'success' => false,
                        'message' => 'No File with the given id or owner'
                    ]);
                } else {
                    $this->utils->deleteFile($data['url']);
                    $this->documentModel->deleteDocument($id, $owner_id);
                }
            }

            $res->json([
                'success' => true,
                'message' => 'file successfully deleted'
            ]);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            $res->json([
                'success' => false,
                'message' => 'Server Error'
            ]);
        }
    }

    public function uploadToImgbb($req, $res){
        try {
            if(isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_OK){
                Log::error("Error during image upload");
                $res->json([
                    'success' => false,
                    'message' => 'Failed to upload image'
                ]);
                return;
            }


            $imgbbApiKey = $_ENV['IMGBB_API_KEY'] ?? getenv('IMGBB_API_KEY');
            if(!$imgbbApiKey){
                Log::error("No api key for imgbb");
                $res->json([
                    'success' => false,
                    'message' => 'Missing api key'
                ]);
                return;
            }

            $imagePath = $_FILES['image']['tmp_name'];
            if(!file_exists($imagePath)){
                Log::info("Problem with save path");
                $res->json([
                    'success' => false,
                    'message' => 'Server error'
                ]);
                return;
            }
            
            $imageData = base64_encode(file_get_contents($imagePath));
            $postData = [
                'image' => $imageData,
                'name' => $_FILES['image']['name'],
            ];
            
            $url = "https://api.imgbb.com/1/upload?key=$imgbbApiKey&expiration=3600";
            $ch = curl_init();
            

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'accept: application/json'
                ],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            ]);
            Log::info("upload url: $url");

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_errno($ch);
            curl_close($ch);
            
            if($error){
                Log::error("error while sending request: " . curl_error($ch));
                $res->json([
                    'success' => false,
                    'message' => 'Server Error'
                ]);
                return;
            }

            $result = json_decode($response, true);
            if($httpCode === 200 && isset($result['success']) && $result['success']){
                Log::info("successfully uploaded file to imgbb");
                $res->json([
                    'success' => true,
                    'data' => [
                        'id' => $result['data']['id'],
                        'url' => $result['data']['url'],
                        'display_url' => $result['data']['display_url'],
                        'thumbnail' => $result['data']['thumb']['url'],
                        'delete_url' => $result['data']['delete_url']
                    ]
                ]);
                return;
            } else {
                Log::error("Image upload to imgbb failed. Raw response:");
                Log::error($result);
                $res->json([
                    'success' => false,
                    'message' => 'Error uploading file to imgbb'
                ]);
                return;
            }

            $res->json([
                'success' => false,
                'message' => 'Failed to upload file to imgbb'
            ]);
            return;
        } catch(\Exception $e){
            Log::error("Error in upload to imgbb: {$e->getMessage()}");
            $res->json([
                'success' => false,
                'message' => 'Server error'
            ]);
            return;
        }
        $res->json([
            'success' => false,
            'message' => 'Unknown Error'
        ]);
    }

    public function deleteFromImgbb($req, $res){
        try {
            $imgbApiKey = $_ENV['IMGBB_API_KEY'] ?? getenv('IMGBB_API_KEY');
            if(!$imgbApiKey){
                Log::error("No api key for imgbb");
                $res->json([
                    'success' => false,
                    'message' => 'Missing api key'
                ]);
                exit;
            }
            $postData = [
                'action' => 'delete',
                'delete' => 'image',
                'from' => 'resource',
                'deleting[id]' => $req->body->imageId,
                'deleting[hash]' => $req->body->imageHash
            ];
            
            $url = $req->body->deleteUrl;
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'accept: application/json'
                ],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSH_VERIFYHOST => 2
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_errno($ch);
            curl_close($ch);
            
            if($error){
                Log::error("error while receving response: " . curl_error($ch));
                $res->json([
                    'success' => false,
                    'message' => 'Server Error'
                ]);
                exit;
            }

            $result = json_decode($response, true);
            if($httpCode === 200 && isset($result['success']) && $result['success']){
                Log::info("successfully uploaded file to imgbb");
                $res->json([
                    'success' => true,
                    'image_data' => [
                        'id' => $result['data']['id'],
                        'url' => $result['data']['url'],
                        'display_url' => $result['data']['display_url'],
                        'thumbnail' => $result['data']['thumb']['url'],
                        'delete_url' => $result['data']['delete_url']
                    ]
                ]);
                exit;
            } else {
                Log::error("Failed to delete image from imgbb. Raw response:");
                Log::error($result);
                $res->json([
                    'success' => false,
                    'message' => "Error deleteing file '{$req->body->imageId}' from imgbb"
                ]);
                exit;
            }

            $res->json([
                'success' => false,
                'message' => 'Failed to delete file from imgbb'
            ]);
            exit;
        } catch(\Exception $e){
            Log::error("Error in upload to imgbb: {$e->getMessage()}");
            $res->json([
                'success' => false,
                'message' => 'Server error'
            ]);
            exit;
        }
        $res->json([
            'success' => false,
            'message' => 'Unknown Error'
        ]);
    }
}
?>