<?php
/**
 * Dızo Wear - Admin Order Detail
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Order.php';

$orderModel = new Order();

$orderId = (int) ($_GET['id'] ?? 0);
$order = $orderModel->find($orderId);

if (!$order) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Sipariş bulunamadı.'];
    header('Location: orders.php');
    exit;
}

$items = $orderModel->getItems($orderId);
$statusLabels = Order::getStatusLabels();
$paymentLabels = Order::getPaymentStatusLabels();

// Durum güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status'])) {
        $orderModel->updateStatus($orderId, $_POST['status']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sipariş durumu güncellendi.'];
    }
    if (isset($_POST['payment_status'])) {
        $orderModel->updatePaymentStatus($orderId, $_POST['payment_status']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Ödeme durumu güncellendi.'];
    }
    header('Location: order-detail.php?id=' . $orderId);
    exit;
}

$pageTitle = 'Sipariş #' . $order['order_number'];
include 'views/layouts/header.php';
?>

<div class="mb-4">
    <a href="orders.php" class="btn btn-outline-dark btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Siparişlere Dön
    </a>
</div>

<div class="row g-4">
    <!-- Order Info -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sipariş #<?= htmlspecialchars($order['order_number']) ?></h5>
                <span class="badge status-<?= $order['status'] ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Müşteri Bilgileri</strong>
                        <p class="mb-1"><?= htmlspecialchars($order['name']) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($order['email']) ?></p>
                        <p class="mb-0"><?= htmlspecialchars($order['phone']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Teslimat Adresi</strong>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">Sipariş Kalemleri</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Beden</th>
                                <th>Birim Fiyat</th>
                                <th>Adet</th>
                                <th class="text-end">Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($item['product_name']) ?></strong></td>
                                    <td><span class="badge bg-secondary"><?= $item['size'] ?></span></td>
                                    <td><?= formatPrice($item['price']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td class="text-end"><strong><?= formatPrice($item['total']) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end">Ara Toplam:</td>
                                <td class="text-end"><?= formatPrice($order['subtotal']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Kargo:</td>
                                <td class="text-end"><?= formatPrice($order['shipping_cost']) ?></td>
                            </tr>
                            <tr class="table-dark">
                                <td colspan="4" class="text-end"><strong>TOPLAM:</strong></td>
                                <td class="text-end"><strong><?= formatPrice($order['total']) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if ($order['notes']): ?>
            <div class="admin-card mt-4">
                <div class="card-header"><h5 class="mb-0">Müşteri Notu</h5></div>
                <div class="card-body"><?= nl2br(htmlspecialchars($order['notes'])) ?></div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Actions -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="card-header"><h5 class="mb-0">Sipariş Durumu</h5></div>
            <div class="card-body">
                <form method="POST">
                    <select name="status" class="form-select mb-3">
                        <?php foreach ($statusLabels as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-dark w-100">Durumu Güncelle</button>
                </form>
            </div>
        </div>
        
        <div class="admin-card mb-4">
            <div class="card-header"><h5 class="mb-0">Ödeme Durumu</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'failed' ? 'danger' : 'warning') ?> fs-6">
                        <?= $paymentLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                    </span>
                </div>
                <form method="POST">
                    <select name="payment_status" class="form-select mb-3">
                        <?php foreach ($paymentLabels as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $order['payment_status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-outline-dark w-100">Ödeme Durumunu Güncelle</button>
                </form>
            </div>
        </div>
        
        <div class="admin-card">
            <div class="card-header"><h5 class="mb-0">Tarih Bilgileri</h5></div>
            <div class="card-body">
                <p class="mb-2"><strong>Oluşturulma:</strong> <?= formatDate($order['created_at'], 'd.m.Y H:i') ?></p>
                <p class="mb-0"><strong>Güncelleme:</strong> <?= formatDate($order['updated_at'], 'd.m.Y H:i') ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
