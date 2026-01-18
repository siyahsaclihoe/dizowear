<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($settings['site_description'] ?? 'Premium Streetwear Giyim') ?>">
    <title><?= isset($title) ? htmlspecialchars($title) . ' | ' : '' ?><?= htmlspecialchars($settings['site_name'] ?? 'Dızo Wear') ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/favicon.png') ?>">
    
    <!-- Preloader Styles -->
    <style>
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .page-loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        
        .loader-brand {
            font-size: 48px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -2px;
            position: relative;
            animation: brandPulse 2s ease-in-out infinite;
        }
        
        .loader-brand span {
            color: #666;
        }
        
        @keyframes brandPulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.02); }
        }
        
        .loader-line {
            width: 200px;
            height: 3px;
            background: #333;
            margin-top: 30px;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }
        
        .loader-line::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 40%;
            background: linear-gradient(90deg, transparent, #fff, transparent);
            animation: lineMove 1.5s ease-in-out infinite;
        }
        
        @keyframes lineMove {
            0% { left: -40%; }
            100% { left: 100%; }
        }
        
        .loader-text {
            color: #666;
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 20px;
            animation: textFade 2s ease-in-out infinite;
        }
        
        @keyframes textFade {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        /* Loader particles */
        .loader-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .loader-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #fff;
            border-radius: 50%;
            opacity: 0.1;
        }
    </style>
