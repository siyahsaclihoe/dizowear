<?php
/**
 * Dızo Wear - Admin Settings
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';

$db = Database::getInstance();

// Ayarları kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_description' => trim($_POST['site_description'] ?? ''),
        'site_email' => trim($_POST['site_email'] ?? ''),
        'site_phone' => trim($_POST['site_phone'] ?? ''),
        'site_address' => trim($_POST['site_address'] ?? ''),
        'social_instagram' => trim($_POST['social_instagram'] ?? ''),
        'social_twitter' => trim($_POST['social_twitter'] ?? ''),
        'social_youtube' => trim($_POST['social_youtube'] ?? ''),
        'social_tiktok' => trim($_POST['social_tiktok'] ?? ''),
        'shipping_cost' => (float) ($_POST['shipping_cost'] ?? 29.90),
        'free_shipping_limit' => (float) ($_POST['free_shipping_limit'] ?? 500),
        'min_order_amount' => (float) ($_POST['min_order_amount'] ?? 50),
    ];
    
    foreach ($settings as $key => $value) {
        // Upsert
        $exists = $db->fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        if ($exists) {
            $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        } else {
            $db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
        }
    }
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Ayarlar kaydedildi.'];
    header('Location: settings.php');
    exit;
}

// Mevcut ayarları al
$settingsRaw = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
$settings = [];
foreach ($settingsRaw as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Site Ayarları';
include 'views/layouts/header.php';
?>

<form method="POST">
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- General Settings -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Genel Ayarlar</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Site Adı</label>
                            <input type="text" name="site_name" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_name'] ?? 'Dızo Wear') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Site E-posta</label>
                            <input type="email" name="site_email" class="form-control"
                                   value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Site Açıklaması</label>
                            <textarea name="site_description" class="form-control" rows="2"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="site_phone" class="form-control"
                                   value="<?= htmlspecialchars($settings['site_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adres</label>
                            <input type="text" name="site_address" class="form-control"
                                   value="<?= htmlspecialchars($settings['site_address'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Social Media -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-share me-2"></i>Sosyal Medya</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-instagram me-1"></i>Instagram</label>
                            <input type="url" name="social_instagram" class="form-control" placeholder="https://instagram.com/..."
                                   value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-twitter-x me-1"></i>Twitter</label>
                            <input type="url" name="social_twitter" class="form-control" placeholder="https://twitter.com/..."
                                   value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-youtube me-1"></i>YouTube</label>
                            <input type="url" name="social_youtube" class="form-control" placeholder="https://youtube.com/..."
                                   value="<?= htmlspecialchars($settings['social_youtube'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-tiktok me-1"></i>TikTok</label>
                            <input type="url" name="social_tiktok" class="form-control" placeholder="https://tiktok.com/..."
                                   value="<?= htmlspecialchars($settings['social_tiktok'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Settings -->
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Kargo Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kargo Ücreti (TL)</label>
                            <input type="number" name="shipping_cost" class="form-control" step="0.01"
                                   value="<?= htmlspecialchars($settings['shipping_cost'] ?? '29.90') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ücretsiz Kargo Limiti (TL)</label>
                            <input type="number" name="free_shipping_limit" class="form-control" step="0.01"
                                   value="<?= htmlspecialchars($settings['free_shipping_limit'] ?? '500') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimum Sipariş Tutarı (TL)</label>
                            <input type="number" name="min_order_amount" class="form-control" step="0.01"
                                   value="<?= htmlspecialchars($settings['min_order_amount'] ?? '50') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="card-body">
                    <button type="submit" class="btn btn-dark w-100 btn-lg">
                        <i class="bi bi-check-circle me-2"></i>Ayarları Kaydet
                    </button>
                </div>
            </div>
            
            <div class="admin-card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Sistem Bilgisi</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>PHP:</strong> <?= phpversion() ?></li>
                        <li class="mb-2"><strong>MySQL:</strong> <?= $db->getConnection()->getAttribute(PDO::ATTR_SERVER_VERSION) ?></li>
                        <li><strong>Script:</strong> Dızo Wear v1.0</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include 'views/layouts/footer.php'; ?>
