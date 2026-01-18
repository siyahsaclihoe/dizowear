<?php
/**
 * Dızo Wear - Mini Single-File Version
 * Tek dosyalık demo/showcase versiyonu
 * 
 * Bu dosya tam özellikli bir e-ticaret değildir,
 * temel frontend işlevselliğini gösteren minimal bir versiyondur.
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ========== DATABASE CONFIG ==========
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'dizowear',
    'username' => 'root',
    'password' => ''
];

// ========== DATABASE CONNECTION ==========
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('DB Error: ' . $e->getMessage());
}

// ========== HELPERS ==========
function formatPrice($price) {
    return number_format($price, 2, ',', '.') . ' TL';
}

function getCart() {
    return $_SESSION['cart'] ?? [];
}

function getCartCount() {
    return array_sum(array_column(getCart(), 'quantity'));
}

// ========== ROUTING ==========
$page = $_GET['page'] ?? 'home';
$slug = $_GET['slug'] ?? '';

// ========== CART ACTIONS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'add_to_cart') {
        $productId = (int) $_POST['product_id'];
        $size = $_POST['size'] ?? 'M';
        $quantity = (int) ($_POST['quantity'] ?? 1);
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $cartKey = $productId . '_' . $size;
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            
            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$cartKey] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['sale_price'] ?: $product['price'],
                    'size' => $size,
                    'quantity' => $quantity,
                ];
            }
            echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
        }
        exit;
    }
    
    if ($_POST['action'] === 'remove_from_cart') {
        $cartKey = $_POST['cart_key'];
        unset($_SESSION['cart'][$cartKey]);
        echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
        exit;
    }
}

// ========== DATA FETCHING ==========
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'active' ORDER BY p.created_at DESC LIMIT 12")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order")->fetchAll();
$cart = getCart();
$cartCount = getCartCount();

// Product detail
$product = null;
if ($page === 'product' && $slug) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.status = 'active'");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
    
    if ($product) {
        $sizes = $pdo->prepare("SELECT * FROM product_sizes WHERE product_id = ?");
        $sizes->execute([$product['id']]);
        $product['sizes'] = $sizes->fetchAll();
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dızo Wear - Mini Version</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #000; --secondary: #fff; }
        body { font-family: 'Inter', sans-serif; }
        .navbar-brand { font-weight: 800; font-size: 24px; }
        .navbar-brand span { color: #666; }
        .product-card { border-radius: 12px; overflow: hidden; transition: all 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .product-image { aspect-ratio: 3/4; background: #f5f5f5; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-title { font-weight: 600; }
        .price-current { font-weight: 700; font-size: 18px; }
        .price-old { text-decoration: line-through; color: #999; }
        .badge-new { background: #000; color: #fff; }
        .badge-sale { background: #dc3545; color: #fff; }
        .cart-count { position: absolute; top: -5px; right: -5px; background: #000; color: #fff; font-size: 10px; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .hero { background: linear-gradient(135deg, #1a1a1a, #000); color: #fff; padding: 100px 0; }
        .hero h1 { font-size: 48px; font-weight: 800; }
        .btn-hero { background: #fff; color: #000; padding: 15px 40px; font-weight: 600; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="?page=home">DIZO<span>WEAR</span></a>
            <div class="d-flex align-items-center gap-3">
                <a href="?page=cart" class="btn btn-link position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="cart-count"><?= $cartCount ?></span>
                </a>
            </div>
        </div>
    </nav>
    
    <?php if ($page === 'home'): ?>
    <!-- Hero -->
    <section class="hero text-center">
        <div class="container">
            <p class="mb-2" style="letter-spacing: 3px;">MINI VERSION</p>
            <h1>DIZO WEAR</h1>
            <p class="lead mb-4">Tek dosyalık e-ticaret demo</p>
            <a href="#products" class="btn btn-hero">ÜRÜNLERE GÖZ AT</a>
        </div>
    </section>
    
    <!-- Products -->
    <section class="py-5" id="products">
        <div class="container">
            <h3 class="text-center mb-4">ÜRÜNLER</h3>
            <div class="row g-4">
                <?php foreach ($products as $p): ?>
                <div class="col-md-4 col-6">
                    <div class="product-card bg-white">
                        <div class="product-image position-relative">
                            <img src="../assets/images/no-image.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
                            <div class="position-absolute top-0 start-0 p-2">
                                <?php if ($p['is_new']): ?><span class="badge badge-new">YENİ</span><?php endif; ?>
                                <?php if ($p['sale_price'] && $p['sale_price'] < $p['price']): ?>
                                    <span class="badge badge-sale">-%<?= round((1-$p['sale_price']/$p['price'])*100) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-3">
                            <small class="text-muted"><?= htmlspecialchars($p['category_name'] ?? '') ?></small>
                            <h6 class="product-title mb-2"><?= htmlspecialchars($p['name']) ?></h6>
                            <div class="d-flex gap-2 align-items-center">
                                <?php if ($p['sale_price'] && $p['sale_price'] < $p['price']): ?>
                                    <span class="price-old"><?= formatPrice($p['price']) ?></span>
                                    <span class="price-current"><?= formatPrice($p['sale_price']) ?></span>
                                <?php else: ?>
                                    <span class="price-current"><?= formatPrice($p['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="?page=product&slug=<?= $p['slug'] ?>" class="btn btn-dark btn-sm w-100 mt-3">İNCELE</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <?php elseif ($page === 'product' && $product): ?>
    <!-- Product Detail -->
    <section class="py-5">
        <div class="container">
            <a href="?page=home" class="btn btn-outline-dark btn-sm mb-4"><i class="bi bi-arrow-left me-2"></i>Geri</a>
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="bg-light rounded" style="aspect-ratio: 1; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-image display-1 text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted"><?= htmlspecialchars($product['category_name'] ?? '') ?></small>
                    <h2 class="mb-3"><?= htmlspecialchars($product['name']) ?></h2>
                    <div class="mb-4">
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <span class="price-old fs-5"><?= formatPrice($product['price']) ?></span>
                            <span class="price-current fs-3"><?= formatPrice($product['sale_price']) ?></span>
                        <?php else: ?>
                            <span class="price-current fs-3"><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
                    
                    <form id="addToCartForm">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Beden</label>
                            <div class="d-flex gap-2">
                                <?php foreach ($product['sizes'] as $size): ?>
                                    <label class="btn btn-outline-dark">
                                        <input type="radio" name="size" value="<?= $size['size'] ?>" class="d-none" required>
                                        <?= $size['size'] ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-dark btn-lg w-100">
                            <i class="bi bi-bag-plus me-2"></i>SEPETE EKLE
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <?php elseif ($page === 'cart'): ?>
    <!-- Cart -->
    <section class="py-5">
        <div class="container">
            <h3 class="mb-4">SEPETİM</h3>
            <?php if (empty($cart)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-bag-x display-1 text-muted"></i>
                    <p class="mt-3">Sepetiniz boş</p>
                    <a href="?page=home" class="btn btn-dark">Alışverişe Başla</a>
                </div>
            <?php else: ?>
                <?php $total = 0; ?>
                <?php foreach ($cart as $key => $item): ?>
                    <?php $lineTotal = $item['price'] * $item['quantity']; $total += $lineTotal; ?>
                    <div class="d-flex align-items-center border-bottom py-3">
                        <div class="flex-grow-1">
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <small class="d-block text-muted">Beden: <?= $item['size'] ?> | Adet: <?= $item['quantity'] ?></small>
                        </div>
                        <div class="text-end">
                            <strong><?= formatPrice($lineTotal) ?></strong>
                            <button class="btn btn-link text-danger btn-remove" data-key="<?= $key ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-end mt-4">
                    <h4>Toplam: <?= formatPrice($total) ?></h4>
                    <button class="btn btn-dark btn-lg mt-2" disabled>Demo - Ödeme Yapılamaz</button>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php else: ?>
    <div class="container py-5 text-center">
        <h1>404</h1>
        <p>Sayfa bulunamadı</p>
        <a href="?page=home" class="btn btn-dark">Ana Sayfa</a>
    </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Dızo Wear Mini Version</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Size selection
    document.querySelectorAll('input[name="size"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('input[name="size"]').forEach(i => i.closest('label').classList.remove('active'));
            this.closest('label').classList.add('active');
        });
    });
    
    // Add to cart
    const form = document.getElementById('addToCartForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Ürün sepete eklendi!');
                        document.querySelector('.cart-count').textContent = data.cart_count;
                    }
                });
        });
    }
    
    // Remove from cart
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            const formData = new FormData();
            formData.append('action', 'remove_from_cart');
            formData.append('cart_key', this.dataset.key);
            fetch('', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(() => location.reload());
        });
    });
    </script>
</body>
</html>
