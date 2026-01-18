<!-- Account Addresses Page -->
<div class="account-page section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../partials/account-sidebar.php'; ?>
            
            <!-- Content -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">Adreslerim</h3>
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="bi bi-plus-circle me-2"></i>Yeni Adres
                    </button>
                </div>
                
                <?php if (empty($addresses)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-geo-alt display-4 text-muted"></i>
                            <h5 class="mt-3">Kayıtlı adresiniz yok</h5>
                            <p class="text-muted">Hızlı alışveriş için adres ekleyin.</p>
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="bi bi-plus-circle me-2"></i>Adres Ekle
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($addresses as $address): ?>
                            <div class="col-md-6">
                                <div class="card h-100 <?= $address['is_default'] ? 'border-dark' : '' ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h6 class="mb-0">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($address['title']) ?>
                                            </h6>
                                            <?php if ($address['is_default']): ?>
                                                <span class="badge bg-dark">Varsayılan</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p class="mb-1"><strong><?= htmlspecialchars($address['name']) ?></strong></p>
                                        <p class="mb-1 text-muted small">
                                            <?= htmlspecialchars($address['address']) ?><br>
                                            <?= htmlspecialchars($address['neighborhood'] ?? '') ?> 
                                            <?= htmlspecialchars($address['district']) ?>/<?= htmlspecialchars($address['city']) ?>
                                        </p>
                                        <p class="mb-0"><small><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($address['phone']) ?></small></p>
                                    </div>
                                    <div class="card-footer bg-transparent d-flex gap-2">
                                        <?php if (!$address['is_default']): ?>
                                            <a href="<?= url('account/address/default/' . $address['id']) ?>" 
                                               class="btn btn-sm btn-outline-dark">
                                                <i class="bi bi-star me-1"></i>Varsayılan Yap
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= url('account/address/delete/' . $address['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bu adresi silmek istediğinize emin misiniz?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
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

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-geo-alt me-2"></i>Yeni Adres Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('account/address/add') ?>" method="POST">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Adres Başlığı *</label>
                            <input type="text" name="title" class="form-control" placeholder="Ev, İş vb." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ad Soyad *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Telefon *</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">İl *</label>
                            <select name="city" id="citySelect" class="form-select" required>
                                <option value="">İl seçin</option>
                                <?php if (!empty($cities)): ?>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= htmlspecialchars($city['name']) ?>">
                                            <?= htmlspecialchars($city['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">İlçe *</label>
                            <input type="text" name="district" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mahalle</label>
                            <input type="text" name="neighborhood" class="form-control">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Açık Adres *</label>
                            <textarea name="address" class="form-control" rows="2" required 
                                      placeholder="Sokak, bina no, daire no..."></textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Posta Kodu</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-check-circle me-2"></i>Adresi Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
