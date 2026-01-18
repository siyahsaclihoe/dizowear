<!-- Checkout Page -->
<div class="checkout-page py-5">
    <div class="container">
        <h1 class="page-title mb-4">Ödeme</h1>
        
        <form id="checkoutForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="row g-4">
                <!-- Checkout Form -->
                <div class="col-lg-8">
                    <!-- Contact Info -->
                    <div class="checkout-section">
                        <h4 class="section-title"><i class="bi bi-person me-2"></i>İletişim Bilgileri</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ad Soyad *</label>
                                <input type="text" name="name" class="form-control" required
                                       value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-posta *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefon *</label>
                                <input type="tel" name="phone" class="form-control" required
                                       placeholder="05XX XXX XX XX"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div class="checkout-section">
                        <h4 class="section-title"><i class="bi bi-geo-alt me-2"></i>Teslimat Adresi</h4>
                        
                        <?php if (!empty($addresses)): ?>
                            <div class="saved-addresses mb-3">
                                <label class="form-label">Kayıtlı Adreslerim</label>
                                <div class="address-list">
                                    <?php foreach ($addresses as $addr): ?>
                                        <label class="address-option">
                                            <input type="radio" name="saved_address" value="<?= $addr['id'] ?>">
                                            <div class="address-card">
                                                <strong><?= htmlspecialchars($addr['title']) ?></strong>
                                                <p><?= nl2br(htmlspecialchars($addr['address'])) ?></p>
                                                <small><?= htmlspecialchars($addr['district']) ?> / <?= htmlspecialchars($addr['city']) ?></small>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                    <label class="address-option">
                                        <input type="radio" name="saved_address" value="new" checked>
                                        <div class="address-card new-address">
                                            <i class="bi bi-plus-circle"></i>
                                            <span>Yeni Adres Ekle</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div id="newAddressForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">İl *</label>
                                    <select name="city" id="citySelect" class="form-select" required>
                                        <option value="">İl Seçin</option>
                                        <?php foreach ($cities as $city): ?>
                                            <option value="<?= htmlspecialchars($city['name']) ?>" data-id="<?= $city['id'] ?>">
                                                <?= htmlspecialchars($city['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">İlçe *</label>
                                    <select name="district" id="districtSelect" class="form-select" required disabled>
                                        <option value="">Önce il seçin</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mahalle</label>
                                    <select name="neighborhood" id="neighborhoodSelect" class="form-select" disabled>
                                        <option value="">Önce ilçe seçin</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Adres *</label>
                                    <textarea name="address" class="form-control" rows="3" required
                                              placeholder="Sokak, mahalle, bina no, daire no..."></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Posta Kodu</label>
                                    <input type="text" name="postal_code" class="form-control" placeholder="34000">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="checkout-section">
                        <h4 class="section-title"><i class="bi bi-chat-left-text me-2"></i>Sipariş Notu</h4>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Siparişinizle ilgili eklemek istediğiniz not (opsiyonel)"></textarea>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <h4 class="section-title"><i class="bi bi-credit-card me-2"></i>Ödeme Yöntemi</h4>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="credit_card" checked>
                                <div class="payment-card">
                                    <i class="bi bi-credit-card-2-front"></i>
                                    <span>Kredi / Banka Kartı</span>
                                    <small>3D Secure ile güvenli ödeme</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary sticky-top">
                        <h4 class="summary-title">Sipariş Özeti</h4>
                        
                        <!-- Cart Items Preview -->
                        <div class="summary-items">
                            <?php foreach ($cart as $item): ?>
                                <div class="summary-item">
                                    <div class="item-image">
                                        <img src="<?= $item['image'] ? upload($item['image']) : asset('images/no-image.jpg') ?>" alt="">
                                        <span class="item-qty"><?= $item['quantity'] ?></span>
                                    </div>
                                    <div class="item-info">
                                        <h6><?= htmlspecialchars($item['name']) ?></h6>
                                        <small>Beden: <?= $item['size'] ?></small>
                                    </div>
                                    <div class="item-price">
                                        <?= formatPrice($item['price'] * $item['quantity']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="summary-row">
                            <span>Ara Toplam</span>
                            <span><?= formatPrice($totals['subtotal']) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Kargo</span>
                            <span>
                                <?php if ($totals['free_shipping']): ?>
                                    <span class="text-success">Ücretsiz</span>
                                <?php else: ?>
                                    <?= formatPrice($totals['shipping']) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <hr>
                        
                        <div class="summary-row summary-total">
                            <span>Toplam</span>
                            <span><?= formatPrice($totals['total']) ?></span>
                        </div>
                        
                        <button type="submit" class="btn btn-dark btn-lg w-100 mt-4" id="placeOrderBtn">
                            <i class="bi bi-lock me-2"></i>Siparişi Tamamla
                        </button>
                        
                        <p class="text-muted small mt-3 text-center">
                            <i class="bi bi-shield-check me-1"></i>
                            256-bit SSL şifrelemeli güvenli ödeme
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Address API - turkiyeapi.dev entegrasyonu
const citySelect = document.getElementById('citySelect');
const districtSelect = document.getElementById('districtSelect');
const neighborhoodSelect = document.getElementById('neighborhoodSelect');

// İl değiştiğinde ilçeleri yükle
citySelect.addEventListener('change', async function() {
    const cityId = this.options[this.selectedIndex].dataset.id;
    
    if (!cityId) {
        districtSelect.innerHTML = '<option value="">Önce il seçin</option>';
        districtSelect.disabled = true;
        neighborhoodSelect.innerHTML = '<option value="">Önce ilçe seçin</option>';
        neighborhoodSelect.disabled = true;
        return;
    }
    
    // Loading state
    districtSelect.disabled = true;
    districtSelect.innerHTML = '<option value="">⏳ İlçeler yükleniyor...</option>';
    neighborhoodSelect.innerHTML = '<option value="">Önce ilçe seçin</option>';
    neighborhoodSelect.disabled = true;
    
    try {
        const response = await fetch(CONFIG.baseUrl + '/checkout/districts?city_id=' + cityId);
        const data = await response.json();
        
        if (data.districts && data.districts.length > 0) {
            districtSelect.innerHTML = '<option value="">İlçe Seçin</option>';
            data.districts.forEach(d => {
                const option = document.createElement('option');
                option.value = d.name;
                option.dataset.id = d.id;
                option.textContent = d.name;
                districtSelect.appendChild(option);
            });
            districtSelect.disabled = false;
        } else {
            districtSelect.innerHTML = '<option value="">İlçe bulunamadı</option>';
        }
    } catch (error) {
        console.error('İlçe yükleme hatası:', error);
        districtSelect.innerHTML = '<option value="">Yüklenemedi, tekrar deneyin</option>';
        districtSelect.disabled = false;
    }
});

// İlçe değiştiğinde (mahalle API desteklenmediği için basit bir mesaj)
districtSelect.addEventListener('change', function() {
    if (this.value) {
        neighborhoodSelect.innerHTML = '<option value="">Mahalle (Opsiyonel)</option>';
        neighborhoodSelect.disabled = true;
    }
});

// Kayıtlı adres seçildiğinde formu gizle/göster
document.querySelectorAll('input[name="saved_address"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const newAddressForm = document.getElementById('newAddressForm');
        if (this.value === 'new') {
            newAddressForm.style.display = 'block';
        } else {
            newAddressForm.style.display = 'none';
        }
    });
});

// Form Submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('placeOrderBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>İşleniyor...';
    
    const formData = new FormData(this);
    
    fetch(CONFIG.baseUrl + '/checkout/place-order', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(() => {
        showToast('Bir hata oluştu, lütfen tekrar deneyin', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>
