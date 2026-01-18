<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> | Dızo Wear Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <a href="index.php" class="sidebar-logo">DIZO<span>WEAR</span></a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-label">Ana Menü</div>
                
                <div class="nav-item">
                    <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-label">E-Ticaret</div>
                
                <div class="nav-item">
                    <a href="products.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'product') !== false ? 'active' : '' ?>">
                        <i class="bi bi-box-seam"></i>
                        <span>Ürünler</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="categories.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>">
                        <i class="bi bi-folder"></i>
                        <span>Kategoriler</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="orders.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'order') !== false ? 'active' : '' ?>">
                        <i class="bi bi-receipt"></i>
                        <span>Siparişler</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="customers.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'customers.php' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        <span>Müşteriler</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="coupons.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'coupons.php' ? 'active' : '' ?>">
                        <i class="bi bi-ticket-perforated"></i>
                        <span>Kuponlar</span>
                    </a>
                </div>
                
                <div class="nav-label">Görünüm</div>
                
                <div class="nav-item">
                    <a href="sliders.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'sliders.php' ? 'active' : '' ?>">
                        <i class="bi bi-images"></i>
                        <span>Sliderlar</span>
                    </a>
                </div>
                
                <div class="nav-label">Ayarlar</div>
                
                <div class="nav-item">
                    <a href="payments.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'payments.php' ? 'active' : '' ?>">
                        <i class="bi bi-credit-card"></i>
                        <span>Ödeme Ayarları</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="settings.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>">
                        <i class="bi bi-gear"></i>
                        <span>Site Ayarları</span>
                    </a>
                </div>
                
                <div class="nav-item mt-4">
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Çıkış Yap</span>
                    </a>
                </div>
            </nav>
            
            <!-- User Info -->
            <div class="admin-user">
                <div class="admin-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <div class="admin-user-info">
                    <div class="admin-user-name"><?= htmlspecialchars($_SESSION['admin']['name'] ?? 'Admin') ?></div>
                    <div class="admin-user-role">Yönetici</div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <div class="admin-header">
                <div>
                    <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
                    <p class="page-subtitle"><?= date('d F Y, l') ?></p>
                </div>
                <div class="d-flex gap-3">
                    <a href="../" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i>
                        <span class="d-none d-md-inline">Siteyi Görüntüle</span>
                    </a>
                </div>
            </div>
            
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'] ?> fade-in">
                    <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
