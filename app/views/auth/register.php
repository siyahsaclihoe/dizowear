<!-- Register Page -->
<div class="auth-page py-5">
    <div class="container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <h2>Kayıt Ol</h2>
                <p class="text-muted">Yeni hesap oluşturun</p>
            </div>
            
            <form action="<?= url('register') ?>" method="POST">
                <?= csrfField() ?>
                
                <div class="mb-3">
                    <label class="form-label">Ad Soyad *</label>
                    <input type="text" name="name" class="form-control form-control-lg" required
                           placeholder="Adınız Soyadınız">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">E-posta *</label>
                    <input type="email" name="email" class="form-control form-control-lg" required
                           placeholder="ornek@email.com">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="tel" name="phone" class="form-control form-control-lg"
                           placeholder="05XX XXX XX XX">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Şifre *</label>
                    <input type="password" name="password" class="form-control form-control-lg" required
                           placeholder="En az 6 karakter" minlength="6">
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Şifre Tekrar *</label>
                    <input type="password" name="password_confirm" class="form-control form-control-lg" required
                           placeholder="Şifrenizi tekrar girin">
                </div>
                
                <div class="mb-4">
                    <label class="form-check">
                        <input type="checkbox" name="terms" class="form-check-input" required>
                        <span class="form-check-label">
                            <a href="#" target="_blank">Kullanım koşullarını</a> okudum ve kabul ediyorum.
                        </span>
                    </label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-dark btn-lg">Kayıt Ol</button>
                </div>
            </form>
            
            <hr class="my-4">
            
            <p class="text-center mb-0">
                Zaten hesabınız var mı? 
                <a href="<?= url('login') ?>" class="fw-bold">Giriş Yap</a>
            </p>
        </div>
    </div>
</div>

<style>
.auth-page { min-height: 60vh; display: flex; align-items: center; }
.auth-card { max-width: 450px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
</style>
