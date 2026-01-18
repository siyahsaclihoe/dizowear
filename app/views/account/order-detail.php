<!-- Account Order Detail Page -->
<div class="account-page section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../partials/account-sidebar.php'; ?>
            
            <!-- Content -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="<?= url('account/orders') ?>" class="btn btn-sm btn-outline-dark mb-2">
                            <i class="bi bi-arrow-left me-1"></i>Siparişlere Dön
                        </a>
                        <h3 class="mb-0">Sipariş #<?= htmlspecialchars($order['order_number']) ?></h3>
                    </div>
                    <span class="badge fs-6 status-<?= $order['status'] ?>">
                        <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                    </span>
                </div>
                
                <div class="row g-4">
                    <!-- Order Info -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-box me-2"></i>Sipariş Kalemleri</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Beden</th>
                                                <th>Birim</th>
                                                <th>Adet</th>
                                                <th class="text-end">Toplam</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <?php if (!empty($item['image'])): ?>
                                                                <img src="<?= upload($item['image']) ?>" 
                                                                     width="50" height="50" 
                                                                     class="rounded" 
                                                                     style="object-fit: cover;">
                                                            <?php endif; ?>
                                                            <span><?= htmlspecialchars($item['product_name']) ?></span>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-secondary"><?= $item['size'] ?></span></td>
                                                    <td><?= formatPrice($item['price']) ?></td>
                                                    <td><?= $item['quantity'] ?></td>
                                                    <td class="text-end fw-bold"><?= formatPrice($item['total']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Address -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Teslimat Adresi</h5>
                            </div>
                            <div class="card-body">
                                <pre class="mb-0" style="font-family: inherit; white-space: pre-wrap;"><?= htmlspecialchars($order['shipping_address']) ?></pre>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Özet</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ara Toplam</span>
                                    <span><?= formatPrice($order['subtotal']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Kargo</span>
                                    <span><?= $order['shipping_cost'] > 0 ? formatPrice($order['shipping_cost']) : 'Ücretsiz' ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Toplam</strong>
                                    <strong class="fs-5"><?= formatPrice($order['total']) ?></strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Sipariş Tarihi</small>
                                    <div><?= formatDate($order['created_at'], 'd.m.Y H:i') ?></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Ödeme Durumu</small>
                                    <div>
                                        <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'failed' ? 'danger' : 'warning') ?>">
                                            <?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Ödeme Yöntemi</small>
                                    <div><?= $order['payment_method'] === 'credit_card' ? 'Kredi Kartı' : $order['payment_method'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-pending { background: #ffc107; color: #000; }
.status-confirmed { background: #17a2b8; color: #fff; }
.status-processing { background: #6c757d; color: #fff; }
.status-shipped { background: #007bff; color: #fff; }
.status-delivered { background: #28a745; color: #fff; }
.status-cancelled { background: #dc3545; color: #fff; }
</style>
