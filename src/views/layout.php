<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Aislum Studio</title>
    
    <!-- PICO CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/custom.css">
    
    <style>
        :root {
            --form-element-valid-border-color: #48bb78;
            --form-element-invalid-border-color: #f56565;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        nav {
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 0;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }
        
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        nav a:hover {
            color: #0066cc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0066cc;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-user {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .flash-messages {
            margin: 1rem 0;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            border: 1px solid #9ae6b4;
            color: #22543d;
        }
        
        .alert-error {
            background-color: #fed7d7;
            border: 1px solid #fc8181;
            color: #742a2a;
        }
        
        .alert-warning {
            background-color: #feebc8;
            border: 1px solid #fbd38d;
            color: #7c2d12;
        }
        
        .alert-info {
            background-color: #bee3f8;
            border: 1px solid #90cdf4;
            color: #2c5282;
        }
        
        main {
            min-height: calc(100vh - 200px);
            padding: 2rem 0;
        }
        
        footer {
            background-color: #fff;
            border-top: 1px solid #e0e0e0;
            padding: 2rem 0;
            text-align: center;
            color: #666;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <div class="nav-container">
                <div class="nav-brand">
                    <a href="?page=dashboard">Aislum Studio</a>
                </div>
                
                <?php if (Auth::isLoggedIn()): ?>
                    <div class="nav-links">
                        <a href="?page=dashboard">Dashboard</a>
                        <a href="?page=documents">Documents</a>
                        <a href="?page=designs">Designs</a>
                    </div>
                    
                    <div class="nav-user">
                        <span><?php echo sanitize($_SESSION['username']); ?></span>
                        <a href="?page=profile">Profile</a>
                        <a href="?page=auth&action=logout">Logout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Flash Messages -->
            <?php 
            $messages = getAllFlashMessages();
            if (!empty($messages)):
            ?>
                <div class="flash-messages">
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?php echo $message['type']; ?>">
                            <?php echo $message['message']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <?php echo isset($content) ? $content : ''; ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Aislum Studio. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="/js/app.js"></script>
</body>
</html>
