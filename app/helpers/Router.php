<?php
/**
 * Dızo Wear - Router Class
 * SEO-friendly URL routing sistemi
 */

class Router {
    private $routes = [];
    private $params = [];
    private $namespace = 'Controllers\\';
    
    /**
     * Route formatı: GET /products/{id}
     */
    public function add(string $method, string $route, string $handler): void {
        // {param} formatını regex'e çevir
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
    
    /**
     * GET route ekle
     */
    public function get(string $route, string $handler): void {
        $this->add('GET', $route, $handler);
    }
    
    /**
     * POST route ekle
     */
    public function post(string $route, string $handler): void {
        $this->add('POST', $route, $handler);
    }
    
    /**
     * Route'u çalıştır
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // URL parametrelerini al
                $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Handler'ı parse et: Controller@method
                list($controller, $action) = explode('@', $route['handler']);
                
                $this->callController($controller, $action);
                return;
            }
        }
        
        // 404 Not Found
        $this->notFound();
    }
    
    /**
     * URI'yi al ve temizle
     */
    private function getUri(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Query string'i kaldır
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Base path'i kaldır (XAMPP için)
        $basePath = '/dizowear';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Trailing slash'ı kaldır
        $uri = rtrim($uri, '/');
        
        return $uri ?: '/';
    }
    
    /**
     * Controller'ı çağır
     */
    private function callController(string $controller, string $action): void {
        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
        
        if (!file_exists($controllerFile)) {
            $this->notFound("Controller bulunamadı: $controller");
            return;
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controller)) {
            $this->notFound("Controller class bulunamadı: $controller");
            return;
        }
        
        $instance = new $controller();
        
        if (!method_exists($instance, $action)) {
            $this->notFound("Action bulunamadı: $action");
            return;
        }
        
        // URL parametrelerini method'a geç
        call_user_func_array([$instance, $action], $this->params);
    }
    
    /**
     * URL parametresi al
     */
    public function getParam(string $key, $default = null) {
        return $this->params[$key] ?? $default;
    }
    
    /**
     * 404 sayfası
     */
    private function notFound(string $message = 'Sayfa bulunamadı'): void {
        header('HTTP/1.1 404 Not Found');
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }
    
    /**
     * Redirect
     */
    public static function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * URL oluştur
     */
    public static function url(string $path = ''): string {
        $base = '/dizowear';
        return $base . '/' . ltrim($path, '/');
    }
    
    /**
     * Asset URL oluştur
     */
    public static function asset(string $path): string {
        return self::url('assets/' . ltrim($path, '/'));
    }
}
