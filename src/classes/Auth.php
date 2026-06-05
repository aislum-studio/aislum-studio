<?php
/**
 * Auth Class
 * 
 * Handles user authentication and session management
 */

class Auth {
    private static $db = null;
    
    /**
     * Initialize database connection
     */
    private static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
    }
    
    /**
     * Register a new user
     * 
     * @param string $username Username
     * @param string $email Email address
     * @param string $password Password
     * @param string $fullName Full name
     * @return array Result array with 'success' and 'message' keys
     */
    public static function register($username, $email, $password, $fullName) {
        self::init();
        
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check if username already exists
        $existing = self::$db->fetchOne('SELECT id FROM users WHERE username = ?', [$username]);
        if ($existing) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email already exists
        $existing = self::$db->fetchOne('SELECT id FROM users WHERE email = ?', [$email]);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        try {
            // Insert new user
            $userId = self::$db->insert('users', [
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
                'full_name' => $fullName,
                'role' => 'user',
                'is_active' => 1
            ]);
            
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Login user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array Result array with 'success' and 'message' keys
     */
    public static function login($username, $password) {
        self::init();
        
        // Validate input
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }
        
        // Find user by username or email
        $user = self::$db->fetchOne(
            'SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1',
            [$username, $username]
        );
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        return ['success' => true, 'message' => 'Login successful'];
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check session timeout
     * 
     * @return bool
     */
    public static function checkSessionTimeout() {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            self::logout();
            return false;
        }
        
        // Update last activity time
        $_SESSION['login_time'] = time();
        return true;
    }
    
    /**
     * Change password
     * 
     * @param int $userId User ID
     * @param string $oldPassword Old password
     * @param string $newPassword New password
     * @return array Result array
     */
    public static function changePassword($userId, $oldPassword, $newPassword) {
        self::init();
        
        // Get user
        $user = self::$db->fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Verify old password
        if (!password_verify($oldPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Validate new password
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }
        
        // Hash new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        // Update password
        self::$db->update('users', ['password_hash' => $newPasswordHash], 'id = ?', [$userId]);
        
        return ['success' => true, 'message' => 'Password changed successfully'];
    }
    
    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data User data to update
     * @return array Result array
     */
    public static function updateProfile($userId, $data) {
        self::init();
        
        $allowedFields = ['full_name', 'email'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        try {
            self::$db->update('users', $updateData, 'id = ?', [$userId]);
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param int $userId User ID
     * @return array|null User data or null
     */
    public static function getUserById($userId) {
        self::init();
        return self::$db->fetchOne('SELECT id, username, email, full_name, role, is_active, created_at FROM users WHERE id = ?', [$userId]);
    }
}
