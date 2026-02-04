<?php
/**
 * Classe Session - Gestion des sessions utilisateurs
 */
class Session {
    
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Assurer un répertoire de session persistant
            $savePath = getenv('SESSION_SAVE_PATH') ?: '/tmp/francophile_sessions';
            if (!is_dir($savePath)) {
                @mkdir($savePath, 0700, true);
            }
            if (is_dir($savePath) && is_writable($savePath)) {
                session_save_path($savePath);
            }
            
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                     || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                     || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
                     || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');
            
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
            
            // Prolonger la session à chaque requête
            if (isset($_SESSION['login_time'])) {
                $elapsed = time() - $_SESSION['login_time'];
                if ($elapsed > SESSION_LIFETIME) {
                    // Session expirée
                    self::destroy();
                    return;
                }
                // Regénérer l'ID périodiquement (toutes les 30 min) pour la sécurité
                if ($elapsed > 1800 && !isset($_SESSION['last_regenerate'])) {
                    self::regenerate();
                    $_SESSION['last_regenerate'] = time();
                } elseif (isset($_SESSION['last_regenerate']) && (time() - $_SESSION['last_regenerate']) > 1800) {
                    self::regenerate();
                    $_SESSION['last_regenerate'] = time();
                }
            }
        }
    }
    
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }
    
    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
    
    public static function regenerate(): void {
        session_regenerate_id(true);
    }
    
    // Méthodes spécifiques à l'authentification
    
    public static function login(int $userId, string $userType, array $userData): void {
        self::regenerate();
        self::set('user_id', $userId);
        self::set('user_type', $userType); // 'eleve', 'enseignant', 'admin'
        self::set('user_data', $userData);
        self::set('login_time', time());
    }
    
    public static function logout(): void {
        self::destroy();
    }
    
    public static function isLoggedIn(): bool {
        return self::has('user_id') && self::has('user_type');
    }
    
    public static function getUserId(): ?int {
        return self::get('user_id');
    }
    
    public static function getUserType(): ?string {
        return self::get('user_type');
    }
    
    public static function getUserData(): ?array {
        return self::get('user_data');
    }
    
    public static function isEleve(): bool {
        return self::getUserType() === 'eleve';
    }
    
    public static function isEnseignant(): bool {
        return self::getUserType() === 'enseignant';
    }
    
    public static function isAdmin(): bool {
        return self::getUserType() === 'admin';
    }
    
    public static function requireLogin(string $redirectUrl = '/login.php'): void {
        if (!self::isLoggedIn()) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    public static function requireEleve(): void {
        self::requireLogin();
        if (!self::isEleve()) {
            header('Location: /login.php?error=access_denied');
            exit;
        }
    }
    
    public static function requireEnseignant(): void {
        self::requireLogin();
        if (!self::isEnseignant() && !self::isAdmin()) {
            header('Location: /login.php?error=access_denied');
            exit;
        }
    }
    
    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: /login.php?error=access_denied');
            exit;
        }
    }
    
    // Flash messages
    
    public static function setFlash(string $type, string $message): void {
        self::set('flash_' . $type, $message);
    }
    
    public static function getFlash(string $type): ?string {
        $message = self::get('flash_' . $type);
        self::remove('flash_' . $type);
        return $message;
    }
    
    public static function hasFlash(string $type): bool {
        return self::has('flash_' . $type);
    }
}
