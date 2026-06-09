<?php
/**
 * Dashboard View
 * 
 * Displays the main dashboard for authenticated users
 */

// Get current user
$currentUser = getCurrentUser();
$userId = getCurrentUserId();
$db = Database::getInstance();

// Get user statistics
$documentCount = $db->fetchOne(
    'SELECT COUNT(*) as count FROM documents WHERE user_id = ?',
    [$userId]
);
$designCount = $db->fetchOne(
    'SELECT COUNT(*) as count FROM designs WHERE user_id = ?',
    [$userId]
);

// Get recent activity
$recentActivity = $db->fetchAll(
    'SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 5',
    [$userId]
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aislum Studio</title>
    
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
        
        .dashboard-header {
            margin-bottom: 3rem;
        }
        
        .dashboard-header h1 {
            margin: 0;
            color: #333;
        }
        
        .dashboard-header p {
            margin: 0.5rem 0 0 0;
            color: #666;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #667eea;
            font-size: 2rem;
        }
        
        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .action-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        
        .action-card h3 {
            margin: 0 0 1rem 0;
            color: #333;
        }
        
        .action-card p {
            margin: 0 0 1rem 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .action-card a {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
        }
        
        .action-card a:hover {
            background-color: #764ba2;
        }
        
        .activity-section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        
        .activity-section h2 {
            margin: 0 0 1.5rem 0;
            color: #333;
        }
        
        .activity-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-item strong {
            color: #333;
        }
        
        .activity-item small {
            display: block;
            color: #999;
            margin-top: 0.25rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #999;
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
        <div class="dashboard-header">
            <h1>Welcome, <?php echo sanitize($currentUser['full_name']); ?>!</h1>
            <p>Your Aislum Studio Dashboard</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $documentCount['count']; ?></h3>
                <p>Documents</p>
            </div>
            
            <div class="stat-card">
                <h3><?php echo $designCount['count']; ?></h3>
                <p>Designs</p>
            </div>
            
            <div class="stat-card">
                <h3><?php echo date('M d'); ?></h3>
                <p>Today's Date</p>
            </div>
        </div>
        
        <!-- Quick Action Cards -->
        <div class="action-grid">
            <div class="action-card">
                <h3>📄 My Documents</h3>
                <p>Manage and organize your office documents, invoices, and reports.</p>
                <a href="?page=documents">View Documents</a>
            </div>
            
            <div class="action-card">
                <h3>🎨 My Designs</h3>
                <p>Create and edit business cards, flyers, and other designs.</p>
                <a href="?page=designs">View Designs</a>
            </div>
            
            <div class="action-card">
                <h3>➕ Quick Actions</h3>
                <p>Start a new project or upload files to your workspace.</p>
                <a href="?page=documents">Upload Document</a>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="activity-section">
            <h2>Recent Activity</h2>
            
            <?php if (!empty($recentActivity)): ?>
                <ul class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                        <li class="activity-item">
                            <strong><?php echo ucfirst($activity['action']); ?></strong> - 
                            <?php echo ucfirst($activity['entity_type']); ?>
                            <small><?php echo getTimeAgo($activity['created_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <p>No recent activity. Start by uploading a document or creating a design!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
