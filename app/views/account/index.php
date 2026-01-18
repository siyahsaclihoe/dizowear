<!-- Account Dashboard -->
<div class="account-page section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../partials/account-sidebar.php'; ?>
            
            <!-- Content -->
            <div class="col-lg-9">
                <h3 class="mb-4">Hesabım</h3>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-4">
                                <div class="dashboard-icon">
                                    <i class="bi bi-box"></i>
                                </div>
                                <div>
                                    <h2 class="mb-0"><?= count($recentOrders ?? []) ?></h2>
                                    <span class="text-muted">Sipariş</span>
                                </div>
                            </div>
                            <a href="<?= url('account/orders') ?>" class="stretched-link"></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-4">
                                <div class="dashboard-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h2 class="mb-0"><?= $addressCount ?? 0 ?></h2>
                                    <span class="text-muted">Kayıtlı Adres</span>
                                </div>
                            </div>
                            <a href="<?= url('account/addresses') ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($recentOrders)): ?>
                <div class="mt-5">
                    <h5 class="mb-3">Son Siparişler</h5>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Sipariş No</th>
                                            <th>Tarih</th>
                                            <th>Tutar</th>
                                            <th>Durum</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($order['order_number']) ?></strong></td>
                                            <td><?= formatDate($order['created_at']) ?></td>
                                            <td><?= formatPrice($order['total']) ?></td>
                                            <td><span class="badge status-<?= $order['status'] ?>"><?= $order['status'] ?></span></td>
                                            <td>
                                                <a href="<?= url('account/order/' . $order['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Quick Links -->
                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <a href="<?= url('account/profile') ?>" class="card text-decoration-none">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-person fs-2 mb-2"></i>
                                <h6 class="mb-0">Profil Ayarları</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= url('account/addresses') ?>" class="card text-decoration-none">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-geo-alt fs-2 mb-2"></i>
                                <h6 class="mb-0">Adres Yönetimi</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= url('products') ?>" class="card text-decoration-none">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-bag fs-2 mb-2"></i>
                                <h6 class="mb-0">Alışverişe Devam</h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-icon {
    width: 60px;
    height: 60px;
    background: var(--bg-secondary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.status-pending { background: #ffc107; color: #000; }
.status-confirmed { background: #17a2b8; }
.status-processing { background: #6c757d; }
.status-shipped { background: #007bff; }
.status-delivered { background: #28a745; }
.status-cancelled { background: #dc3545; }
</style>
