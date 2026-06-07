<?php
/**
 * Designs View - Placeholder
 * 
 * This is a placeholder for the designs management page
 */

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designs - Aislum Studio</title>
    
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
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            margin: 0;
            color: #333;
        }
        
        .placeholder-content {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 3rem;
            text-align: center;
            color: #999;
        }
        
        .placeholder-content h2 {
            color: #666;
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
            <h1>My Designs</h1>
        </div>
        
        <div class="placeholder-content">
            <h2>🎨 Design Studio</h2>
            <p>Design tools are coming soon!</p>
            <p>You'll be able to create and edit business cards, flyers, and other designs here.</p>
        </div>
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
