<?php
/**
 * Dızo Wear - Lisans Sistemi Konfigürasyonu
 * Domain bazlı lisans doğrulama
 */

class License {
    private static $instance = null;
    private $licenseKey;
    private $domain;
    private $isValid = false;
    private $licenseData = [];
    
    // Lisans sunucu ayarları
    private $licenseServer = 'https://license.dizowear.com/verify';
    private $offlineMode = true; // Local development için true
    
    private function __construct() {
        $this->domain = $this->getCurrentDomain();
        $this->licenseKey = $this->getLicenseKey();
        $this->validateLicense();
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Mevcut domain'i al
     */
    private function getCurrentDomain(): string {
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // www. prefix'ini kaldır
        $domain = preg_replace('/^www\./', '', $domain);
        // Port numarasını kaldır
        $domain = preg_replace('/:\d+$/', '', $domain);
        return strtolower($domain);
    }
    
    /**
     * Lisans anahtarını al
     */
    private function getLicenseKey(): string {
        $licenseFile = __DIR__ . '/license.key';
        if (file_exists($licenseFile)) {
            return trim(file_get_contents($licenseFile));
        }
        return '';
    }
    
    /**
     * Lisansı doğrula
     */
    private function validateLicense(): bool {
        // Localhost için otomatik geçerli
        if ($this->isLocalhost()) {
            $this->isValid = true;
            $this->licenseData = [
                'type' => 'development',
                'domain' => 'localhost',
                'expires' => 'never',
            ];
            return true;
        }
        
        // Offline mod aktifse veritabanından kontrol
        if ($this->offlineMode) {
            return $this->validateOffline();
        }
        
        // Online lisans sunucusu kontrolü
        return $this->validateOnline();
    }
    
    /**
     * Localhost kontrolü
     */
    private function isLocalhost(): bool {
        $localDomains = ['localhost', '127.0.0.1', '::1'];
        return in_array($this->domain, $localDomains);
    }
    
    /**
     * Offline lisans doğrulama
     */
    private function validateOffline(): bool {
        try {
            require_once __DIR__ . '/database.php';
            $db = Database::getInstance();
            
            $license = $db->fetch(
                "SELECT * FROM licenses WHERE license_key = ? AND (domain = ? OR domain = '*') AND status = 'active'",
                [$this->licenseKey, $this->domain]
            );
            
            if ($license) {
                // Süre kontrolü
                if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
                    $this->isValid = false;
                    return false;
                }
                
                $this->isValid = true;
                $this->licenseData = $license;
                return true;
            }
        } catch (Exception $e) {
            // Veritabanı hatası - development modunda geçerli say
            if ($this->isLocalhost()) {
                $this->isValid = true;
                return true;
            }
        }
        
        $this->isValid = false;
        return false;
    }
    
    /**
     * Online lisans doğrulama
     */
    private function validateOnline(): bool {
        $data = [
            'license_key' => $this->licenseKey,
            'domain' => $this->domain,
            'ip' => $_SERVER['SERVER_ADDR'] ?? '',
        ];
        
        $ch = curl_init($this->licenseServer);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['valid']) && $result['valid'] === true) {
                $this->isValid = true;
                $this->licenseData = $result['data'] ?? [];
                
                // Lisansı local olarak cache'le
                $this->cacheLicense();
                return true;
            }
        }
        
        $this->isValid = false;
        return false;
    }
    
    /**
     * Lisansı cache'le
     */
    private function cacheLicense(): void {
        $cacheFile = __DIR__ . '/../cache/license.cache';
        $cacheData = [
            'key' => $this->licenseKey,
            'domain' => $this->domain,
            'data' => $this->licenseData,
            'cached_at' => time(),
            'expires_in' => 86400, // 24 saat
        ];
        
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($cacheFile, serialize($cacheData));
    }
    
    /**
     * Lisans geçerli mi?
     */
    public function isValid(): bool {
        return $this->isValid;
    }
    
    /**
     * Lisans bilgilerini al
     */
    public function getLicenseData(): array {
        return $this->licenseData;
    }
    
    /**
     * Lisans tipini al
     */
    public function getLicenseType(): string {
        return $this->licenseData['type'] ?? 'unknown';
    }
    
    /**
     * Lisans hata sayfası göster
     */
    public function showLicenseError(): void {
        header('HTTP/1.1 403 Forbidden');
        echo '<!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lisans Hatası - Dızo Wear</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: "Segoe UI", sans-serif; 
                    background: #000; 
                    color: #fff;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .container {
                    text-align: center;
                    padding: 40px;
                }
                h1 { font-size: 48px; margin-bottom: 20px; }
                p { font-size: 18px; color: #888; margin-bottom: 30px; }
                .code { 
                    background: #111; 
                    padding: 20px; 
                    border-radius: 8px;
                    font-family: monospace;
                    color: #ff4444;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>⚠️ Lisans Hatası</h1>
                <p>Bu domain için geçerli bir lisans bulunamadı.</p>
                <div class="code">
                    Domain: ' . htmlspecialchars($this->domain) . '<br>
                    Lisans desteği için: license@dizowear.com
                </div>
            </div>
        </body>
        </html>';
        exit;
    }
    
    /**
     * Her istekte lisans kontrolü yap
     */
    public static function check(): void {
        $license = self::getInstance();
        if (!$license->isValid()) {
            $license->showLicenseError();
        }
    }
}
