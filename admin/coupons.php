<?php
/**
 * Dızo Wear - Admin Coupons
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Coupon.php';

$couponModel = new Coupon();

// Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $couponModel->delete((int)$_GET['delete']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kupon silindi.'];
    header('Location: coupons.php');
    exit;
}

// Durum değiştir
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $coupon = $couponModel->find((int)$_GET['toggle']);
    if ($coupon) {
        $newStatus = $coupon['status'] === 'active' ? 'inactive' : 'active';
        $couponModel->update($coupon['id'], ['status' => $newStatus]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kupon durumu güncellendi.'];
    }
    header('Location: coupons.php');
    exit;
}

// Ekleme/Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code' => strtoupper(trim($_POST['code'] ?? '')),
        'type' => $_POST['type'] ?? 'percentage',
        'value' => (float) ($_POST['value'] ?? 0),
        'min_order_amount' => (float) ($_POST['min_order_amount'] ?? 0),
        'max_discount' => !empty($_POST['max_discount']) ? (float) $_POST['max_discount'] : null,
        'usage_limit' => !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : null,
        'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
        'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
        'status' => $_POST['status'] ?? 'active',
    ];
    
    $editId = (int) ($_POST['edit_id'] ?? 0);
    
    // Kod boşsa otomatik oluştur
    if (empty($data['code'])) {
        $data['code'] = Coupon::generateCode();
    }
    
    if ($editId > 0) {
        $couponModel->update($editId, $data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kupon güncellendi.'];
    } else {
        $couponModel->create($data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kupon eklendi.'];
    }
    
    header('Location: coupons.php');
    exit;
}

$coupons = $couponModel->all('created_at', 'DESC');

$pageTitle = 'Kupon Kodları';
include 'views/layouts/header.php';
?>

<div class="row g-4">
    <!-- Form -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0" id="formTitle">Yeni Kupon</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="couponForm">
                    <input type="hidden" name="edit_id" id="editId" value="0">
                    
                    <div class="mb-3">
                        <label class="form-label">Kupon Kodu</label>
                        <div class="input-group">
                            <input type="text" name="code" id="couponCode" class="form-control" 
                                   placeholder="Otomatik oluşturulur" style="text-transform: uppercase;">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">
                                <i class="bi bi-shuffle"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">İndirim Tipi</label>
                            <select name="type" id="couponType" class="form-select">
                                <option value="percentage">Yüzde (%)</option>
                                <option value="fixed">Sabit (TL)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Değer *</label>
                            <input type="number" name="value" id="couponValue" class="form-control" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Min. Sipariş (TL)</label>
                            <input type="number" name="min_order_amount" id="minOrder" class="form-control" 
                                   step="0.01" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Maks. İndirim (TL)</label>
                            <input type="number" name="max_discount" id="maxDiscount" class="form-control" 
                                   step="0.01" min="0" placeholder="Sınırsız">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kullanım Limiti</label>
                        <input type="number" name="usage_limit" id="usageLimit" class="form-control" 
                               min="1" placeholder="Sınırsız">
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Başlangıç</label>
                            <input type="datetime-local" name="start_date" id="startDate" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Bitiş</label>
                            <input type="datetime-local" name="end_date" id="endDate" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select name="status" id="couponStatus" class="form-select">
                            <option value="active">Aktif</option>
                            <option value="inactive">Pasif</option>
                        </select>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-check-circle me-2"></i><span id="submitText">Ekle</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="cancelEdit" onclick="resetForm()">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- List -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Kupon Listesi</h5>
                <span class="badge bg-dark"><?= count($coupons) ?> kupon</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kod</th>
                                <th>İndirim</th>
                                <th>Kullanım</th>
                                <th>Geçerlilik</th>
                                <th>Durum</th>
                                <th width="120">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($coupons)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Henüz kupon yok</td></tr>
                            <?php else: ?>
                                <?php foreach ($coupons as $coupon): ?>
                                    <tr>
                                        <td>
                                            <code class="fs-6"><?= htmlspecialchars($coupon['code']) ?></code>
                                        </td>
                                        <td>
                                            <?php if ($coupon['type'] === 'percentage'): ?>
                                                <span class="badge bg-info">%<?= $coupon['value'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?= formatPrice($coupon['value']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $coupon['used_count'] ?><?= $coupon['usage_limit'] ? '/' . $coupon['usage_limit'] : '' ?>
                                        </td>
                                        <td>
                                            <?php if ($coupon['end_date']): ?>
                                                <small><?= date('d.m.Y', strtotime($coupon['end_date'])) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Süresiz</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $coupon['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= $coupon['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editCoupon(<?= htmlspecialchars(json_encode($coupon)) ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?toggle=<?= $coupon['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-toggle-<?= $coupon['status'] === 'active' ? 'on' : 'off' ?>"></i>
                                            </a>
                                            <a href="?delete=<?= $coupon['id'] ?>" class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = '';
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('couponCode').value = code;
}

function editCoupon(coupon) {
    document.getElementById('editId').value = coupon.id;
    document.getElementById('couponCode').value = coupon.code;
    document.getElementById('couponType').value = coupon.type;
    document.getElementById('couponValue').value = coupon.value;
    document.getElementById('minOrder').value = coupon.min_order_amount || 0;
    document.getElementById('maxDiscount').value = coupon.max_discount || '';
    document.getElementById('usageLimit').value = coupon.usage_limit || '';
    document.getElementById('startDate').value = coupon.start_date ? coupon.start_date.replace(' ', 'T').slice(0,16) : '';
    document.getElementById('endDate').value = coupon.end_date ? coupon.end_date.replace(' ', 'T').slice(0,16) : '';
    document.getElementById('couponStatus').value = coupon.status;
    document.getElementById('formTitle').textContent = 'Kupon Düzenle';
    document.getElementById('submitText').textContent = 'Güncelle';
    document.getElementById('cancelEdit').classList.remove('d-none');
}

function resetForm() {
    document.getElementById('couponForm').reset();
    document.getElementById('editId').value = '0';
    document.getElementById('formTitle').textContent = 'Yeni Kupon';
    document.getElementById('submitText').textContent = 'Ekle';
    document.getElementById('cancelEdit').classList.add('d-none');
}
</script>

<?php include 'views/layouts/footer.php'; ?>
