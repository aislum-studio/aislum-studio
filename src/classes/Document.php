<?php
/**
 * Document Class
 *
 * Handles all document-related operations including upload, retrieval, and deletion.
 *
 * FIX: Removed private static logActivity() — now calls the shared logActivity()
 *      from src/helpers/functions.php instead.
 */

class Document {
    private static $db          = null;
    private static $allowedExts = ['pdf','doc','docx','xls','xlsx','ppt','pptx','txt','jpg','jpeg','png','gif','zip','rar'];
    private static $maxFileSize = 52428800; // 50 MB
    private static $uploadDir   = __DIR__ . '/../../public/uploads/documents/';

    private static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * Upload a document.
     */
    public static function upload($userId, $title, $description, $category, $file) {
        self::init();

        if (empty($title) || empty($category)) {
            return ['success' => false, 'message' => 'Title and category are required'];
        }

        $validation = self::validateFile($file);
        if (!$validation['success']) {
            return $validation;
        }

        if (!is_dir(self::$uploadDir)) {
            mkdir(self::$uploadDir, 0755, true);
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName      = uniqid('doc_') . '_' . time() . '.' . $fileExtension;
        $filePath      = self::$uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'message' => 'Failed to upload file'];
        }

        try {
            self::$db->execute(
                'INSERT INTO documents (user_id, title, description, file_path, file_type, category, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
                [$userId, $title, $description, 'documents/' . $fileName, $fileExtension, $category]
            );

            $newId = self::$db->lastInsertId();

            // FIX: Use shared logActivity() from functions.php
            logActivity($userId, 'upload', 'document', $newId, "Uploaded document: $title");

            return ['success' => true, 'message' => 'Document uploaded successfully', 'file_id' => $newId];

        } catch (Exception $e) {
            // Clean up the uploaded file if the DB insert fails
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Validate an uploaded file.
     */
    private static function validateFile($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error occurred'];
        }
        if ($file['size'] > self::$maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds maximum limit of 50MB'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedExts)) {
            return ['success' => false, 'message' => 'File type not allowed. Allowed: ' . implode(', ', self::$allowedExts)];
        }

        return ['success' => true];
    }

    /**
     * Get all documents for a user, optionally filtered by category.
     */
    public static function getByUser($userId, $category = null) {
        self::init();

        if ($category) {
            return self::$db->fetchAll(
                'SELECT * FROM documents WHERE user_id = ? AND category = ? ORDER BY created_at DESC',
                [$userId, $category]
            );
        }

        return self::$db->fetchAll(
            'SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    /**
     * Get a specific document (ownership-checked).
     */
    public static function getById($documentId, $userId) {
        self::init();
        return self::$db->fetchOne(
            'SELECT * FROM documents WHERE id = ? AND user_id = ?',
            [$documentId, $userId]
        );
    }

    /**
     * Update document metadata.
     */
    public static function update($documentId, $userId, $title, $description, $category) {
        self::init();

        $document = self::getById($documentId, $userId);
        if (!$document) {
            return ['success' => false, 'message' => 'Document not found or unauthorized'];
        }

        try {
            self::$db->execute(
                'UPDATE documents SET title = ?, description = ?, category = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ? AND user_id = ?',
                [$title, $description, $category, $documentId, $userId]
            );

            // FIX: Use shared logActivity()
            logActivity($userId, 'update', 'document', $documentId, "Updated document: $title");

            return ['success' => true, 'message' => 'Document updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a document (removes file + DB record).
     */
    public static function delete($documentId, $userId) {
        self::init();

        $document = self::getById($documentId, $userId);
        if (!$document) {
            return ['success' => false, 'message' => 'Document not found or unauthorized'];
        }

        try {
            $filePath = __DIR__ . '/../../public/uploads/' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            self::$db->execute(
                'DELETE FROM documents WHERE id = ? AND user_id = ?',
                [$documentId, $userId]
            );

            // FIX: Use shared logActivity()
            logActivity($userId, 'delete', 'document', $documentId, 'Deleted document: ' . $document['title']);

            return ['success' => true, 'message' => 'Document deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get distinct categories for a user.
     */
    public static function getCategories($userId) {
        self::init();
        $rows = self::$db->fetchAll(
            'SELECT DISTINCT category FROM documents WHERE user_id = ? ORDER BY category ASC',
            [$userId]
        );
        return array_column($rows, 'category');
    }

    /**
     * Search documents by title or description.
     */
    public static function search($userId, $query) {
        self::init();
        $like = '%' . $query . '%';
        return self::$db->fetchAll(
            'SELECT * FROM documents WHERE user_id = ? AND (title LIKE ? OR description LIKE ?) ORDER BY created_at DESC',
            [$userId, $like, $like]
        );
    }
}
