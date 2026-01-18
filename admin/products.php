<?php
/**
 * Dızo Wear - Admin Products
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

// Silme işlemi
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $productModel->deleteProduct((int)$_GET['delete']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Ürün silindi.'];
    header('Location: products.php');
    exit;
}

$products = $productModel->all('created_at', 'DESC');
$categories = $categoryModel->all();

$pageTitle = 'Ürünler';
include 'views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Ürün Listesi</h4>
    <a href="product-add.php" class="btn btn-dark">
        <i class="bi bi-plus-circle me-2"></i>Yeni Ürün
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="80">Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th>Fiyat</th>
                        <th>Durum</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-bag-x display-4 d-block mb-3"></i>
                                Henüz ürün eklenmemiş
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <?php 
                                $images = $productModel->getImages($product['id']);
                                $image = $images[0]['image_path'] ?? null;
                                $category = array_filter($categories, fn($c) => $c['id'] == $product['category_id']);
                                $categoryName = !empty($category) ? array_values($category)[0]['name'] : '-';
                            ?>
                            <tr>
                                <td>
                                    <?php if ($image): ?>
                                        <img src="../uploads/<?= htmlspecialchars($image) ?>" alt="" class="product-thumb">
                                    <?php else: ?>
                                        <div class="product-thumb-empty"><i class="bi bi-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    <?php if ($product['is_featured']): ?>
                                        <span class="badge bg-warning text-dark ms-1">Öne Çıkan</span>
                                    <?php endif; ?>
                                    <?php if ($product['is_new']): ?>
                                        <span class="badge bg-success ms-1">Yeni</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($categoryName) ?></td>
                                <td>
                                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                        <del class="text-muted"><?= formatPrice($product['price']) ?></del><br>
                                        <strong class="text-danger"><?= formatPrice($product['sale_price']) ?></strong>
                                    <?php else: ?>
                                        <?= formatPrice($product['price']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $product['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= $product['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="product-edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="products.php?delete=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
