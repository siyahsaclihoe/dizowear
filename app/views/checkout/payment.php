<!-- Checkout Payment Page - Production -->
<?php 
// Direct payment iframe page without layout
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Güvenli Ödeme | <?= $settings['site_name'] ?? 'Dızo Wear' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #1a1a1a 0%, #000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }
        .payment-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .payment-header {
            background: #000;
            color: #fff;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .payment-header h4 { margin: 0; font-weight: 600; }
        .order-number { font-size: 14px; opacity: 0.8; }
        .payment-body { padding: 0; }
        .payment-iframe {
            width: 100%;
            height: 500px;
            border: none;
        }
        .payment-footer {
            padding: 20px 25px;
            background: #f8f9fa;
            text-align: center;
        }
        .total-amount {
            font-size: 24px;
            font-weight: 700;
        }
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #28a745;
            font-size: 13px;
            margin-top: 10px;
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 14px;
        }
        .back-link a:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-card">
            <div class="payment-header">
                <div>
                    <h4><i class="bi bi-shield-lock me-2"></i>Güvenli Ödeme</h4>
                    <span class="order-number">Sipariş: #<?= htmlspecialchars($order['order_number']) ?></span>
                </div>
                <div class="text-end">
                    <span class="total-amount"><?= formatPrice($order['total']) ?></span>
                </div>
            </div>
            
            <div class="payment-body position-relative">
                <?php if (!empty($payment_url) && strpos($payment_url, 'paytr.com') !== false): ?>
                    <!-- PayTR iFrame -->
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="spinner"></div>
                        <p class="mt-3 text-muted">Ödeme formu yükleniyor...</p>
                    </div>
                    <iframe src="<?= htmlspecialchars($payment_url) ?>" 
                            class="payment-iframe" 
                            id="paymentFrame"
                            onload="document.getElementById('loadingOverlay').style.display='none'"
                            allowfullscreen></iframe>
                <?php elseif (!empty($payment_url) && strpos($payment_url, 'demo-payment') !== false): ?>
                    <!-- Demo Mode -->
                    <div class="p-5 text-center">
                        <i class="bi bi-exclamation-triangle text-warning display-3"></i>
                        <h4 class="mt-3">Test Modu Aktif</h4>
                        <p class="text-muted">Ödeme API'leri henüz yapılandırılmamış.</p>
                        <p class="mb-4">Lütfen Admin Paneli → Ödeme Ayarları → PayTR veya İyzico bilgilerini girin.</p>
                        <a href="<?= htmlspecialchars($payment_url) ?>" class="btn btn-dark btn-lg">
                            <i class="bi bi-play-circle me-2"></i>Demo Ödeme
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Error -->
                    <div class="p-5 text-center">
                        <i class="bi bi-x-circle text-danger display-3"></i>
                        <h4 class="mt-3">Ödeme Başlatılamadı</h4>
                        <p class="text-muted">Ödeme sistemi şu anda kullanılamıyor. Lütfen daha sonra tekrar deneyin.</p>
                        <a href="<?= url('checkout') ?>" class="btn btn-outline-dark">
                            <i class="bi bi-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="payment-footer">
                <div class="security-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>256-bit SSL ile korunan güvenli ödeme</span>
                </div>
                <div class="mt-3">
                    <img src="<?= asset('images/payment-methods.png') ?>" alt="Ödeme Yöntemleri" style="height: 25px; opacity: 0.6;">
                </div>
            </div>
        </div>
        
        <div class="back-link">
            <a href="<?= url('checkout') ?>"><i class="bi bi-arrow-left me-1"></i>Siparişe geri dön</a>
        </div>
    </div>
    
    <script>
    // Iframe resize for mobile
    function resizeIframe() {
        const iframe = document.getElementById('paymentFrame');
        if (iframe && window.innerWidth < 768) {
            iframe.style.height = (window.innerHeight - 200) + 'px';
        }
    }
    window.addEventListener('resize', resizeIframe);
    resizeIframe();
    </script>
</body>
</html>
