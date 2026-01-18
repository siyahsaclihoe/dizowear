<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Ödeme | Dızo Wear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; font-family: 'Inter', sans-serif; }
        .payment-container { max-width: 500px; margin: 50px auto; }
        .payment-card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .logo { font-size: 24px; font-weight: 800; margin-bottom: 20px; }
        .logo span { color: #666; }
        .order-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .test-notice { background: #fff3cd; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-card">
            <div class="text-center">
                <div class="logo">DIZO<span>WEAR</span></div>
                <h4>Demo Ödeme Sayfası</h4>
            </div>
            
            <div class="test-notice text-center">
                <strong>⚠️ Test Modu</strong><br>
                Bu bir demo ödeme sayfasıdır. Gerçek bir ödeme işlemi yapılmayacaktır.
            </div>
            
            <div class="order-info">
                <div class="d-flex justify-content-between mb-2">
                    <span>Sipariş No:</span>
                    <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Toplam Tutar:</span>
                    <strong><?= formatPrice($order['total']) ?></strong>
                </div>
            </div>
            
            <form action="<?= url('checkout/process-demo') ?>" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Kart Numarası (Demo)</label>
                    <input type="text" class="form-control" value="4111 1111 1111 1111" readonly>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Son Kullanma</label>
                        <input type="text" class="form-control" value="12/26" readonly>
                    </div>
                    <div class="col-6">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-control" value="123" readonly>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="action" value="success" class="btn btn-success btn-lg">
                        ✓ Ödemeyi Onayla (Başarılı)
                    </button>
                    <button type="submit" name="action" value="fail" class="btn btn-outline-danger">
                        ✗ Ödemeyi İptal Et (Başarısız)
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
