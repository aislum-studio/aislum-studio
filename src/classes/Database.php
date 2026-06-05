<?php
/**
 * Database Class
 * 
 * Handles database connection and operations using PDO
 * Implements singleton pattern for single database instance
 */

class Database {
    private static $instance = null;
    private $pdo = null;
    
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Get singleton instance of Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        try {
            if (DB_TYPE === 'sqlite') {
                $this->pdo = new PDO('sqlite:' . SQLITE_PATH);
                // Enable foreign keys for SQLite
                $this->pdo->exec('PRAGMA foreign_keys = ON');
            } elseif (DB_TYPE === 'mysql') {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            }
            
            // Set error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get PDO instance
     */
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Execute a prepared statement
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return PDOStatement
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Fetch single row
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return array|null
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Fetch all rows
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Insert a record
     * 
     * @param string $table Table name
     * @param array $data Column => value pairs
     * @return int Last inserted ID
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->execute($sql, array_values($data));
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Update a record
     * 
     * @param string $table Table name
     * @param array $data Column => value pairs
     * @param string $where WHERE clause
     * @param array $whereParams WHERE clause parameters
     * @return int Number of affected rows
     */
    public function update($table, $data, $where, $whereParams = []) {
        $set = implode(', ', array_map(function($key) {
            return "$key = ?";
        }, array_keys($data)));
        
        $sql = "UPDATE $table SET $set WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Delete a record
     * 
     * @param string $table Table name
     * @param string $where WHERE clause
     * @param array $whereParams WHERE clause parameters
     * @return int Number of affected rows
     */
    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->execute($sql, $whereParams);
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        $this->pdo->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        $this->pdo->rollBack();
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
