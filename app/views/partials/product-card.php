<!-- Product Card Component -->
<?php
$images = isset($product['images']) ? $product['images'] : [];
$primaryImage = $images[0]['image_path'] ?? null;
$displayPrice = $product['sale_price'] ?? $product['price'];
$hasDiscount = !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
?>

<div class="product-card animate-on-scroll">
    <div class="product-image">
        <?php if ($primaryImage): ?>
            <a href="<?= url('product/' . $product['slug']) ?>">
                <img src="<?= upload($primaryImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
            </a>
        <?php else: ?>
            <a href="<?= url('product/' . $product['slug']) ?>">
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                    <i class="bi bi-image" style="font-size: 48px;"></i>
                </div>
            </a>
        <?php endif; ?>
        
        <!-- Badges -->
        <div class="product-badges">
            <?php if (!empty($product['is_new'])): ?>
                <span class="badge-new">Yeni</span>
            <?php endif; ?>
            <?php if ($hasDiscount): ?>
                <?php $discountPercent = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                <span class="badge-sale">-%<?= $discountPercent ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="product-actions">
            <button class="btn-quick-view" title="Hızlı Görüntüle">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn-wishlist" title="Favorilere Ekle">
                <i class="bi bi-heart"></i>
            </button>
        </div>
        
        <!-- Add to Cart Overlay -->
        <div class="product-cart-overlay">
            <a href="<?= url('product/' . $product['slug']) ?>" class="btn-add-cart">
                <i class="bi bi-bag-plus"></i>
                Ürünü İncele
            </a>
        </div>
    </div>
    
    <div class="product-info">
        <?php if (!empty($product['category_name'])): ?>
            <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
        <?php endif; ?>
        
        <h4 class="product-title">
            <a href="<?= url('product/' . $product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a>
        </h4>
        
        <div class="product-price">
            <span class="price-current"><?= formatPrice($displayPrice) ?></span>
            <?php if ($hasDiscount): ?>
                <span class="price-old"><?= formatPrice($product['price']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
