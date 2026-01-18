<?php
/**
 * Dızo Wear - Cart Controller
 * AJAX tabanlı sepet işlemleri
 */

require_once __DIR__ . '/../helpers/Controller.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Coupon.php';

class CartController extends Controller {
    private $productModel;
    private $couponModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->couponModel = new Coupon();
        
        // Sepet session'ını başlat
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * Sepet sayfası
     */
    public function index(): void {
        $cart = $this->getCartWithDetails();
        $totals = calculateCartTotal($cart);
        
        $data = [
            'title' => 'Sepetim',
            'cart' => $cart,
            'totals' => $totals,
        ];
        
        $this->view('cart/index', $data);
    }
    
    /**
     * Sepeti detaylı bilgilerle getir
     */
    private function getCartWithDetails(): array {
        $cart = $_SESSION['cart'] ?? [];
        $detailedCart = [];
        
        foreach ($cart as $key => $item) {
            $product = $this->productModel->find($item['product_id']);
            if ($product) {
                $detailedCart[$key] = array_merge($item, [
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'image' => $this->productModel->getImages($product['id'])[0]['image_path'] ?? null,
                    'available_stock' => $this->getAvailableStock($product['id'], $item['size']),
                ]);
            }
        }
        
        return $detailedCart;
    }
    
    /**
     * Beden stokunu getir
     */
    private function getAvailableStock(int $productId, string $size): int {
        $sizes = $this->productModel->getSizes($productId);
        foreach ($sizes as $s) {
            if ($s['size'] === $size) {
                return (int) $s['stock'];
            }
        }
        return 0;
    }
    
    /**
     * AJAX: Sepete ekle
     */
    public function add(): void {
        $this->verifyCsrf();
        
        $productId = (int) $this->input('product_id');
        $size = $this->input('size');
        $quantity = (int) $this->input('quantity', 1);
        
        // Validasyon
        if (!$productId || !$size || $quantity < 1) {
            $this->json(['success' => false, 'message' => 'Geçersiz istek'], 400);
            return;
        }
        
        // Ürün kontrolü
        $product = $this->productModel->find($productId);
        if (!$product || $product['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Ürün bulunamadı'], 404);
            return;
        }
        
        // Stok kontrolü
        if (!$this->productModel->checkStock($productId, $size, $quantity)) {
            $this->json(['success' => false, 'message' => 'Yetersiz stok'], 400);
            return;
        }
        
        // Sepet key'i
        $cartKey = $productId . '_' . $size;
        $price = $product['sale_price'] ?: $product['price'];
        
        // Sepete ekle/güncelle
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'size' => $size,
                'quantity' => $quantity,
                'price' => (float) $price,
            ];
        }
        
        $totals = calculateCartTotal($_SESSION['cart']);
        
        $this->json([
            'success' => true,
            'message' => 'Ürün sepete eklendi',
            'cart_count' => $totals['item_count'],
            'cart_total' => formatPrice($totals['total']),
        ]);
    }
    
    /**
     * AJAX: Sepetten kaldır
     */
    public function remove(): void {
        $this->verifyCsrf();
        
        $cartKey = $this->input('cart_key');
        
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
        }
        
        // Kupon kontrolü - sepet boşaldıysa kuponu kaldır
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['coupon']);
        }
        
        $totals = calculateCartTotal($_SESSION['cart']);
        
        $this->json([
            'success' => true,
            'message' => 'Ürün sepetten kaldırıldı',
            'cart_count' => $totals['item_count'],
            'cart_total' => formatPrice($totals['total']),
        ]);
    }
    
    /**
     * AJAX: Adet güncelle
     */
    public function update(): void {
        $this->verifyCsrf();
        
        $cartKey = $this->input('cart_key');
        $quantity = (int) $this->input('quantity');
        
        if ($quantity < 1) {
            // Sil
            unset($_SESSION['cart'][$cartKey]);
        } elseif (isset($_SESSION['cart'][$cartKey])) {
            $item = $_SESSION['cart'][$cartKey];
            
            // Stok kontrolü
            if (!$this->productModel->checkStock($item['product_id'], $item['size'], $quantity)) {
                $this->json(['success' => false, 'message' => 'Yetersiz stok'], 400);
                return;
            }
            
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
        }
        
        $totals = calculateCartTotal($_SESSION['cart']);
        $cart = $this->getCartWithDetails();
        
        // Güncel satır toplamı
        $lineTotal = 0;
        if (isset($cart[$cartKey])) {
            $lineTotal = $cart[$cartKey]['price'] * $cart[$cartKey]['quantity'];
        }
        
        $this->json([
            'success' => true,
            'cart_count' => $totals['item_count'],
            'subtotal' => formatPrice($totals['subtotal']),
            'shipping' => formatPrice($totals['shipping']),
            'total' => formatPrice($totals['total']),
            'line_total' => formatPrice($lineTotal),
            'free_shipping' => $totals['free_shipping'],
        ]);
    }
    
    /**
     * AJAX: Kupon uygula
     */
    public function applyCoupon(): void {
        $this->verifyCsrf();
        
        $code = trim($this->input('code'));
        
        if (empty($code)) {
            $this->json(['valid' => false, 'message' => 'Kupon kodu girin.']);
            return;
        }
        
        $cart = $_SESSION['cart'] ?? [];
        $totals = calculateCartTotal($cart);
        
        // Kupon doğrula
        $result = $this->couponModel->validate($code, $totals['subtotal']);
        
        if (!$result['valid']) {
            $this->json($result);
            return;
        }
        
        $discount = $result['discount'];
        $newTotal = $totals['total'] - $discount;
        if ($newTotal < 0) $newTotal = 0;
        
        // Session'a kaydet
        $_SESSION['coupon'] = [
            'id' => $result['coupon']['id'],
            'code' => $result['coupon']['code'],
            'discount' => $discount,
        ];
        
        $this->json([
            'valid' => true,
            'message' => 'Kupon uygulandı!',
            'coupon' => $result['coupon'],
            'discount' => $discount,
            'discount_formatted' => formatPrice($discount),
            'new_total' => formatPrice($newTotal),
        ]);
    }
    
    /**
     * AJAX: Sepet özet
     */
    public function summary(): void {
        $cart = $this->getCartWithDetails();
        $totals = calculateCartTotal($_SESSION['cart']);
        
        $this->json([
            'success' => true,
            'cart' => array_values($cart),
            'totals' => $totals,
        ]);
    }
    
    /**
     * Sepeti temizle
     */
    public function clear(): void {
        $_SESSION['cart'] = [];
        unset($_SESSION['coupon']);
        $this->redirect('cart');
    }
}
