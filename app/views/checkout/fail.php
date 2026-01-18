<!-- Checkout Fail -->
<div class="checkout-result py-5">
    <div class="container">
        <div class="result-card fail text-center">
            <div class="result-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <h1>Ödeme Başarısız</h1>
            <p class="lead">Maalesef ödeme işleminiz tamamlanamadı.</p>
            
            <p class="text-muted">
                Lütfen kart bilgilerinizi kontrol edip tekrar deneyin.<br>
                Sorun devam ederse farklı bir ödeme yöntemi deneyebilirsiniz.
            </p>
            
            <div class="mt-4">
                <a href="<?= url('checkout') ?>" class="btn btn-dark me-2">
                    <i class="bi bi-arrow-repeat me-2"></i>Tekrar Dene
                </a>
                <a href="<?= url('cart') ?>" class="btn btn-outline-dark">
                    <i class="bi bi-bag me-2"></i>Sepete Dön
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.result-card { max-width: 600px; margin: 0 auto; padding: 50px 30px; background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
.result-card.fail .result-icon { color: #dc3545; }
.result-icon { font-size: 80px; margin-bottom: 20px; }
</style>
