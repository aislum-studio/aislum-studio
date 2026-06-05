<?php
/**
 * Database Initialization Script
 * Run this script to initialize the SQLite database with the schema
 * 
 * Usage: php database/init.php
 */

require_once __DIR__ . '/../config/config.php';

function initializeDatabase() {
    try {
        if (DB_TYPE === 'sqlite') {
            // Create SQLite database
            $db_path = SQLITE_PATH;
            
            // Check if database file exists
            if (!file_exists($db_path)) {
                // Create new SQLite database
                $pdo = new PDO('sqlite:' . $db_path);
                echo "SQLite database created at: $db_path\n";
            } else {
                $pdo = new PDO('sqlite:' . $db_path);
                echo "Connected to existing SQLite database.\n";
            }
            
            // Read and execute schema
            $schema = file_get_contents(__DIR__ . '/schema.sql');
            
            // Split schema into individual statements
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    // Convert MySQL-specific syntax to SQLite
                    $statement = convertMySQLToSQLite($statement);
                    $pdo->exec($statement);
                }
            }
            
            echo "Database schema initialized successfully!\n";
            
            // Create sample admin user
            createSampleUser($pdo);
            
            return true;
        } elseif (DB_TYPE === 'mysql') {
            // MySQL initialization would go here
            echo "MySQL initialization not yet implemented.\n";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error initializing database: " . $e->getMessage() . "\n";
        return false;
    }
}

function convertMySQLToSQLite($statement) {
    // Convert MySQL AUTO_INCREMENT to SQLite AUTOINCREMENT
    $statement = str_replace('AUTO_INCREMENT', 'AUTOINCREMENT', $statement);
    
    // Convert MySQL ENUM to SQLite TEXT with CHECK constraint
    // This is a simplified conversion
    $statement = preg_replace('/ENUM\([^)]+\)/', 'TEXT', $statement);
    
    // Remove ON UPDATE CURRENT_TIMESTAMP (not supported in SQLite)
    $statement = str_replace('ON UPDATE CURRENT_TIMESTAMP', '', $statement);
    
    // Convert TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    $statement = str_replace('TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $statement);
    
    // Remove FOREIGN KEY constraints temporarily (can be added later)
    // SQLite has limited foreign key support by default
    
    return $statement;
}

function createSampleUser($pdo) {
    try {
        $username = 'admin';
        $email = 'admin@aislumstudio.local';
        $password = 'admin123'; // Change this in production!
        
        // Check if admin user already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            echo "Admin user already exists.\n";
            return;
        }
        
        // Create admin user
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $pdo->prepare('
            INSERT INTO users (username, email, password_hash, full_name, role, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        
        $stmt->execute([
            $username,
            $email,
            $password_hash,
            'Administrator',
            'admin',
            1
        ]);
        
        echo "Sample admin user created:\n";
        echo "  Username: $username\n";
        echo "  Email: $email\n";
        echo "  Password: $password\n";
        echo "  ⚠️  IMPORTANT: Change this password immediately in production!\n";
        
    } catch (PDOException $e) {
        echo "Error creating sample user: " . $e->getMessage() . "\n";
    }
}

// Run initialization
if (php_sapi_name() === 'cli') {
    if (initializeDatabase()) {
        exit(0);
    } else {
        exit(1);
    }
} else {
    echo "This script should be run from the command line.\n";
}
