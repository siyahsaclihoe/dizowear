<!-- Products Page -->
<div class="products-header py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('') ?>">Ana Sayfa</a></li>
                <?php if (!empty($currentCategory)): ?>
                    <li class="breadcrumb-item"><a href="<?= url('products') ?>">Ürünler</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($currentCategory['name']) ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active">Ürünler</li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1 class="page-title mt-2"><?= htmlspecialchars($title) ?></h1>
    </div>
</div>

<div class="products-content py-4">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="filters-sidebar">
                    <!-- Categories -->
                    <div class="filter-group">
                        <h5 class="filter-title">Kategoriler</h5>
                        <ul class="filter-list">
                            <li>
                                <a href="<?= url('products') ?>" class="<?= empty($currentCategory) ? 'active' : '' ?>">
                                    Tümü
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="<?= url('category/' . $cat['slug']) ?>" 
                                       class="<?= (!empty($currentCategory) && $currentCategory['id'] == $cat['id']) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h5 class="filter-title">Fiyat</h5>
                        <div class="price-range">
                            <input type="range" class="form-range" min="0" max="2000" id="priceRange">
                            <div class="d-flex justify-content-between">
                                <span>0 TL</span>
                                <span>2000 TL</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Size Filter -->
                    <div class="filter-group">
                        <h5 class="filter-title">Beden</h5>
                        <div class="size-filter">
                            <label class="size-checkbox">
                                <input type="checkbox" value="S"> S
                            </label>
                            <label class="size-checkbox">
                                <input type="checkbox" value="M"> M
                            </label>
                            <label class="size-checkbox">
                                <input type="checkbox" value="L"> L
                            </label>
                            <label class="size-checkbox">
                                <input type="checkbox" value="XL"> XL
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="products-toolbar mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="results-count"><?= count($products) ?> ürün</span>
                        <div class="sort-dropdown">
                            <select class="form-select" id="sortSelect">
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>En Yeni</option>
                                <option value="price-low" <?= $sort === 'price-low' ? 'selected' : '' ?>>Fiyat: Düşükten Yükseğe</option>
                                <option value="price-high" <?= $sort === 'price-high' ? 'selected' : '' ?>>Fiyat: Yüksekten Düşüğe</option>
                                <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popüler</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <?php if (empty($products)): ?>
                    <div class="empty-state text-center py-5">
                        <i class="bi bi-bag-x display-1 text-muted"></i>
                        <h4 class="mt-3">Ürün Bulunamadı</h4>
                        <p class="text-muted">Arama kriterlerinize uygun ürün bulunamadı.</p>
                        <a href="<?= url('products') ?>" class="btn btn-dark">Tüm Ürünleri Gör</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-4 col-6">
                                <?php include __DIR__ . '/../partials/product-card.php'; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($pagination['total'] > 1): ?>
                        <nav class="mt-5">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagination['current'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $pagination['current'] - 1 ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                                    <li class="page-item <?= $i === $pagination['current'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($pagination['hasMore']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $pagination['current'] + 1 ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
