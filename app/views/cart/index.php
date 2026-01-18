<!-- Cart Page -->
<div class="cart-page py-5">
    <div class="container">
        <h1 class="page-title mb-4">Sepetim</h1>
        
        <?php if (empty($cart)): ?>
            <div class="empty-cart text-center py-5">
                <i class="bi bi-bag-x display-1 text-muted"></i>
                <h4 class="mt-3">Sepetiniz Boş</h4>
                <p class="text-muted">Sepetinizde henüz ürün bulunmuyor.</p>
                <a href="<?= url('products') ?>" class="btn btn-dark btn-lg mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Alışverişe Başla
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-items">
                        <?php foreach ($cart as $key => $item): ?>
                            <div class="cart-item" data-cart-key="<?= $key ?>">
                                <div class="row align-items-center">
                                    <!-- Image -->
                                    <div class="col-3 col-md-2">
                                        <a href="<?= url('product/' . $item['slug']) ?>">
                                            <img src="<?= $item['image'] ? upload($item['image']) : asset('images/no-image.jpg') ?>" 
                                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                                 class="cart-item-img">
                                        </a>
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="col-9 col-md-4">
                                        <h5 class="cart-item-title">
                                            <a href="<?= url('product/' . $item['slug']) ?>">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </a>
                                        </h5>
                                        <div class="cart-item-meta">
                                            <span class="badge bg-secondary">Beden: <?= $item['size'] ?></span>
                                        </div>
                                        <div class="cart-item-price d-md-none mt-2">
                                            <?= formatPrice($item['price']) ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Price (Desktop) -->
                                    <div class="col-md-2 d-none d-md-block text-center">
                                        <span class="item-price"><?= formatPrice($item['price']) ?></span>
                                    </div>
                                    
                                    <!-- Quantity -->
                                    <div class="col-6 col-md-2">
                                        <div class="quantity-selector quantity-sm">
                                            <button type="button" class="qty-btn minus" onclick="updateCartQty('<?= $key ?>', -1)">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" value="<?= $item['quantity'] ?>" min="1" max="10" 
                                                   class="qty-input" id="qty-<?= $key ?>" readonly>
                                            <button type="button" class="qty-btn plus" onclick="updateCartQty('<?= $key ?>', 1)">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Total -->
                                    <div class="col-4 col-md-2 text-end">
                                        <span class="line-total" id="total-<?= $key ?>"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                                        <button type="button" class="btn-remove" onclick="removeCartItem('<?= $key ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-actions mt-4">
                        <a href="<?= url('products') ?>" class="btn btn-outline-dark">
                            <i class="bi bi-arrow-left me-2"></i>Alışverişe Devam Et
                        </a>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="summary-title">Sipariş Özeti</h4>
                        
                        <div class="summary-row">
                            <span>Ara Toplam</span>
                            <span id="cartSubtotal"><?= formatPrice($totals['subtotal']) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Kargo</span>
                            <span id="cartShipping">
                                <?php if ($totals['free_shipping']): ?>
                                    <span class="text-success">Ücretsiz</span>
                                <?php else: ?>
                                    <?= formatPrice($totals['shipping']) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <?php if (!$totals['free_shipping']): ?>
                            <div class="free-shipping-notice">
                                <i class="bi bi-truck me-2"></i>
                                Ücretsiz kargo için <?= formatPrice(500 - $totals['subtotal']) ?> daha ekleyin
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="summary-row summary-total">
                            <span>Toplam</span>
                            <span id="cartTotal"><?= formatPrice($totals['total']) ?></span>
                        </div>
                        
                        <!-- Coupon Code Section -->
                        <div class="coupon-section mt-3">
                            <div class="coupon-input-group" id="couponInputGroup">
                                <input type="text" id="couponCode" class="form-control" 
                                       placeholder="Kupon kodu girin" style="text-transform: uppercase;">
                                <button type="button" class="btn btn-outline-dark" id="applyCouponBtn" onclick="applyCoupon()">
                                    Uygula
                                </button>
                            </div>
                            <div id="couponMessage" class="coupon-message mt-2" style="display: none;"></div>
                            <div id="couponApplied" class="coupon-applied mt-2" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="coupon-badge">
                                        <i class="bi bi-ticket-perforated me-1"></i>
                                        <span id="appliedCouponCode"></span>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeCoupon()">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="coupon-discount text-success">
                                    -<span id="couponDiscount"></span>
                                </div>
                            </div>
                        </div>
                        
                        <a href="<?= url('checkout') ?>" class="btn btn-dark btn-lg w-100 mt-4">
                            <i class="bi bi-credit-card me-2"></i>Ödemeye Geç
                        </a>
                        
                        <div class="payment-icons mt-3 text-center">
                            <img src="<?= asset('images/payment-methods.png') ?>" alt="Ödeme Yöntemleri" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let appliedCoupon = null;

