<?php
/**
 * Dızo Wear - Admin Dashboard
 */

session_start();

// Admin kontrolü
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/User.php';

$db = Database::getInstance();
$orderModel = new Order();
$productModel = new Product();
$userModel = new User();

// İstatistikler
$totalOrders = $orderModel->count();
$todayOrders = count($orderModel->getTodayOrders());
$totalSales = $orderModel->getTotalSales();
$totalProducts = $productModel->count();
$totalUsers = $userModel->count();

// Son siparişler
$recentOrders = $orderModel->getRecent(5);

$pageTitle = 'Dashboard';
include 'views/layouts/header.php';
?>

<div class="dashboard-content">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($totalOrders) ?></h3>
                    <span>Toplam Sipariş</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-info">
                    <h3><?= formatPrice($totalSales) ?></h3>
                    <span>Toplam Satış</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="bi bi-bag"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($totalProducts) ?></h3>
                    <span>Ürün</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($totalUsers) ?></h3>
                    <span>Müşteri</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Son Siparişler</h5>
                    <a href="orders.php" class="btn btn-sm btn-outline-dark">Tümünü Gör</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Müşteri</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Henüz sipariş yok</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="order-detail.php?id=<?= $order['id'] ?>">
                                                    #<?= htmlspecialchars($order['order_number']) ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($order['name']) ?></td>
                                            <td><?= formatPrice($order['total']) ?></td>
                                            <td>
                                                <span class="badge status-<?= $order['status'] ?>">
                                                    <?= Order::getStatusLabels()[$order['status']] ?? $order['status'] ?>
                                                </span>
                                            </td>
                                            <td><?= formatDate($order['created_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0">Bugün</h5>
                </div>
                <div class="card-body">
                    <div class="quick-stat">
                        <span>Yeni Siparişler</span>
                        <strong><?= $todayOrders ?></strong>
                    </div>
                    <hr>
                    <div class="quick-stat">
                        <span>Bekleyen Siparişler</span>
                        <strong><?= $orderModel->count("status = 'pending'") ?></strong>
                    </div>
                    <hr>
                    <div class="quick-stat">
                        <span>Kargoda</span>
                        <strong><?= $orderModel->count("status = 'shipped'") ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="admin-card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Hızlı Erişim</h5>
                </div>
                <div class="card-body">
                    <a href="product-add.php" class="btn btn-dark w-100 mb-2">
                        <i class="bi bi-plus-circle me-2"></i>Yeni Ürün Ekle
                    </a>
                    <a href="sliders.php" class="btn btn-outline-dark w-100 mb-2">
                        <i class="bi bi-images me-2"></i>Slider Yönetimi
                    </a>
                    <a href="settings.php" class="btn btn-outline-dark w-100">
                        <i class="bi bi-gear me-2"></i>Site Ayarları
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
