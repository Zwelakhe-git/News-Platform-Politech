<?php
namespace Thunderpc\Vkurse\Admin\Models;
require_once __DIR__ . '/../../../config/config.php';
require_once HTDOCS . '/vendor/autoload.php';

use Thunderpc\Vkurse\Admin\Models\Database;

class DocumentModel extends Database {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function getAllDocuments($status = null, $type = null) {
        try {
            $sql = "SELECT d.*, u.name as author_name, i.location as featured_image 
                    FROM documents d 
                    LEFT JOIN users u ON d.author_id = u.id 
                    LEFT JOIN Images i ON d.featured_image_id = i.id 
                    WHERE 1=1";
            
            $params = [];
            
            if ($status) {
                $sql .= " AND d.status = ?";
                $params[] = $status;
            }
            
            if ($type) {
                $sql .= " AND d.document_type = ?";
                $params[] = $type;
            }
            
            $sql .= " ORDER BY d.created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $documents = $stmt->fetchAll();
            
            // Получаем категории для каждого документа
            foreach ($documents as &$doc) {
                $doc['categories'] = $this->getDocumentCategories($doc['id']);
                $doc['images'] = $this->getDocumentImages($doc['id']);
            }
            
            return $documents;
            
        } catch (PDOException $e) {
            error_log("DocumentModel Error: " . $e->getMessage());
            return ['error' => 'Database error'];
        }
    }
    
    public function getDocument($id) {
        try {
            $sql = "SELECT d.*, u.name as author_name, i.location as featured_image 
                    FROM documents d 
                    LEFT JOIN users u ON d.author_id = u.id 
                    LEFT JOIN Images i ON d.featured_image_id = i.id 
                    WHERE d.id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $document = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($document) {
                $document['categories'] = $this->getDocumentCategories($id);
                $document['images'] = $this->getDocumentImages($id);
            }
            
            return $document;
            
        } catch (PDOException $e) {
            error_log("DocumentModel Error: " . $e->getMessage());
            return ['error' => 'Database error'];
        }
    }
    
    public function saveDocument($data) {
        try {
            $this->pdo->beginTransaction();
            
            if (isset($data['id']) && $data['id']) {
                // Обновление существующего документа
                $sql = "UPDATE documents SET 
                        title = ?, slug = ?, content = ?, content_json = ?, excerpt = ?,
                        featured_image_id = ?, status = ?, document_type = ?, updated_at = NOW()
                        WHERE id = ?";
                
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    $data['title'],
                    $data['slug'],
                    $data['content'],
                    isset($data['content_json']) ? json_encode($data['content_json']) : null,
                    $data['excerpt'],
                    $data['featured_image_id'] ?? null,
                    $data['status'] ?? 'draft',
                    $data['document_type'] ?? 'article',
                    $data['id']
                ]);
                
                $documentId = $data['id'];
            } else {
                // Создание нового документа
                $sql = "INSERT INTO documents 
                        (title, slug, content, content_json, excerpt, featured_image_id, 
                         author_id, status, document_type, published_at, owner) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    $data['title'],
                    $data['slug'],
                    $data['content'],
                    isset($data['content_json']) ? json_encode($data['content_json']) : null,
                    $data['excerpt'],
                    $data['featured_image_id'] ?? null,
                    $data['author_id'] ?? null,
                    $data['status'] ?? 'draft',
                    $data['document_type'] ?? 'article',
                    $data['status'] == 'published' ? date('Y-m-d H:i:s') : null,
                    $data['owner'] ?? 'admin@konektem.net'
                ]);
                
                $documentId = $this->pdo->lastInsertId();
            }
            
            // Обновляем категории
            if (isset($data['categories'])) {
                $this->updateDocumentCategories($documentId, $data['categories']);
            }
            
            // Обновляем изображения
            if (isset($data['images'])) {
                $this->updateDocumentImages($documentId, $data['images']);
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'document_id' => $documentId,
                'message' => isset($data['id']) ? 'Document updated' : 'Document created'
            ];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("DocumentModel Save Error: " . $e->getMessage());
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    private function getDocumentCategories($documentId) {
        $sql = "SELECT c.* FROM document_categories c
                JOIN document_category_relations r ON c.id = r.category_id
                WHERE r.document_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$documentId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getDocumentImages($documentId) {
        $sql = "SELECT i.*, di.order_no 
                FROM Images i
                JOIN document_images di ON i.id = di.image_id
                WHERE di.document_id = ?
                ORDER BY di.order_no";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$documentId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function updateDocumentCategories($documentId, $categories) {
        // Удаляем старые связи
        $sql = "DELETE FROM document_category_relations WHERE document_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$documentId]);
        
        // Добавляем новые связи
        if (!empty($categories)) {
            $sql = "INSERT INTO document_category_relations (document_id, category_id) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($categories as $categoryId) {
                $stmt->execute([$documentId, $categoryId]);
            }
        }
    }
    
    private function updateDocumentImages($documentId, $images) {
        // Удаляем старые связи
        $sql = "DELETE FROM document_images WHERE document_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$documentId]);
        
        // Добавляем новые связи
        if (!empty($images)) {
            $sql = "INSERT INTO document_images (document_id, image_id, order_no) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            $order = 0;
            foreach ($images as $imageId) {
                $stmt->execute([$documentId, $imageId, $order++]);
            }
        }
    }
    
    public function deleteDocument($id) {
        try {
            $sql = "DELETE FROM documents WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$id]);
            
            return [
                'success' => $result,
                'message' => $result ? 'Document deleted' : 'Document not found'
            ];
            
        } catch (PDOException $e) {
            error_log("DocumentModel Delete Error: " . $e->getMessage());
            return ['error' => 'Database error'];
        }
    }
    
    public function uploadImage($file, $documentId = null) {
        try {
            // Здесь должна быть логика загрузки файла
            // Возвращаем ID созданной записи в таблице Images
            // Для простоты предположим, что файл уже загружен
            
            return [
                'success' => true,
                'image_id' => 1, // ID созданной записи
                'message' => 'Image uploaded'
            ];
            
        } catch (Exception $e) {
            error_log("DocumentModel Upload Error: " . $e->getMessage());
            return ['error' => 'Upload error'];
        }
    }
}
?>