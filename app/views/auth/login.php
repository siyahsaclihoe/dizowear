<!-- Login Page -->
<div class="auth-page py-5">
    <div class="container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <h2>Giriş Yap</h2>
                <p class="text-muted">Hesabınıza giriş yapın</p>
            </div>
            
            <form action="<?= url('login') ?>" method="POST">
                <?= csrfField() ?>
                
                <div class="mb-3">
                    <label class="form-label">E-posta</label>
                    <input type="email" name="email" class="form-control form-control-lg" required
                           placeholder="ornek@email.com">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Şifre</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control form-control-lg" 
                               id="password" required placeholder="••••••••">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input">
                        <span class="form-check-label">Beni hatırla</span>
                    </label>
                    <a href="<?= url('forgot-password') ?>" class="text-muted">Şifremi unuttum</a>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-dark btn-lg">Giriş Yap</button>
                </div>
            </form>
            
            <hr class="my-4">
            
            <p class="text-center mb-0">
                Hesabınız yok mu? 
                <a href="<?= url('register') ?>" class="fw-bold">Kayıt Ol</a>
            </p>
        </div>
    </div>
</div>

<style>
.auth-page { min-height: 60vh; display: flex; align-items: center; }
.auth-card { max-width: 450px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
</style>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
