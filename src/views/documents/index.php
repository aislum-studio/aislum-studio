<?php
/**
 * Documents View
 * 
 * Displays document management interface with upload and list functionality
 */

require_once __DIR__ . '/../../classes/Document.php';

$currentUser = getCurrentUser();
$userId = getCurrentUserId();

// Get documents and categories
$documents = Document::getByUser($userId);
$categories = Document::getCategories($userId);
$selectedCategory = isset($_GET['category']) ? sanitize($_GET['category']) : null;

// Filter documents by category if selected
if ($selectedCategory) {
    $documents = array_filter($documents, function($doc) use ($selectedCategory) {
        return $doc['category'] === $selectedCategory;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Aislum Studio</title>
    
    <!-- PICO CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/custom.css">
    
    <style>
        nav {
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 0;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        .nav-links a:hover {
            color: #667eea;
        }
        
        .nav-user {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-user a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        .nav-user a:hover {
            color: #667eea;
        }
        
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            margin: 0;
            color: #333;
        }
        
        .btn-upload {
            background-color: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-upload:hover {
            background-color: #764ba2;
        }
        
        .content-wrapper {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
        }
        
        .sidebar {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            height: fit-content;
        }
        
        .sidebar h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1rem;
        }
        
        .category-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .category-list li {
            margin-bottom: 0.5rem;
        }
        
        .category-list a {
            display: block;
            padding: 0.5rem;
            color: #333;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background-color 0.3s ease;
        }
        
        .category-list a:hover,
        .category-list a.active {
            background-color: #667eea;
            color: white;
        }
        
        .main-content {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        
        .upload-section {
            background: #f9f9f9;
            border: 2px dashed #667eea;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .upload-section:hover {
            background-color: #f0f0f0;
        }
        
        .upload-section input[type="file"] {
            display: none;
        }
        
        .upload-section p {
            margin: 0.5rem 0;
            color: #666;
        }
        
        .upload-form {
            display: none;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .upload-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
        }
        
        .form-actions button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: 500;
        }
        
        .btn-submit {
            background-color: #667eea;
            color: white;
        }
        
        .btn-submit:hover {
            background-color: #764ba2;
        }
        
        .btn-cancel {
            background-color: #ddd;
            color: #333;
        }
        
        .btn-cancel:hover {
            background-color: #ccc;
        }
        
        .documents-list {
            margin-top: 2rem;
        }
        
        .documents-list h2 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.2rem;
        }
        
        .document-item {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.3s ease;
        }
        
        .document-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .document-info {
            flex: 1;
        }
        
        .document-title {
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .document-meta {
            font-size: 0.9rem;
            color: #666;
            margin: 0.5rem 0 0 0;
        }
        
        .document-category {
            display: inline-block;
            background-color: #e8eaf6;
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        
        .document-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-download {
            background-color: #48bb78;
            color: white;
        }
        
        .btn-download:hover {
            background-color: #38a169;
        }
        
        .btn-delete {
            background-color: #f56565;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #e53e3e;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #999;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            border-color: #48bb78;
            color: #22543d;
        }
        
        .alert-error {
            background-color: #fed7d7;
            border-color: #f56565;
            color: #742a2a;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .document-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .document-actions {
                width: 100%;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="?page=dashboard" class="nav-brand">Aislum Studio</a>
            
            <ul class="nav-links">
                <li><a href="?page=dashboard">Dashboard</a></li>
                <li><a href="?page=documents">Documents</a></li>
                <li><a href="?page=designs">Designs</a></li>
            </ul>
            
            <div class="nav-user">
                <span><?php echo sanitize($currentUser['username']); ?></span>
                <a href="?page=profile">Profile</a>
                <a href="?page=auth&action=logout">Logout</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        <div class="page-header">
            <h1>📄 My Documents</h1>
            <button class="btn-upload" onclick="toggleUploadForm()">+ Upload Document</button>
        </div>
        
        <!-- Flash Messages -->
        <?php
        $uploadSuccess = getFlashMessage('upload_success');
        $uploadError = getFlashMessage('upload_error');
        $deleteSuccess = getFlashMessage('delete_success');
        $deleteError = getFlashMessage('delete_error');
        
        if ($uploadSuccess) {
            echo '<div class="alert alert-success">' . $uploadSuccess['message'] . '</div>';
        }
        if ($uploadError) {
            echo '<div class="alert alert-error">' . $uploadError['message'] . '</div>';
        }
        if ($deleteSuccess) {
            echo '<div class="alert alert-success">' . $deleteSuccess['message'] . '</div>';
        }
        if ($deleteError) {
            echo '<div class="alert alert-error">' . $deleteError['message'] . '</div>';
        }
        ?>
        
        <div class="content-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h3>Categories</h3>
                <ul class="category-list">
                    <li>
                        <a href="?page=documents" <?php echo !$selectedCategory ? 'class="active"' : ''; ?>>
                            All Documents
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="?page=documents&category=<?php echo urlencode($category); ?>" 
                               <?php echo $selectedCategory === $category ? 'class="active"' : ''; ?>>
                                <?php echo sanitize($category); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Upload Form -->
                <div class="upload-form" id="uploadForm">
                    <h3>Upload New Document</h3>
                    <form action="?page=documents&action=upload" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Document Title *</label>
                            <input type="text" id="title" name="title" placeholder="Enter document title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" placeholder="Enter document description"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="Invoices">Invoices</option>
                                <option value="Contracts">Contracts</option>
                                <option value="Reports">Reports</option>
                                <option value="Proposals">Proposals</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="document">Select File *</label>
                            <input type="file" id="document" name="document" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                            <small>Allowed: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG, GIF, ZIP, RAR (Max 50MB)</small>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-submit">Upload Document</button>
                            <button type="button" class="btn-cancel" onclick="toggleUploadForm()">Cancel</button>
                        </div>
                    </form>
                </div>
                
                <!-- Documents List -->
                <div class="documents-list">
                    <h2>Documents (<?php echo count($documents); ?>)</h2>
                    
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="document-item">
                                <div class="document-info">
                                    <p class="document-title"><?php echo sanitize($doc['title']); ?></p>
                                    <p class="document-meta">
                                        <span class="document-category"><?php echo sanitize($doc['category']); ?></span>
                                        <span>Uploaded: <?php echo formatDate($doc['created_at']); ?></span>
                                    </p>
                                    <?php if (!empty($doc['description'])): ?>
                                        <p class="document-meta"><?php echo sanitize($doc['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="document-actions">
                                    <a href="/uploads/<?php echo $doc['file_path']; ?>" class="btn-small btn-download" download>Download</a>
                                    <form action="?page=documents&action=delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                        <button type="submit" class="btn-small btn-delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>📁 No documents found. Start by uploading your first document!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        function toggleUploadForm() {
            $('#uploadForm').toggleClass('active');
        }
    </script>
</body>
</html>
