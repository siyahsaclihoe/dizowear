<!-- Checkout Success -->
<div class="checkout-result py-5">
    <div class="container">
        <div class="result-card success text-center">
            <div class="result-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1>Siparişiniz Alındı!</h1>
            <p class="lead">Teşekkür ederiz, siparişiniz başarıyla oluşturuldu.</p>
            
            <?php if ($order_number): ?>
                <div class="order-number-box">
                    <span>Sipariş Numaranız</span>
                    <strong><?= htmlspecialchars($order_number) ?></strong>
                </div>
            <?php endif; ?>
            
            <p class="text-muted">
                Sipariş detaylarınız e-posta adresinize gönderildi.<br>
                Siparişinizi "Hesabım" bölümünden takip edebilirsiniz.
            </p>
            
            <div class="mt-4">
                <?php if ($user): ?>
                    <a href="<?= url('account/orders') ?>" class="btn btn-dark me-2">
                        <i class="bi bi-box me-2"></i>Siparişlerim
                    </a>
                <?php endif; ?>
                <a href="<?= url('products') ?>" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left me-2"></i>Alışverişe Devam Et
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.result-card { max-width: 600px; margin: 0 auto; padding: 50px 30px; background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
.result-card.success .result-icon { color: #28a745; }
.result-icon { font-size: 80px; margin-bottom: 20px; }
.order-number-box { display: inline-block; background: #f8f9fa; padding: 15px 30px; border-radius: 8px; margin: 20px 0; }
.order-number-box span { display: block; font-size: 14px; color: #666; }
.order-number-box strong { font-size: 24px; }
</style>
