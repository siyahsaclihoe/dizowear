<?php
/**
 * Dızo Wear - Admin Payments
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';

$db = Database::getInstance();

// Ödeme ayarlarını kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'payment_test_mode' => isset($_POST['test_mode']) ? '1' : '0',
        'paytr_merchant_id' => trim($_POST['paytr_merchant_id'] ?? ''),
        'paytr_merchant_key' => trim($_POST['paytr_merchant_key'] ?? ''),
        'paytr_merchant_salt' => trim($_POST['paytr_merchant_salt'] ?? ''),
        'iyzico_api_key' => trim($_POST['iyzico_api_key'] ?? ''),
        'iyzico_secret_key' => trim($_POST['iyzico_secret_key'] ?? ''),
    ];
    
    foreach ($settings as $key => $value) {
        $exists = $db->fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        if ($exists) {
            $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        } else {
            $db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
        }
    }
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Ödeme ayarları kaydedildi.'];
    header('Location: payments.php');
    exit;
}

// Mevcut ayarlar
$settingsRaw = $db->fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'pay%' OR setting_key LIKE 'iyz%'");
$settings = [];
foreach ($settingsRaw as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Ödeme Ayarları';
include 'views/layouts/header.php';
?>

<form method="POST">
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Test Mode -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bug me-2"></i>Test Modu</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="test_mode" class="form-check-input" id="testMode"
                               <?= ($settings['payment_test_mode'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="testMode">
                            <strong>Test Modu Aktif</strong><br>
                            <small class="text-muted">Gerçek ödeme alınmaz, demo ödeme sayfası gösterilir.</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- PayTR -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>PayTR Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Merchant ID</label>
                            <input type="text" name="paytr_merchant_id" class="form-control"
                                   value="<?= htmlspecialchars($settings['paytr_merchant_id'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Merchant Key</label>
                            <input type="password" name="paytr_merchant_key" class="form-control"
                                   value="<?= htmlspecialchars($settings['paytr_merchant_key'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Merchant Salt</label>
                            <input type="password" name="paytr_merchant_salt" class="form-control"
                                   value="<?= htmlspecialchars($settings['paytr_merchant_salt'] ?? '') ?>">
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        PayTR hesabınızdan bu bilgileri alabilirsiniz: 
                        <a href="https://www.paytr.com" target="_blank">paytr.com</a>
                    </p>
                </div>
            </div>
            
            <!-- Iyzico -->
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card-2-front me-2"></i>İyzico Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">API Key</label>
                            <input type="text" name="iyzico_api_key" class="form-control"
                                   value="<?= htmlspecialchars($settings['iyzico_api_key'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secret Key</label>
                            <input type="password" name="iyzico_secret_key" class="form-control"
                                   value="<?= htmlspecialchars($settings['iyzico_secret_key'] ?? '') ?>">
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        İyzico hesabınızdan bu bilgileri alabilirsiniz: 
                        <a href="https://www.iyzico.com" target="_blank">iyzico.com</a>
                    </p>
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
                <div class="card-header"><h5 class="mb-0">Bilgi</h5></div>
                <div class="card-body">
                    <p class="mb-2"><strong>Callback URL:</strong></p>
                    <code class="d-block bg-light p-2 rounded mb-3" style="word-break: break-all;">
                        <?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/dizowear/checkout/callback' ?>
                    </code>
                    <p class="text-muted small mb-0">
                        Bu URL'yi ödeme sağlayıcınızın panelinde "Callback/Webhook URL" olarak tanımlayın.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include 'views/layouts/footer.php'; ?>
