<?php
/**
 * Dızo Wear - Installer
 * Veritabanı kurulum ve admin hesabı oluşturma
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = (int) ($_GET['step'] ?? 1);
$error = '';
$success = '';

// Kurulum tamamlandı mı kontrol et
if (file_exists(__DIR__ . '/config/.installed')) {
    die('Kurulum zaten tamamlanmış. Güvenlik için bu dosyayı silin.');
}

// Form işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        // Veritabanı bağlantı testi
        $host = trim($_POST['db_host'] ?? 'localhost');
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? 'root');
        $pass = $_POST['db_pass'] ?? '';
        
        try {
            $pdo = new PDO("mysql:host={$host}", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Veritabanını oluştur
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$name}`");
            
            // Tabloları oluştur
            $sql = file_get_contents(__DIR__ . '/install_schema.sql');
            $pdo->exec($sql);
            
            // Config dosyasını güncelle
            $configContent = "<?php
/**
 * Dızo Wear - Database Configuration
 */

class Database {
    private static \$instance = null;
    private \$connection;
    
    private \$host = '{$host}';
    private \$dbname = '{$name}';
    private \$username = '{$user}';
    private \$password = '{$pass}';
    private \$charset = 'utf8mb4';
    
    private function __construct() {
        try {
            \$dsn = \"mysql:host={\$this->host};dbname={\$this->dbname};charset={\$this->charset}\";
            \$options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            \$this->connection = new PDO(\$dsn, \$this->username, \$this->password, \$options);
        } catch (PDOException \$e) {
            die('Veritabanı bağlantı hatası: ' . \$e->getMessage());
        }
    }
    
    public static function getInstance(): self {
        if (self::\$instance === null) {
            self::\$instance = new self();
        }
        return self::\$instance;
    }
    
    public function getConnection(): PDO {
        return \$this->connection;
    }
    
    public function query(string \$sql, array \$params = []): PDOStatement {
        \$stmt = \$this->connection->prepare(\$sql);
        \$stmt->execute(\$params);
        return \$stmt;
    }
    
    public function fetch(string \$sql, array \$params = []): ?array {
        \$result = \$this->query(\$sql, \$params)->fetch();
        return \$result ?: null;
    }
    
    public function fetchAll(string \$sql, array \$params = []): array {
        return \$this->query(\$sql, \$params)->fetchAll();
    }
    
    public function lastInsertId(): string {
        return \$this->connection->lastInsertId();
    }
    
    private function __clone() {}
    public function __wakeup() { throw new Exception(\"Cannot unserialize singleton\"); }
}";
            file_put_contents(__DIR__ . '/config/database.php', $configContent);
            
            $_SESSION['install_db'] = true;
            header('Location: install.php?step=2');
            exit;
            
        } catch (PDOException $e) {
            $error = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
    
    if ($step === 2) {
        // Admin hesabı oluştur
        $adminName = trim($_POST['admin_name'] ?? '');
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminPass = $_POST['admin_pass'] ?? '';
        
        if (empty($adminName) || empty($adminEmail) || empty($adminPass)) {
            $error = 'Tüm alanları doldurun.';
        } elseif (strlen($adminPass) < 6) {
            $error = 'Şifre en az 6 karakter olmalı.';
        } else {
            require_once __DIR__ . '/config/database.php';
            $db = Database::getInstance();
            
            $hashedPass = password_hash($adminPass, PASSWORD_BCRYPT);
            $db->query(
                "INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, 'admin', 'active', NOW())",
                [$adminName, $adminEmail, $hashedPass]
            );
            
            // Örnek veriler ekle
            insertSampleData($db);
            
            // Kurulum tamamlandı işareti
            file_put_contents(__DIR__ . '/config/.installed', date('Y-m-d H:i:s'));
            
            $_SESSION['install_complete'] = true;
            $_SESSION['admin_email'] = $adminEmail;
            header('Location: install.php?step=3');
            exit;
        }
    }
}

function insertSampleData($db) {
    // Kategoriler
    $categories = [
        ['Tişört', 'tisort', 1],
        ['Sweatshirt', 'sweatshirt', 2],
        ['Hoodie', 'hoodie', 3],
        ['Pantolon', 'pantolon', 4],
        ['Aksesuar', 'aksesuar', 5],
    ];
    
    foreach ($categories as $cat) {
        $db->query(
            "INSERT INTO categories (name, slug, sort_order, status) VALUES (?, ?, ?, 'active')",
            $cat
        );
    }
    
    // Ayarlar
    $settings = [
        ['site_name', 'Dızo Wear'],
        ['site_description', 'Premium Streetwear Giyim Markası'],
        ['site_email', 'info@dizowear.com'],
        ['site_phone', '+90 212 555 0000'],
        ['shipping_cost', '29.90'],
        ['free_shipping_limit', '500'],
        ['min_order_amount', '50'],
    ];
    
    foreach ($settings as $setting) {
        $db->query(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)",
            $setting
        );
    }
    
