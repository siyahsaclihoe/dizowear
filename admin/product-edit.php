<?php
/**
 * Dızo Wear - Admin Product Edit
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

$productId = (int) ($_GET['id'] ?? 0);
$product = $productModel->find($productId);

if (!$product) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ürün bulunamadı.'];
    header('Location: products.php');
    exit;
}

$categories = $categoryModel->all();
$images = $productModel->getImages($productId);
$sizes = $productModel->getSizes($productId);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => slug($_POST['name'] ?? ''),
        'category_id' => (int) ($_POST['category_id'] ?? 0) ?: null,
        'description' => trim($_POST['description'] ?? ''),
        'price' => (float) ($_POST['price'] ?? 0),
        'sale_price' => (float) ($_POST['sale_price'] ?? 0) ?: null,
        'status' => $_POST['status'] ?? 'active',
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_new' => isset($_POST['is_new']) ? 1 : 0,
    ];
    
    if (empty($data['name'])) $errors[] = 'Ürün adı gereklidir.';
    if ($data['price'] <= 0) $errors[] = 'Geçerli bir fiyat girin.';
    
    if (empty($errors)) {
        $productModel->update($productId, $data);
        
        // Yeni görseller
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['images']['name'][$i],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['images']['error'][$i],
                        'size' => $_FILES['images']['size'][$i],
                    ];
                    $imagePath = uploadFile($file, 'products');
                    if ($imagePath) {
                        $productModel->addImage($productId, $imagePath, 0);
                    }
                }
            }
        }
        
        // Bedenleri güncelle
        $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        foreach ($allSizes as $size) {
            $stock = (int) ($_POST['stock_' . $size] ?? 0);
            $existingSize = array_filter($sizes, fn($s) => $s['size'] === $size);
            
            if (!empty($existingSize)) {
                $productModel->updateSize($productId, $size, $stock);
            } elseif ($stock > 0) {
                $productModel->setSize($productId, $size, $stock);
            }
        }
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Ürün güncellendi.'];
        header('Location: products.php');
        exit;
    }
}

// Size lookup
$sizeStock = [];
foreach ($sizes as $s) {
    $sizeStock[$s['size']] = $s['stock'];
}

$pageTitle = 'Ürün Düzenle';
include 'views/layouts/header.php';
?>

<div class="mb-4">
    <a href="products.php" class="btn btn-outline-dark btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Ürünlere Dön
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-header"><h5 class="mb-0">Ürün Bilgileri</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ürün Adı *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fiyat (TL) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" required
                                   value="<?= $product['price'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">İndirimli Fiyat (TL)</label>
                            <input type="number" name="sale_price" class="form-control" step="0.01"
                                   value="<?= $product['sale_price'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Images -->
            <div class="admin-card mt-4">
                <div class="card-header"><h5 class="mb-0">Görseller</h5></div>
                <div class="card-body">
                    <?php if (!empty($images)): ?>
                        <div class="row g-2 mb-3">
                            <?php foreach ($images as $img): ?>
                                <div class="col-3">
                                    <img src="../uploads/<?= htmlspecialchars($img['image_path']) ?>" 
                                         class="img-thumbnail" style="height: 100px; object-fit: cover; width: 100%;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted">Yeni görsel eklemek için seçin</small>
                </div>
            </div>
            
            <!-- Sizes -->
            <div class="admin-card mt-4">
                <div class="card-header"><h5 class="mb-0">Beden & Stok</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                            <div class="col">
                                <label class="form-label"><?= $size ?></label>
                                <input type="number" name="stock_<?= $size ?>" class="form-control" min="0" 
                                       value="<?= $sizeStock[$size] ?? 0 ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="card-header"><h5 class="mb-0">Yayınla</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured"
                               <?= $product['is_featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isFeatured">Öne çıkan ürün</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_new" class="form-check-input" id="isNew"
                               <?= $product['is_new'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isNew">Yeni ürün</label>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-check-circle me-2"></i>Güncelle
                    </button>
                </div>
            </div>
            
            <div class="admin-card mt-4">
                <div class="card-header"><h5 class="mb-0">Kategori</h5></div>
                <div class="card-body">
                    <select name="category_id" class="form-select">
                        <option value="">Kategori Seçin</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include 'views/layouts/footer.php'; ?>
