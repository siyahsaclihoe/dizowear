<!-- Account Orders Page -->
<div class="account-page section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../partials/account-sidebar.php'; ?>
            
            <!-- Content -->
            <div class="col-lg-9">
                <h3 class="mb-4">Siparişlerim</h3>
                
                <?php if (empty($orders)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <h5 class="mt-3">Henüz sipariş yok</h5>
                            <p class="text-muted">İlk siparişinizi verin!</p>
                            <a href="<?= url('products') ?>" class="btn btn-dark">
                                <i class="bi bi-bag me-2"></i>Alışverişe Başla
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Sipariş #<?= htmlspecialchars($order['order_number']) ?></strong>
                                        <span class="text-muted ms-2"><?= formatDate($order['created_at']) ?></span>
                                    </div>
                                    <span class="badge status-<?= $order['status'] ?>">
                                        <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Tutar:</strong> <?= formatPrice($order['total']) ?></p>
                                            <p class="mb-0">
                                                <strong>Ödeme:</strong>
                                                <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'failed' ? 'danger' : 'warning') ?>">
                                                    <?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                            <a href="<?= url('account/order/' . $order['id']) ?>" class="btn btn-outline-dark">
                                                Detay Görüntüle <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.status-pending { background: #ffc107; color: #000; }
.status-confirmed { background: #17a2b8; }
.status-processing { background: #6c757d; }
.status-shipped { background: #007bff; }
.status-delivered { background: #28a745; }
.status-cancelled { background: #dc3545; }
</style>