function updateCartQty(cartKey, delta) {
    const input = document.getElementById('qty-' + cartKey);
    let newQty = parseInt(input.value) + delta;
    if (newQty < 1) newQty = 1;
    if (newQty > 10) newQty = 10;
    
    input.value = newQty;
    
    const formData = new FormData();
    formData.append('cart_key', cartKey);
    formData.append('quantity', newQty);
    formData.append('csrf_token', CONFIG.csrfToken);
    
    fetch(CONFIG.baseUrl + '/cart/update', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('total-' + cartKey).textContent = data.line_total;
            document.getElementById('cartSubtotal').textContent = data.subtotal;
            document.getElementById('cartShipping').innerHTML = data.free_shipping ? '<span class="text-success">Ücretsiz</span>' : data.shipping;
            document.getElementById('cartTotal').textContent = data.total;
            document.getElementById('cartCount').textContent = data.cart_count;
            
            // Re-validate coupon if applied
            if (appliedCoupon) {
                applyCoupon();
            }
        }
    });
}

function removeCartItem(cartKey) {
    if (!confirm('Bu ürünü sepetten kaldırmak istediğinize emin misiniz?')) return;
    
    const formData = new FormData();
    formData.append('cart_key', cartKey);
    formData.append('csrf_token', CONFIG.csrfToken);
    
    fetch(CONFIG.baseUrl + '/cart/remove', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.querySelector('[data-cart-key="' + cartKey + '"]').remove();
            document.getElementById('cartCount').textContent = data.cart_count;
            
            if (data.cart_count === 0) {
                location.reload();
            }
        }
    });
}

function applyCoupon() {
    const code = document.getElementById('couponCode').value.trim().toUpperCase();
    if (!code) {
        showCouponMessage('Lütfen bir kupon kodu girin.', 'error');
        return;
    }
    
    const btn = document.getElementById('applyCouponBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    const formData = new FormData();
    formData.append('code', code);
    formData.append('csrf_token', CONFIG.csrfToken);
    
    fetch(CONFIG.baseUrl + '/cart/apply-coupon', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = 'Uygula';
        
        if (data.valid) {
            appliedCoupon = data.coupon;
            document.getElementById('couponInputGroup').style.display = 'none';
            document.getElementById('couponMessage').style.display = 'none';
            document.getElementById('couponApplied').style.display = 'block';
            document.getElementById('appliedCouponCode').textContent = code;
            document.getElementById('couponDiscount').textContent = data.discount_formatted;
            document.getElementById('cartTotal').textContent = data.new_total;
            showToast('Kupon uygulandı!', 'success');
        } else {
            showCouponMessage(data.message, 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = 'Uygula';
        showCouponMessage('Bir hata oluştu, tekrar deneyin.', 'error');
    });
}

function removeCoupon() {
    appliedCoupon = null;
    sessionStorage.removeItem('coupon');
    
    document.getElementById('couponInputGroup').style.display = 'flex';
    document.getElementById('couponApplied').style.display = 'none';
    document.getElementById('couponCode').value = '';
    
    // Reload cart totals
    location.reload();
}

function showCouponMessage(message, type) {
    const el = document.getElementById('couponMessage');
    el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-1"></i>${message}`;
    el.className = `coupon-message mt-2 ${type === 'success' ? 'text-success' : 'text-danger'}`;
    el.style.display = 'block';
}
</script>
