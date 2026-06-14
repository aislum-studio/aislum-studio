<?php
/**
 * Card Schema Migration
 *
 * Adds the business card tables to the existing Aislum Studio database.
 * Safe to run multiple times — uses CREATE TABLE IF NOT EXISTS.
 *
 * Usage:  php database/migrate_cards.php
 */

require_once __DIR__ . '/../config/config.php';

function runCardMigration() {
    try {
        if (DB_TYPE === 'sqlite') {
            $pdo = new PDO('sqlite:' . SQLITE_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec('PRAGMA foreign_keys = ON');
            echo "Connected to SQLite database.\n";
        } elseif (DB_TYPE === 'mysql') {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected to MySQL database.\n";
        } else {
            echo "Unknown DB_TYPE.\n";
            exit(1);
        }

        $sql        = file_get_contents(__DIR__ . '/card_schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $stmt) {
            // Skip comment-only blocks
            if (empty($stmt) || strpos(ltrim($stmt), '--') === 0) {
                continue;
            }

            if (DB_TYPE === 'sqlite') {
                $stmt = convertToSQLite($stmt);
            }

            $pdo->exec($stmt);
        }

        echo "Card schema migration completed successfully.\n";
        echo "Tables created (if not already present):\n";
        echo "  - card_templates\n";
        echo "  - card_designs\n";
        return true;

    } catch (PDOException $e) {
        echo "Migration error: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Convert MySQL-specific syntax to SQLite equivalents.
 * (Mirrors the logic in database/init.php)
 */
function convertToSQLite($sql) {
    $sql = str_replace('AUTO_INCREMENT', 'AUTOINCREMENT', $sql);
    $sql = preg_replace('/ENUM\([^)]+\)/', 'TEXT', $sql);
    $sql = str_replace('ON UPDATE CURRENT_TIMESTAMP', '', $sql);
    $sql = str_replace('TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $sql);

    // SQLite does not enforce FK ON DELETE SET NULL the same way;
    // behaviour is correct once PRAGMA foreign_keys = ON is set.
    return $sql;
}

if (php_sapi_name() === 'cli') {
    runCardMigration() ? exit(0) : exit(1);
} else {
    http_response_code(403);
    echo "Run this script from the command line only.";
    exit(1);
}
