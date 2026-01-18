<?php
/**
 * Dızo Wear - Base Controller
 * Tüm controller'ların miras aldığı ana sınıf
 */

class Controller {
    protected $db;
    protected $data = [];
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = Database::getInstance();
        
        // Session başlat
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // CSRF token oluştur
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Temel view değişkenlerini ayarla
        $this->data['csrf_token'] = $_SESSION['csrf_token'];
        $this->data['user'] = $_SESSION['user'] ?? null;
        $this->data['cart'] = $this->getCart();
        $this->data['settings'] = $this->getSettings();
        $this->data['categories'] = $this->getCategories();
    }
    
    /**
     * View render et
     */
    protected function view(string $view, array $data = []): void {
        $data = array_merge($this->data, $data);
        extract($data);
        
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die("View bulunamadı: $view");
        }
        
        // Output buffering
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        // Layout ile render et
        require __DIR__ . '/../views/layouts/main.php';
    }
    
    /**
     * Layout olmadan view render et
     */
    protected function viewOnly(string $view, array $data = []): void {
        $data = array_merge($this->data, $data);
        extract($data);
        
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die("View bulunamadı: $view");
        }
        
        require $viewPath;
    }
    
    /**
     * JSON response döndür
     */
    protected function json(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * CSRF doğrulama
     */
    protected function verifyCsrf(): bool {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            $this->json(['success' => false, 'message' => 'Güvenlik hatası!'], 403);
            return false;
        }
        
        return true;
    }
    
    /**
     * POST verisini temizle (XSS koruması)
     */
    protected function input(string $key, $default = null) {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }
    
    /**
     * POST verilerini toplu al
     */
    protected function only(array $keys): array {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->input($key);
        }
        return $data;
    }
    
    /**
     * Sepeti session'dan al
     */
    protected function getCart(): array {
        return $_SESSION['cart'] ?? [];
    }
    
    /**
     * Site ayarlarını al
     */
    protected function getSettings(): array {
        try {
            $settings = $this->db->fetchAll("SELECT setting_key, setting_value FROM settings");
            $result = [];
            foreach ($settings as $row) {
                $result[$row['setting_key']] = $row['setting_value'];
            }
            return $result;
        } catch (Exception $e) {
            return [
                'site_name' => 'Dızo Wear',
                'site_url' => 'http://localhost/dizowear',
                'currency' => 'TL',
            ];
        }
    }
    
    /**
     * Kullanıcı giriş yapmış mı?
     */
    protected function isLoggedIn(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    
    /**
     * Giriş gerektir
     */
    protected function requireAuth(): void {
        if (!$this->isLoggedIn()) {
            Router::redirect(Router::url('login'));
        }
    }
    
    /**
     * Flash mesajı ayarla
     */
    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
    
    /**
     * Flash mesajını al ve sil
     */
    protected function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
    
    /**
     * Redirect
     */
    protected function redirect(string $path): void {
        Router::redirect(Router::url($path));
    }
    
    /**
     * Kategorileri al (navigasyon için)
     */
    protected function getCategories(): array {
        try {
            return $this->db->fetchAll(
                "SELECT id, name, slug FROM categories WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6"
            );
        } catch (Exception $e) {
            return [];
        }
    }
}
