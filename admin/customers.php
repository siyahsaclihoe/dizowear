<?php
/**
 * Dızo Wear - Admin Customers
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/User.php';

$userModel = new User();

// Durum güncelleme
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $user = $userModel->find((int)$_GET['toggle']);
    if ($user) {
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $userModel->update($user['id'], ['status' => $newStatus]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kullanıcı durumu güncellendi.'];
    }
    header('Location: customers.php');
    exit;
}

$customers = $userModel->all('created_at', 'DESC');

$pageTitle = 'Müşteriler';
include 'views/layouts/header.php';
?>

<div class="admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Müşteri Listesi</h5>
        <span class="badge bg-dark"><?= count($customers) ?> müşteri</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th width="80">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 d-block mb-3"></i>
                                Henüz müşteri yok
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>#<?= $customer['id'] ?></td>
                                <td><strong><?= htmlspecialchars($customer['name']) ?></strong></td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                <td><?= htmlspecialchars($customer['phone'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $customer['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                        <?= $customer['role'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $customer['status'] === 'active' ? 'success' : 'warning' ?>">
                                        <?= $customer['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                                    </span>
                                </td>
                                <td><?= formatDate($customer['created_at']) ?></td>
                                <td>
                                    <a href="?toggle=<?= $customer['id'] ?>" class="btn btn-sm btn-outline-secondary"
                                       title="Durumu Değiştir">
                                        <i class="bi bi-toggle-<?= $customer['status'] === 'active' ? 'on' : 'off' ?>"></i>
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
