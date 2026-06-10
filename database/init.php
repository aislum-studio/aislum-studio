<?php
/**
 * Database Initialization Script
 * 
 * Usage:
 *   php database/init.php                         # auto-generates a random admin password
 *   php database/init.php --admin-password=mypass # set your own password
 * 
 * Run once during initial setup. Safe to re-run — skips admin creation if user already exists.
 */

require_once __DIR__ . '/../config/config.php';

function initializeDatabase() {
    try {
        if (DB_TYPE === 'sqlite') {
            $db_path = SQLITE_PATH;

            if (!file_exists($db_path)) {
                $pdo = new PDO('sqlite:' . $db_path);
                echo "SQLite database created at: $db_path\n";
            } else {
                $pdo = new PDO('sqlite:' . $db_path);
                echo "Connected to existing SQLite database.\n";
            }

            $schema     = file_get_contents(__DIR__ . '/schema.sql');
            $statements = array_filter(array_map('trim', explode(';', $schema)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $statement = convertMySQLToSQLite($statement);
                    $pdo->exec($statement);
                }
            }

            echo "Database schema initialized successfully!\n";

            // FIX: Pass password from CLI argument or generate a secure random one
            $adminPassword = resolveAdminPassword();
            createAdminUser($pdo, $adminPassword);

            return true;

        } elseif (DB_TYPE === 'mysql') {
            echo "MySQL initialization not yet implemented.\n";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error initializing database: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * FIX: Resolve admin password from CLI argument or generate a secure random one.
 * Never falls back to a hardcoded default.
 */
function resolveAdminPassword() {
    global $argv;

    // Accept --admin-password=somevalue from CLI
    foreach (($argv ?? []) as $arg) {
        if (strpos($arg, '--admin-password=') === 0) {
            $password = substr($arg, strlen('--admin-password='));
            if (strlen($password) < 8) {
                echo "Error: --admin-password must be at least 8 characters.\n";
                exit(1);
            }
            return $password;
        }
    }

    // No argument provided — generate a cryptographically secure random password
    return bin2hex(random_bytes(12)); // 24-char hex string
}

function convertMySQLToSQLite($statement) {
    $statement = str_replace('AUTO_INCREMENT', 'AUTOINCREMENT', $statement);
    $statement = preg_replace('/ENUM\([^)]+\)/', 'TEXT', $statement);
    $statement = str_replace('ON UPDATE CURRENT_TIMESTAMP', '', $statement);
    $statement = str_replace('TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $statement);
    return $statement;
}

function createAdminUser($pdo, $password) {
    try {
        $username = 'admin';
        $email    = 'admin@aislumstudio.local';

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            echo "Admin user already exists — skipping creation.\n";
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare('
            INSERT INTO users (username, email, password_hash, full_name, role, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([$username, $email, $passwordHash, 'Administrator', 'admin', 1]);

        // FIX: Print password ONCE here, then it's gone — store it immediately
        echo "\n✅ Admin user created:\n";
        echo "   Username : $username\n";
        echo "   Email    : $email\n";
        echo "   Password : $password\n";
        echo "\n⚠️  SAVE THIS PASSWORD NOW — it will not be shown again.\n";
        echo "   Change it after first login via the Profile page.\n\n";

    } catch (PDOException $e) {
        echo "Error creating admin user: " . $e->getMessage() . "\n";
    }
}

if (php_sapi_name() === 'cli') {
    if (initializeDatabase()) {
        exit(0);
    } else {
        exit(1);
    }
} else {
    // FIX: Block web access entirely — this script must never run via HTTP
    http_response_code(403);
    echo "This script must be run from the command line only.";
    exit(1);
}