    // Örnek ürünler
    $products = [
        ['Oversize Basic Tişört', 'oversize-basic-tisort', 1, 'Premium kalite pamuklu oversize tişört', 249.90, 199.90, 1, 1],
        ['Street Style Hoodie', 'street-style-hoodie', 3, 'Kapüşonlu street style sweatshirt', 449.90, null, 1, 1],
        ['Minimal Logo Sweatshirt', 'minimal-logo-sweatshirt', 2, 'Minimal tasarım logo sweatshirt', 399.90, 349.90, 1, 0],
    ];
    
    foreach ($products as $product) {
        $db->query(
            "INSERT INTO products (name, slug, category_id, description, price, sale_price, is_featured, is_new, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())",
            $product
        );
        $productId = $db->lastInsertId();
        
        // Bedenler
        $sizes = ['S' => 10, 'M' => 15, 'L' => 12, 'XL' => 8];
        foreach ($sizes as $size => $stock) {
            $db->query(
                "INSERT INTO product_sizes (product_id, size, stock) VALUES (?, ?, ?)",
                [$productId, $size, $stock]
            );
        }
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurulum | Dızo Wear</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; min-height: 100vh; }
        .installer { max-width: 600px; margin: 50px auto; }
        .install-card { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .logo { font-size: 32px; font-weight: 800; text-align: center; margin-bottom: 10px; }
        .logo span { color: #666; }
        .steps { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; }
        .step { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; background: #eee; color: #666; }
        .step.active { background: #000; color: #fff; }
        .step.done { background: #28a745; color: #fff; }
    </style>
</head>
<body>
    <div class="installer">
        <div class="install-card">
            <div class="logo">DIZO<span>WEAR</span></div>
            <p class="text-center text-muted mb-4">E-Ticaret Scripti Kurulum Sihirbazı</p>
            
            <div class="steps">
                <div class="step <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">1</div>
                <div class="step <?= $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' ?>">2</div>
                <div class="step <?= $step >= 3 ? 'done' : '' ?>">3</div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($step === 1): ?>
                <!-- Step 1: Database -->
                <h4 class="mb-4">Veritabanı Ayarları</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Sunucu (Host)</label>
                        <input type="text" name="db_host" class="form-control" value="localhost" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Veritabanı Adı</label>
                        <input type="text" name="db_name" class="form-control" value="dizowear" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı</label>
                        <input type="text" name="db_user" class="form-control" value="root" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifre</label>
                        <input type="password" name="db_pass" class="form-control">
                        <small class="text-muted">XAMPP varsayılan: boş bırakın</small>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Devam Et <i class="bi bi-arrow-right ms-2"></i></button>
                </form>
            
            <?php elseif ($step === 2): ?>
                <!-- Step 2: Admin Account -->
                <h4 class="mb-4">Admin Hesabı</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="admin_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input type="email" name="admin_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifre</label>
                        <input type="password" name="admin_pass" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Kurulumu Tamamla <i class="bi bi-check-circle ms-2"></i></button>
                </form>
            
            <?php elseif ($step === 3): ?>
                <!-- Step 3: Complete -->
                <div class="text-center">
                    <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
                    <h3>Kurulum Tamamlandı!</h3>
                    <p class="text-muted">Dızo Wear başarıyla kuruldu.</p>
                    
                    <div class="bg-light p-3 rounded mb-4">
                        <strong>Admin Giriş:</strong><br>
                        E-posta: <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?><br>
                        <small class="text-muted">Belirlediğiniz şifreyi kullanın</small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="admin/login.php" class="btn btn-dark btn-lg">
                            <i class="bi bi-shield-lock me-2"></i>Admin Paneli
                        </a>
                        <a href="./" class="btn btn-outline-dark">
                            <i class="bi bi-eye me-2"></i>Siteyi Görüntüle
                        </a>
                    </div>
                    
                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Güvenlik:</strong> install.php ve install_schema.sql dosyalarını silin!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
