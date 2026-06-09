<?php
/**
 * Document Class
 * 
 * Handles all document-related operations including upload, retrieval, and deletion
 */

class Document {
    private static $db = null;
    private static $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
    private static $maxFileSize = 52428800; // 50MB in bytes
    private static $uploadDir = __DIR__ . '/../../public/uploads/documents/';
    
    /**
     * Initialize database connection
     */
    private static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
    }
    
    /**
     * Upload a document
     * 
     * @param int $userId User ID
     * @param string $title Document title
     * @param string $description Document description
     * @param string $category Document category
     * @param array $file File from $_FILES
     * 
     * @return array Result array with success status and message
     */
    public static function upload($userId, $title, $description, $category, $file) {
        self::init();
        
        // Validate inputs
        if (empty($title) || empty($category)) {
            return ['success' => false, 'message' => 'Title and category are required'];
        }
        
        // Validate file
        $fileValidation = self::validateFile($file);
        if (!$fileValidation['success']) {
            return $fileValidation;
        }
        
        // Create upload directory if it doesn't exist
        if (!is_dir(self::$uploadDir)) {
            mkdir(self::$uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('doc_') . '_' . time() . '.' . $fileExtension;
        $filePath = self::$uploadDir . $fileName;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'message' => 'Failed to upload file'];
        }
        
        // Save to database
        try {
            $result = self::$db->execute(
                'INSERT INTO documents (user_id, title, description, file_path, file_type, category, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
                [$userId, $title, $description, 'documents/' . $fileName, $fileExtension, $category]
            );
            
            if ($result) {
                // Log activity
                self::logActivity($userId, 'upload', 'document', self::$db->lastInsertId(), "Uploaded document: $title");
                
                return ['success' => true, 'message' => 'Document uploaded successfully', 'file_id' => self::$db->lastInsertId()];
            } else {
                // Delete uploaded file if database insert fails
                unlink($filePath);
                return ['success' => false, 'message' => 'Failed to save document to database'];
            }
        } catch (Exception $e) {
            // Delete uploaded file if error occurs
            unlink($filePath);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate uploaded file
     * 
     * @param array $file File from $_FILES
     * 
     * @return array Validation result
     */
    private static function validateFile($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error occurred'];
        }
        
        if ($file['size'] > self::$maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds maximum limit of 50MB'];
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, self::$allowedExtensions)) {
            return ['success' => false, 'message' => 'File type not allowed. Allowed types: ' . implode(', ', self::$allowedExtensions)];
        }
        
        return ['success' => true];
    }
    
    /**
     * Get all documents for a user
     * 
     * @param int $userId User ID
     * @param string $category Optional category filter
     * 
     * @return array Array of documents
     */
    public static function getByUser($userId, $category = null) {
        self::init();
        
        if ($category) {
            $documents = self::$db->fetchAll(
                'SELECT * FROM documents WHERE user_id = ? AND category = ? ORDER BY created_at DESC',
                [$userId, $category]
            );
        } else {
            $documents = self::$db->fetchAll(
                'SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC',
                [$userId]
            );
        }
        
        return $documents;
    }
    
    /**
     * Get a specific document
     * 
     * @param int $documentId Document ID
     * @param int $userId User ID (for authorization)
     * 
     * @return array|null Document data or null if not found
     */
    public static function getById($documentId, $userId) {
        self::init();
        
        $document = self::$db->fetchOne(
            'SELECT * FROM documents WHERE id = ? AND user_id = ?',
            [$documentId, $userId]
        );
        
        return $document;
    }
    
    /**
     * Update document metadata
     * 
     * @param int $documentId Document ID
     * @param int $userId User ID (for authorization)
     * @param string $title New title
     * @param string $description New description
     * @param string $category New category
     * 
     * @return array Result array
     */
    public static function update($documentId, $userId, $title, $description, $category) {
        self::init();
        
        // Check authorization
        $document = self::getById($documentId, $userId);
        if (!$document) {
            return ['success' => false, 'message' => 'Document not found or unauthorized'];
        }
        
        try {
            $result = self::$db->execute(
                'UPDATE documents SET title = ?, description = ?, category = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?',
                [$title, $description, $category, $documentId, $userId]
            );
            
            if ($result) {
                self::logActivity($userId, 'update', 'document', $documentId, "Updated document: $title");
                return ['success' => true, 'message' => 'Document updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update document'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete a document
     * 
     * @param int $documentId Document ID
     * @param int $userId User ID (for authorization)
     * 
     * @return array Result array
     */
    public static function delete($documentId, $userId) {
        self::init();
        
        // Get document first
        $document = self::getById($documentId, $userId);
        if (!$document) {
            return ['success' => false, 'message' => 'Document not found or unauthorized'];
        }
        
        try {
            // Delete file from server
            $filePath = __DIR__ . '/../../public/uploads/' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete from database
            $result = self::$db->execute(
                'DELETE FROM documents WHERE id = ? AND user_id = ?',
                [$documentId, $userId]
            );
            
            if ($result) {
                self::logActivity($userId, 'delete', 'document', $documentId, "Deleted document: " . $document['title']);
                return ['success' => true, 'message' => 'Document deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete document'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get document categories
     * 
     * @param int $userId User ID
     * 
     * @return array Array of unique categories
     */
    public static function getCategories($userId) {
        self::init();
        
        $categories = self::$db->fetchAll(
            'SELECT DISTINCT category FROM documents WHERE user_id = ? ORDER BY category ASC',
            [$userId]
        );
        
        return array_column($categories, 'category');
    }
    
    /**
     * Search documents
     * 
     * @param int $userId User ID
     * @param string $query Search query
     * 
     * @return array Array of matching documents
     */
    public static function search($userId, $query) {
        self::init();
        
        $query = '%' . $query . '%';
        $documents = self::$db->fetchAll(
            'SELECT * FROM documents WHERE user_id = ? AND (title LIKE ? OR description LIKE ?) ORDER BY created_at DESC',
            [$userId, $query, $query]
        );
        
        return $documents;
    }
    
    /**
     * Log activity
     * 
     * @param int $userId User ID
     * @param string $action Action performed
     * @param string $entityType Entity type (document, design, etc.)
     * @param int $entityId Entity ID
     * @param string $details Additional details
     */
    private static function logActivity($userId, $action, $entityType, $entityId, $details) {
        self::$db->execute(
            'INSERT INTO activity_log (user_id, action, entity_type, entity_id, details, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)',
            [$userId, $action, $entityType, $entityId, $details]
        );
    }
}
