<!-- Account Profile Page -->
<div class="account-page section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../partials/account-sidebar.php'; ?>
            
            <!-- Content -->
            <div class="col-lg-9">
                <h3 class="mb-4">Profil Ayarları</h3>
                
                <!-- Profile Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Kişisel Bilgiler</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= url('account/update-profile') ?>" method="POST">
                            <?= csrfField() ?>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ad Soyad *</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">E-posta</label>
                                    <input type="email" class="form-control" 
                                           value="<?= htmlspecialchars($profile['email'] ?? '') ?>" disabled>
                                    <small class="text-muted">E-posta değiştirilemez</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefon</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"
                                           placeholder="5XX XXX XX XX">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-dark">
                                    <i class="bi bi-check-circle me-2"></i>Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Password Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Şifre Değiştir</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= url('account/change-password') ?>" method="POST">
                            <?= csrfField() ?>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Mevcut Şifre *</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Yeni Şifre *</label>
                                    <input type="password" name="new_password" class="form-control" 
                                           minlength="6" required>
                                    <small class="text-muted">En az 6 karakter</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Yeni Şifre (Tekrar) *</label>
                                    <input type="password" name="confirm_password" class="form-control" 
                                           minlength="6" required>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-outline-dark">
                                    <i class="bi bi-key me-2"></i>Şifreyi Değiştir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
