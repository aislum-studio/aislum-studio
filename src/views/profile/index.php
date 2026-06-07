<?php
/**
 * Profile View
 * 
 * Displays user profile information and settings
 */

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Aislum Studio</title>
    
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
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .profile-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .profile-header {
            margin-bottom: 2rem;
        }
        
        .profile-header h1 {
            margin: 0;
            color: #333;
        }
        
        .profile-info {
            margin: 1.5rem 0;
        }
        
        .profile-info p {
            margin: 0.5rem 0;
            color: #666;
        }
        
        .profile-info strong {
            color: #333;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #667eea;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
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
        <a href="?page=dashboard" class="back-link">← Back to Dashboard</a>
        
        <div class="profile-card">
            <div class="profile-header">
                <h1>Your Profile</h1>
            </div>
            
            <div class="profile-info">
                <p>
                    <strong>Full Name:</strong><br>
                    <?php echo sanitize($currentUser['full_name']); ?>
                </p>
            </div>
            
            <div class="profile-info">
                <p>
                    <strong>Username:</strong><br>
                    <?php echo sanitize($currentUser['username']); ?>
                </p>
            </div>
            
            <div class="profile-info">
                <p>
                    <strong>Email:</strong><br>
                    <?php echo sanitize($currentUser['email']); ?>
                </p>
            </div>
            
            <div class="profile-info">
                <p>
                    <strong>Role:</strong><br>
                    <?php echo ucfirst($currentUser['role']); ?>
                </p>
            </div>
            
            <div class="profile-info">
                <p>
                    <strong>Member Since:</strong><br>
                    <?php echo formatDate($currentUser['created_at']); ?>
                </p>
            </div>
            
            <hr>
            
            <p style="color: #999; font-size: 0.9rem;">
                Profile editing features coming soon!
            </p>
        </div>
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
