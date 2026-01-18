<?php
/**
 * Dızo Wear - Admin Orders
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Order.php';

$orderModel = new Order();

// Durum güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int) $_POST['order_id'];
    $status = $_POST['status'];
    $orderModel->updateStatus($orderId, $status);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sipariş durumu güncellendi.'];
    header('Location: orders.php');
    exit;
}

// Filtreleme
$statusFilter = $_GET['status'] ?? '';
if ($statusFilter) {
    $orders = $orderModel->getByStatus($statusFilter);
} else {
    $orders = $orderModel->all('created_at', 'DESC');
}

$statusLabels = Order::getStatusLabels();
$paymentStatusLabels = Order::getPaymentStatusLabels();

$pageTitle = 'Siparişler';
include 'views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Sipariş Listesi</h4>
    <div class="btn-group">
        <a href="orders.php" class="btn btn-outline-dark btn-sm <?= !$statusFilter ? 'active' : '' ?>">Tümü</a>
        <a href="?status=pending" class="btn btn-outline-dark btn-sm <?= $statusFilter === 'pending' ? 'active' : '' ?>">Bekleyen</a>
        <a href="?status=confirmed" class="btn btn-outline-dark btn-sm <?= $statusFilter === 'confirmed' ? 'active' : '' ?>">Onaylanan</a>
        <a href="?status=shipped" class="btn btn-outline-dark btn-sm <?= $statusFilter === 'shipped' ? 'active' : '' ?>">Kargoda</a>
        <a href="?status=delivered" class="btn btn-outline-dark btn-sm <?= $statusFilter === 'delivered' ? 'active' : '' ?>">Teslim</a>
    </div>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Ödeme</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th width="150">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                Sipariş bulunamadı
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <a href="order-detail.php?id=<?= $order['id'] ?>" class="fw-bold">
                                        #<?= htmlspecialchars($order['order_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($order['name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($order['email']) ?></small>
                                </td>
                                <td class="fw-bold"><?= formatPrice($order['total']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'failed' ? 'danger' : 'warning') ?>">
                                        <?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline status-form">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="status" class="form-select form-select-sm status-select" onchange="this.form.submit()">
                                            <?php foreach ($statusLabels as $key => $label): ?>
                                                <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td><?= formatDate($order['created_at']) ?></td>
                                <td>
                                    <a href="order-detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detay
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

<?php include 'views/layouts/footer.php'; ?>