</head>
<body>
    <!-- Page Loader - Brand Name From Settings -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-particles" id="loaderParticles"></div>
        <div class="loader-brand">
            <?php 
            $siteName = $settings['site_name'] ?? 'DIZOWEAR';
            // Split name for styling (e.g., "DIZO WEAR" -> "DIZO" + "WEAR")
            $parts = explode(' ', strtoupper($siteName));
            if (count($parts) >= 2) {
                echo htmlspecialchars($parts[0]) . '<span>' . htmlspecialchars($parts[1]) . '</span>';
            } else {
                // Single word - try to split at middle
                $len = strlen($siteName);
                $mid = ceil($len / 2);
                echo htmlspecialchars(substr($siteName, 0, $mid)) . '<span>' . htmlspecialchars(substr($siteName, $mid)) . '</span>';
            }
            ?>
        </div>
        <div class="loader-line"></div>
        <div class="loader-text">Yükleniyor</div>
    </div>
    
    <!-- Particles Background -->
    <div id="particles-js"></div>
    
    <!-- Cursor Glow Effect -->
    <div class="cursor-glow" id="cursorGlow"></div>
    
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="container">
            <div class="announcement-content">
                <span><i class="bi bi-truck me-2"></i>500 TL ve üzeri siparişlerde ücretsiz kargo!</span>
                <span class="d-none d-md-inline"><i class="bi bi-shield-check me-2"></i>Güvenli Ödeme</span>
                <span class="d-none d-lg-inline"><i class="bi bi-arrow-repeat me-2"></i>14 Gün İade</span>
            </div>
        </div>
    </div>
    
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand logo-text" href="<?= url('/') ?>">
                    <?php 
                    $parts = explode(' ', strtoupper($settings['site_name'] ?? 'DIZO WEAR'));
                    echo htmlspecialchars($parts[0] ?? 'DIZO');
                    if (isset($parts[1])) echo '<span class="logo-accent">' . htmlspecialchars($parts[1]) . '</span>';
                    ?>
                </a>
                
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <i class="bi bi-list fs-4"></i>
                </button>
                
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('/') ?>" href="<?= url('/') ?>">Ana Sayfa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('/products') ?>" href="<?= url('products') ?>">Ürünler</a>
                        </li>
                        <?php if (!empty($categories)): ?>
                            <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= url('category/' . $cat['slug']) ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="navbar-actions d-flex align-items-center gap-2">
                    <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Arama">
                        <i class="bi bi-search"></i>
                    </button>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown">
                            <button class="btn-icon dropdown-toggle" data-bs-toggle="dropdown" aria-label="Hesap">
                                <i class="bi bi-person"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text fw-bold"><?= htmlspecialchars($_SESSION['user']['name']) ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('account') ?>"><i class="bi bi-grid me-2"></i>Hesabım</a></li>
                                <li><a class="dropdown-item" href="<?= url('account/orders') ?>"><i class="bi bi-box me-2"></i>Siparişlerim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Çıkış</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn-icon" aria-label="Giriş">
                            <i class="bi bi-person"></i>
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= url('cart') ?>" class="btn-icon btn-cart" aria-label="Sepet">
                        <i class="bi bi-bag"></i>
                        <span class="cart-count" id="cartCount"><?= getCartCount() ?></span>
                    </a>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash-message alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'] ?>">
            <div class="container">
                <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                <button type="button" class="btn-close-flash" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main>
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <div class="footer-widget">
                            <h4 class="footer-brand mb-4">
                                <?php 
                                $parts = explode(' ', strtoupper($settings['site_name'] ?? 'DIZO WEAR'));
                                echo htmlspecialchars($parts[0] ?? 'DIZO');
                                if (isset($parts[1])) echo '<span>' . htmlspecialchars($parts[1]) . '</span>';
                                ?>
                            </h4>
                            <p class="footer-desc">Premium streetwear giyim markası. Kalite ve tarzı bir arada sunuyoruz.</p>
                            <div class="social-links">
                                <a href="<?= $settings['social_instagram'] ?? '#' ?>" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                                <a href="<?= $settings['social_twitter'] ?? '#' ?>" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                                <a href="<?= $settings['social_youtube'] ?? '#' ?>" class="social-link" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                                <a href="<?= $settings['social_tiktok'] ?? '#' ?>" class="social-link" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="footer-widget">
                            <h5>Alışveriş</h5>
                            <ul class="footer-links">
                                <li><a href="<?= url('products') ?>">Tüm Ürünler</a></li>
                                <li><a href="<?= url('products?sort=newest') ?>">Yeni Gelenler</a></li>
                                <li><a href="<?= url('products?sale=1') ?>">İndirimler</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="footer-widget">
                            <h5>Yardım</h5>
                            <ul class="footer-links">
                                <li><a href="<?= url('account/orders') ?>">Sipariş Takibi</a></li>
                                <li><a href="<?= url('about') ?>">Hakkımızda</a></li>
                                <li><a href="<?= url('contact') ?>">İletişim</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="footer-widget">
                            <h5>Bülten</h5>
                            <p class="footer-desc">Kampanya ve fırsatlardan haberdar ol!</p>
                            <form class="newsletter-form" id="newsletterForm">
                                <input type="email" placeholder="E-posta adresin" required>
                                <button type="submit"><i class="bi bi-send"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'Dızo Wear') ?>. Tüm hakları saklıdır.</p>
                    <div class="footer-bottom-links">
                        <a href="#">Gizlilik Politikası</a>
                        <a href="#">Kullanım Şartları</a>
                        <a href="#">KVKK</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content search-modal-content">
                <div class="modal-body p-4">
                    <div class="search-container">
                        <div class="search-input-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchInput" placeholder="Ürün ara..." autofocus>
                            <kbd>ESC</kbd>
                        </div>
                        <div id="searchResults" class="search-results"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Theme Toggle -->
    <button class="theme-toggle" id="themeToggle" title="Tema Değiştir" aria-label="Tema Değiştir">
        <i class="bi bi-moon-fill icon-moon"></i>
        <i class="bi bi-sun-fill icon-sun"></i>
    </button>
    
    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toastContainer"></div>
    
    <!-- Scroll to Top -->
    <button class="scroll-top" id="scrollTop" aria-label="Yukarı Git">
        <i class="bi bi-chevron-up"></i>
    </button>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.CONFIG = {
            baseUrl: '<?= rtrim(url(''), '/') ?>',
            csrfToken: '<?= $_SESSION['csrf_token'] ?? '' ?>',
            siteName: '<?= addslashes($settings['site_name'] ?? 'Dızo Wear') ?>'
        };
        
        // Generate loader particles
        (function() {
            const container = document.getElementById('loaderParticles');
            if (!container) return;
            for (let i = 0; i < 20; i++) {
                const p = document.createElement('div');
                p.className = 'loader-particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.top = Math.random() * 100 + '%';
                p.style.animationDelay = Math.random() * 5 + 's';
                p.style.animation = `float ${10 + Math.random() * 20}s infinite`;
                container.appendChild(p);
            }
        })();
    </script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
