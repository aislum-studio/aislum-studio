<?php
/**
 * Auth Class
 *
 * Handles user authentication and session management.
 */

class Auth {
    private static $db = null;

    private static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * Register a new user.
     */
    public static function register($username, $email, $password, $fullName) {
        self::init();

        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        $existing = self::$db->fetchOne('SELECT id FROM users WHERE username = ?', [$username]);
        if ($existing) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        $existing = self::$db->fetchOne('SELECT id FROM users WHERE email = ?', [$email]);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);

        try {
            $userId = self::$db->insert('users', [
                'username'      => $username,
                'email'         => $email,
                'password_hash' => $passwordHash,
                'full_name'     => $fullName,
                'role'          => 'user',
                'is_active'     => 1,
            ]);
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Login user.
     */
    public static function login($username, $password) {
        self::init();

        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }

        $user = self::$db->fetchOne(
            'SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1',
            [$username, $username]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        $_SESSION['user_id']       = $user['id'];
        $_SESSION['username']      = $user['username'];
        $_SESSION['email']         = $user['email'];
        $_SESSION['role']          = $user['role'];

        // FIX: Store login time separately from last activity.
        // login_time never changes after login.
        // last_activity is updated on every request and used for idle timeout.
        $_SESSION['login_time']    = time();
        $_SESSION['last_activity'] = time();

        return ['success' => true, 'message' => 'Login successful'];
    }

    /**
     * Logout user.
     */
    public static function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /**
     * Check if user is logged in.
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * FIX: Check session timeout properly.
     *
     * Two independent checks:
     *   1. Absolute timeout  — session older than SESSION_TIMEOUT since login
     *   2. Idle timeout      — no activity for SESSION_IDLE_TIMEOUT seconds
     *
     * SESSION_IDLE_TIMEOUT defaults to half of SESSION_TIMEOUT if not defined.
     *
     * @return bool  true = session still valid, false = session expired
     */
    public static function checkSessionTimeout() {
        if (!self::isLoggedIn()) {
            return false;
        }

        $now         = time();
        $idleLimit   = defined('SESSION_IDLE_TIMEOUT') ? SESSION_IDLE_TIMEOUT : (int)(SESSION_TIMEOUT / 2);

        // 1. Absolute timeout — has the session lived too long overall?
        if (isset($_SESSION['login_time']) && ($now - $_SESSION['login_time']) > SESSION_TIMEOUT) {
            self::logout();
            return false;
        }

        // 2. Idle timeout — has the user been inactive too long?
        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > $idleLimit) {
            self::logout();
            return false;
        }

        // Session is valid — update last_activity only (login_time stays fixed)
        $_SESSION['last_activity'] = $now;
        return true;
    }

    /**
     * Change password.
     */
    public static function changePassword($userId, $oldPassword, $newPassword) {
        self::init();

        $user = self::$db->fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        if (!password_verify($oldPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        self::$db->update('users', ['password_hash' => $newPasswordHash], 'id = ?', [$userId]);

        return ['success' => true, 'message' => 'Password changed successfully'];
    }

    /**
     * Update user profile.
     */
    public static function updateProfile($userId, $data) {
        self::init();

        $allowedFields = ['full_name', 'email'];
        $updateData    = [];

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
     * Get user by ID.
     */
    public static function getUserById($userId) {
        self::init();
        return self::$db->fetchOne(
            'SELECT id, username, email, full_name, role, is_active, created_at FROM users WHERE id = ?',
            [$userId]
        );
    }
}
