<?php
/**
 * Document API Endpoints
 * 
 * Handles document upload, retrieval, update, and deletion
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Document.php';
require_once __DIR__ . '/../helpers/functions.php';

session_start();

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$method = $_SERVER['REQUEST_METHOD'];
$userId = getCurrentUserId();

// Handle different document actions
switch ($action) {
    case 'upload':
        if ($method === 'POST') {
            handleUpload($userId);
        }
        break;
    
    case 'list':
        if ($method === 'GET') {
            handleList($userId);
        }
        break;
    
    case 'get':
        if ($method === 'GET') {
            handleGet($userId);
        }
        break;
    
    case 'update':
        if ($method === 'POST') {
            handleUpdate($userId);
        }
        break;
    
    case 'delete':
        if ($method === 'POST') {
            handleDelete($userId);
        }
        break;
    
    case 'search':
        if ($method === 'GET') {
            handleSearch($userId);
        }
        break;
    
    case 'categories':
        if ($method === 'GET') {
            handleGetCategories($userId);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle document upload
 */
function handleUpload($userId) {
    // Check if file was uploaded
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        setFlashMessage('upload_error', 'No file uploaded or upload error occurred', 'error');
        redirect('?page=documents');
    }
    
    // Get form data
    $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    
    // Upload document
    $result = Document::upload($userId, $title, $description, $category, $_FILES['document']);
    
    if ($result['success']) {
        setFlashMessage('upload_success', $result['message'], 'success');
    } else {
        setFlashMessage('upload_error', $result['message'], 'error');
    }
    
    redirect('?page=documents');
}

/**
 * Handle document list retrieval
 */
function handleList($userId) {
    $category = isset($_GET['category']) ? sanitize($_GET['category']) : null;
    
    $documents = Document::getByUser($userId, $category);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'documents' => $documents]);
}

/**
 * Handle single document retrieval
 */
function handleGet($userId) {
    $documentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($documentId === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid document ID']);
        return;
    }
    
    $document = Document::getById($documentId, $userId);
    
    if (!$document) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Document not found']);
        return;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'document' => $document]);
}

/**
 * Handle document update
 */
function handleUpdate($userId) {
    $documentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    
    if ($documentId === 0) {
        setFlashMessage('update_error', 'Invalid document ID', 'error');
        redirect('?page=documents');
    }
    
    $result = Document::update($documentId, $userId, $title, $description, $category);
    
    if ($result['success']) {
        setFlashMessage('update_success', $result['message'], 'success');
    } else {
        setFlashMessage('update_error', $result['message'], 'error');
    }
    
    redirect('?page=documents');
}

/**
 * Handle document deletion
 */
function handleDelete($userId) {
    $documentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($documentId === 0) {
        setFlashMessage('delete_error', 'Invalid document ID', 'error');
        redirect('?page=documents');
    }
    
    $result = Document::delete($documentId, $userId);
    
    if ($result['success']) {
        setFlashMessage('delete_success', $result['message'], 'success');
    } else {
        setFlashMessage('delete_error', $result['message'], 'error');
    }
    
    redirect('?page=documents');
}

/**
 * Handle document search
 */
function handleSearch($userId) {
    $query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
    
    if (empty($query)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Search query required']);
        return;
    }
    
    $documents = Document::search($userId, $query);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'documents' => $documents]);
}

/**
 * Handle category retrieval
 */
function handleGetCategories($userId) {
    $categories = Document::getCategories($userId);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'categories' => $categories]);
}
