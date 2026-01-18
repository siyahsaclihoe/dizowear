<!-- Homepage -->
<section class="hero-slider">
    <?php if (!empty($sliders)): ?>
        <!-- Dynamic Slider -->
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($sliders as $i => $slider): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <div class="hero-slide" style="background-image: url('<?= asset('uploads/' . $slider['image']) ?>');">
                            <div class="hero-overlay"></div>
                            <div class="container">
                                <div class="hero-content fade-in">
                                    <span class="hero-subtitle"><?= htmlspecialchars($slider['subtitle'] ?? 'YENİ KOLEKSİYON') ?></span>
                                    <h1 class="hero-title"><?= htmlspecialchars($slider['title']) ?></h1>
                                    <?php if ($slider['button_text']): ?>
                                        <a href="<?= htmlspecialchars($slider['button_link'] ?? url('products')) ?>" class="btn-hero btn-glow">
                                            <?= htmlspecialchars($slider['button_text']) ?>
                                            <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Default Hero -->
        <div class="hero-slide hero-default">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-content fade-in">
                    <span class="hero-subtitle">2026 KOLEKSİYONU</span>
                    <h1 class="hero-title">TARZINI<br>YANSIT</h1>
                    <a href="<?= url('products') ?>" class="btn-hero btn-glow">
                        Koleksiyonu Keşfet
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Marquee -->
<section class="marquee-section">
    <div class="marquee">
        <div class="marquee-content">
            <span>Ücretsiz Kargo</span>
            <span>Güvenli Ödeme</span>
            <span>Kolay İade</span>
            <span>7/24 Destek</span>
            <span>Premium Kalite</span>
            <span>Hızlı Teslimat</span>
            <span>Ücretsiz Kargo</span>
            <span>Güvenli Ödeme</span>
            <span>Kolay İade</span>
            <span>7/24 Destek</span>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-subtitle">ÖNE ÇIKANLAR</span>
            <h2 class="section-title">En Çok Tercih Edilenler</h2>
        </div>
        
        <div class="row g-4 stagger">
            <?php if (!empty($featured)): ?>
                <?php foreach ($featured as $product): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <?php include __DIR__ . '/../partials/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-box display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Henüz öne çıkan ürün yok</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?= url('products') ?>" class="btn btn-outline-dark btn-lg">
                <span>Tümünü Gör</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Promo Banners -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="promo-card" style="background: var(--accent); color: var(--bg-primary); padding: 60px 40px; border-radius: var(--radius-lg); position: relative; overflow: hidden;">
                    <span style="font-size: 12px; letter-spacing: 3px; opacity: 0.8;">LİMİTED EDİTİON</span>
                    <h3 style="font-size: 32px; font-weight: 800; margin: 15px 0;">Yeni Sezon<br>Ürünleri</h3>
                    <a href="<?= url('products') ?>" class="btn" style="background: var(--bg-primary); color: var(--accent); margin-top: 10px;">
                        Keşfet <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="promo-card" style="background: var(--bg-primary); border: 2px solid var(--border-color); padding: 60px 40px; border-radius: var(--radius-lg);">
                    <span style="font-size: 12px; letter-spacing: 3px; color: var(--text-muted);">ÖZEL FIRSATLAR</span>
                    <h3 style="font-size: 32px; font-weight: 800; margin: 15px 0;">%30'a Varan<br>İndirimler</h3>
                    <a href="<?= url('products') ?>" class="btn btn-dark" style="margin-top: 10px;">
                        Alışverişe Başla <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-subtitle">YENİ GELENLER</span>
            <h2 class="section-title">Son Eklenen Ürünler</h2>
        </div>
        
        <div class="row g-4 stagger">
            <?php if (!empty($newArrivals)): ?>
                <?php foreach ($newArrivals as $product): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <?php include __DIR__ . '/../partials/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-box display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Henüz ürün yok</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon" style="font-size: 40px; margin-bottom: 15px;">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h6 style="font-weight: 700;">Ücretsiz Kargo</h6>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">500 TL üzeri siparişlerde</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon" style="font-size: 40px; margin-bottom: 15px;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h6 style="font-weight: 700;">Kolay İade</h6>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">14 gün içinde iade</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon" style="font-size: 40px; margin-bottom: 15px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6 style="font-weight: 700;">Güvenli Ödeme</h6>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">256-bit SSL koruma</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon" style="font-size: 40px; margin-bottom: 15px;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h6 style="font-weight: 700;">7/24 Destek</h6>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Canlı destek hattı</p>
                </div>
            </div>
        </div>
    </div>
</section>
