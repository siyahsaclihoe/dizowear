<!-- Account Sidebar Partial -->
<div class="col-lg-3">
    <div class="account-sidebar">
        <div class="user-info text-center mb-4">
            <div class="avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <h5 class="mt-3"><?= htmlspecialchars($user['name'] ?? 'Kullanıcı') ?></h5>
            <small class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></small>
        </div>
        
        <nav class="account-nav">
            <a href="<?= url('account') ?>" class="nav-link <?= isActive('account$') ?>">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="<?= url('account/orders') ?>" class="nav-link <?= isActive('orders') ?>">
                <i class="bi bi-box"></i> Siparişlerim
            </a>
            <a href="<?= url('account/addresses') ?>" class="nav-link <?= isActive('addresses') ?>">
                <i class="bi bi-geo-alt"></i> Adreslerim
            </a>
            <a href="<?= url('account/profile') ?>" class="nav-link <?= isActive('profile') ?>">
                <i class="bi bi-person"></i> Profil
            </a>
            <a href="<?= url('logout') ?>" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
            </a>
        </nav>
    </div>
</div>

<style>
.account-sidebar { 
    background: var(--bg-secondary); 
    padding: 30px; 
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}
.user-info .avatar { 
    font-size: 60px; 
    color: var(--text-muted); 
}
.user-info h5 {
    color: var(--text-primary);
}
.account-nav .nav-link { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    padding: 14px 18px; 
    color: var(--text-secondary); 
    border-radius: var(--radius); 
    margin-bottom: 5px;
    transition: var(--transition);
}
.account-nav .nav-link:hover, 
.account-nav .nav-link.active { 
    background: var(--bg-primary);
    color: var(--text-primary);
}
.account-nav .nav-link.active {
    font-weight: 600;
}
</style>
