<!-- Product Detail -->
<div class="product-detail py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('') ?>">Ana Sayfa</a></li>
                <li class="breadcrumb-item"><a href="<?= url('products') ?>">Ürünler</a></li>
                <?php if (!empty($product['category_name'])): ?>
                    <li class="breadcrumb-item"><a href="<?= url('category/' . ($product['category']['slug'] ?? '')) ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
        
        <div class="row g-5">
            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <!-- Main Image -->
                    <div class="main-image mb-3">
                        <?php $mainImage = $product['images'][0]['image_path'] ?? null; ?>
                        <img src="<?= $mainImage ? upload($mainImage) : asset('images/no-image.jpg') ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             id="mainProductImage"
                             class="img-fluid">
                        
                        <!-- Badges -->
                        <div class="product-badges">
                            <?php if (!empty($product['is_new'])): ?>
                                <span class="badge badge-new">YENİ</span>
                            <?php endif; ?>
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                                <span class="badge badge-sale">-%<?= $discount ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Thumbnails -->
                    <?php if (count($product['images']) > 1): ?>
                        <div class="thumbnail-images">
                            <div class="row g-2">
                                <?php foreach ($product['images'] as $image): ?>
                                    <div class="col-3">
                                        <img src="<?= upload($image['image_path']) ?>" 
                                             alt="Thumbnail"
                                             class="thumbnail-img"
                                             onclick="changeMainImage(this.src)">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info-detail">
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="product-category-label"><?= htmlspecialchars($product['category_name']) ?></span>
                    <?php endif; ?>
                    
                    <h1 class="product-title-detail"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <!-- Price -->
                    <div class="product-price-detail mb-4">
                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                            <span class="price-old"><?= formatPrice($product['price']) ?></span>
                            <span class="price-current"><?= formatPrice($product['sale_price']) ?></span>
                        <?php else: ?>
                            <span class="price-current"><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <?php if (!empty($product['description'])): ?>
                        <div class="product-description mb-4">
                            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Add to Cart Form -->
                    <form id="addToCartForm" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <!-- Size Selection -->
                        <?php if (!empty($product['sizes'])): ?>
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Beden Seçin</label>
                                <div class="size-selector">
                                    <?php foreach ($product['sizes'] as $size): ?>
                                        <label class="size-option <?= $size['stock'] <= 0 ? 'out-of-stock' : '' ?>">
                                            <input type="radio" name="size" value="<?= $size['size'] ?>" 
                                                   <?= $size['stock'] <= 0 ? 'disabled' : '' ?>
                                                   required>
                                            <span class="size-label">
                                                <?= $size['size'] ?>
                                                <?php if ($size['stock'] <= 0): ?>
                                                    <small class="stock-out">Tükendi</small>
                                                <?php elseif ($size['stock'] <= 3): ?>
                                                    <small class="stock-low">Son <?= $size['stock'] ?></small>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Quantity -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Adet</label>
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus" onclick="changeQty(-1)">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" name="quantity" value="1" min="1" max="10" class="qty-input" id="qtyInput">
                                <button type="button" class="qty-btn plus" onclick="changeQty(1)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Add to Cart Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark btn-lg btn-add-to-cart">
                                <i class="bi bi-bag-plus me-2"></i>
                                SEPETE EKLE
                            </button>
                        </div>
                    </form>
                    
                    <!-- Product Meta -->
                    <div class="product-meta mt-4 pt-4 border-top">
                        <div class="meta-item">
                            <i class="bi bi-truck"></i>
                            <span>500 TL üzeri ücretsiz kargo</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>14 gün içinde kolay iade</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Güvenli ödeme</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related)): ?>
            <section class="related-products mt-5 pt-5 border-top">
                <h3 class="section-title mb-4">Benzer Ürünler</h3>
                <div class="row g-4">
                    <?php foreach ($related as $product): ?>
                        <div class="col-lg-3 col-md-4 col-6">
                            <?php include __DIR__ . '/../partials/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('mainProductImage').src = src;
}

function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 10) val = 10;
    input.value = val;
}

document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const btn = this.querySelector('.btn-add-to-cart');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ekleniyor...';
    
    fetch(CONFIG.baseUrl + '/cart/add', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="bi bi-check2 me-2"></i>Eklendi!';
            btn.classList.remove('btn-dark');
            btn.classList.add('btn-success');
            
            document.getElementById('cartCount').textContent = data.cart_count;
            
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-dark');
            }, 2000);
        } else {
            alert(data.message || 'Bir hata oluştu');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(() => {
        alert('Bir hata oluştu');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>
