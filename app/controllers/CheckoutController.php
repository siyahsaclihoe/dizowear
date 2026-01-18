<?php
/**
 * Dızo Wear - Checkout Controller
 * Ödeme ve sipariş işlemleri
 */

require_once __DIR__ . '/../helpers/Controller.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Address.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../helpers/TurkeyAddress.php';

class CheckoutController extends Controller {
    private $productModel;
    private $orderModel;
    private $addressModel;
    private $paymentModel;
    private $paymentConfig;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->addressModel = new Address();
        $this->paymentModel = new Payment();
        $this->paymentConfig = require __DIR__ . '/../../config/payment.php';
    }
    
    /**
     * Checkout sayfası
     */
    public function index(): void {
        $cart = $_SESSION['cart'] ?? [];
        
        if (empty($cart)) {
            $this->flash('warning', 'Sepetiniz boş.');
            $this->redirect('cart');
            return;
        }
        
        $totals = calculateCartTotal($cart);
        
        // Minimum tuttar kontrolü
        if ($totals['subtotal'] < $this->paymentConfig['general']['min_order_amount']) {
            $this->flash('warning', 'Minimum sipariş tutarı ' . formatPrice($this->paymentConfig['general']['min_order_amount']) . '\'dir.');
            $this->redirect('cart');
            return;
        }
        
        // Kullanıcı adresleri
        $addresses = [];
        if ($this->isLoggedIn()) {
            $addresses = $this->addressModel->getByUser($_SESSION['user']['id']);
        }
        
        // İl listesi
        $turkeyAddress = new TurkeyAddress();
        $cities = $turkeyAddress->getCities();
        
        $data = [
            'title' => 'Ödeme',
            'cart' => $this->getCartWithDetails(),
            'totals' => $totals,
            'addresses' => $addresses,
            'cities' => $cities,
        ];
        
        $this->view('checkout/index', $data);
    }
    
    /**
     * Sepet detayları
     */
    private function getCartWithDetails(): array {
        $cart = $_SESSION['cart'] ?? [];
        $detailed = [];
        
        foreach ($cart as $key => $item) {
            $product = $this->productModel->find($item['product_id']);
            if ($product) {
                $images = $this->productModel->getImages($product['id']);
                $detailed[$key] = array_merge($item, [
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'image' => $images[0]['image_path'] ?? null,
                ]);
            }
        }
        
        return $detailed;
    }
    
    /**
     * Sipariş oluştur
     */
    public function placeOrder(): void {
        $this->verifyCsrf();
        
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->json(['success' => false, 'message' => 'Sepetiniz boş'], 400);
            return;
        }
        
        // Form verileri
        $orderData = $this->only([
            'name', 'email', 'phone', 'city', 'district', 'neighborhood',
            'address', 'postal_code', 'notes', 'payment_method'
        ]);
        
        // Validasyon
        $required = ['name', 'email', 'phone', 'city', 'district', 'address'];
        foreach ($required as $field) {
            if (empty($orderData[$field])) {
                $this->json(['success' => false, 'message' => 'Lütfen tüm zorunlu alanları doldurun.'], 400);
                return;
            }
        }
        
        // E-posta validasyonu
        if (!filter_var($orderData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'message' => 'Geçerli bir e-posta adresi girin.'], 400);
            return;
        }
        
        // Stok kontrolü
        foreach ($cart as $item) {
            if (!$this->productModel->checkStock($item['product_id'], $item['size'], $item['quantity'])) {
                $product = $this->productModel->find($item['product_id']);
                $this->json([
                    'success' => false,
                    'message' => "'{$product['name']}' ürününde yeterli stok yok."
                ], 400);
                return;
            }
        }
        
        // Tutarları hesapla
        $totals = calculateCartTotal($cart);
        
        // Adresi formatla
        $shippingAddress = sprintf(
            "%s\n%s\n%s, %s/%s\nTel: %s",
            $orderData['name'],
            $orderData['address'],
            $orderData['neighborhood'] ?? '',
            $orderData['district'],
            $orderData['city'],
            $orderData['phone']
        );
        
        // Sipariş verileri
        $order = [
            'user_id' => $_SESSION['user']['id'] ?? null,
            'name' => $orderData['name'],
            'email' => $orderData['email'],
            'phone' => $orderData['phone'],
            'shipping_address' => $shippingAddress,
            'billing_address' => $shippingAddress,
            'subtotal' => $totals['subtotal'],
            'shipping_cost' => $totals['shipping'],
            'total' => $totals['total'],
            'payment_method' => $orderData['payment_method'] ?? 'credit_card',
            'notes' => $orderData['notes'] ?? '',
        ];
        
        // Sipariş kalemlerini hazırla
        $items = [];
        foreach ($cart as $item) {
            $product = $this->productModel->find($item['product_id']);
            $items[] = [
                'product_id' => $item['product_id'],
                'name' => $product['name'],
                'size' => $item['size'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }
        
        try {
            // Siparişi oluştur
            $orderId = $this->orderModel->createOrder($order, $items);
            $orderData = $this->orderModel->find($orderId);
            
            // Ödeme kaydı oluştur
            $paymentId = $this->paymentModel->createPayment(
                $orderId,
                $orderData['payment_method'],
                $totals['total']
            );
            
            // Siparişi session'a kaydet
            $_SESSION['pending_order'] = [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'order_number' => $orderData['order_number'],
            ];
            
            // Ödeme sayfasına yönlendir
            $this->json([
                'success' => true,
                'redirect' => url('checkout/payment/' . $orderId),
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Sipariş oluşturulamadı. Lütfen tekrar deneyin.'], 500);
        }
    }
    
    /**
     * Ödeme sayfası
     */
    public function payment(int $orderId): void {
        $order = $this->orderModel->getWithItems($orderId);
        
        if (!$order) {
            $this->redirect('checkout');
            return;
        }
        
        // Güvenlik kontrolü
        if ($order['user_id'] && (!$this->isLoggedIn() || $_SESSION['user']['id'] != $order['user_id'])) {
            $this->redirect('checkout');
            return;
        }
        
        // PayTR iframe URL'i oluştur
        $paymentUrl = $this->generatePaytrPayment($order);
        
        $data = [
            'title' => 'Ödeme',
            'order' => $order,
            'payment_url' => $paymentUrl,
        ];
        
        $this->viewOnly('checkout/payment', $data);
    }
    
    /**
     * PayTR ödeme linki oluştur
     */
    private function generatePaytrPayment(array $order): string {
        $config = $this->paymentConfig['paytr'];
        $db = Database::getInstance();
        
        // Veritabanından ayarları al
        $dbSettings = $db->fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'paytr%' OR setting_key LIKE 'payment%'");
        $settings = [];
        foreach ($dbSettings as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // API bilgileri
        $merchant_id = $settings['paytr_merchant_id'] ?? $config['merchant_id'];
        $merchant_key = $settings['paytr_merchant_key'] ?? $config['merchant_key'];
        $merchant_salt = $settings['paytr_merchant_salt'] ?? $config['merchant_salt'];
        $testMode = ($settings['payment_test_mode'] ?? '0') === '1' || $config['test_mode'];
        
        // API bilgileri eksikse demo moda düş
        if (empty($merchant_id) || empty($merchant_key) || empty($merchant_salt)) {
            return url('checkout/demo-payment/' . $order['id']);
        }
        
        // Gerçek PayTR entegrasyonu
        $user_ip = $_SERVER['REMOTE_ADDR'];
        if ($user_ip === '::1') $user_ip = '127.0.0.1';
        
        $merchant_oid = $order['order_number'];
        $email = $order['email'];
        $payment_amount = (int)($order['total'] * 100); // Kuruş
        $user_name = $order['name'];
        $user_phone = preg_replace('/[^0-9]/', '', $order['phone']);
        $user_address = $order['shipping_address'];
        
        // Sepet içeriği
        $basket = [];
        $items = $this->orderModel->getItems($order['id']);
        foreach ($items as $item) {
            $basket[] = [$item['product_name'], number_format($item['price'], 2, '.', ''), $item['quantity']];
        }
        $user_basket = base64_encode(json_encode($basket));
        
        $no_installment = $config['no_installment'];
        $max_installment = $config['max_installment'];
        $currency = $config['currency'];
        $test_mode = $testMode ? 1 : 0;
        
        $merchant_ok_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/dizowear/checkout/success';
        $merchant_fail_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/dizowear/checkout/fail';
        
        // Hash oluştur
        $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . 
                    $no_installment . $max_installment . $currency . $test_mode;
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
        
        $post_vals = [
            'merchant_id' => $merchant_id,
            'user_ip' => $user_ip,
            'merchant_oid' => $merchant_oid,
            'email' => $email,
            'payment_amount' => $payment_amount,
            'paytr_token' => $paytr_token,
            'user_basket' => $user_basket,
            'debug_on' => $config['debug_on'],
            'no_installment' => $no_installment,
            'max_installment' => $max_installment,
            'user_name' => $user_name,
            'user_address' => $user_address,
            'user_phone' => $user_phone,
            'merchant_ok_url' => $merchant_ok_url,
            'merchant_fail_url' => $merchant_fail_url,
            'timeout_limit' => $config['timeout_limit'],
            'currency' => $currency,
            'test_mode' => $test_mode,
            'lang' => $config['lang'],
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['api_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('PayTR CURL Error: ' . $error);
            return url('checkout/demo-payment/' . $order['id']);
        }
        
        $result = json_decode($result, true);
        
        if (isset($result['status']) && $result['status'] === 'success') {
            return $config['iframe_url'] . $result['token'];
        }
        
        error_log('PayTR Error: ' . ($result['reason'] ?? 'Unknown error'));
        return url('checkout/fail');
    }
    
    /**
     * Demo ödeme sayfası (test için)
     */
    public function demoPayment(int $orderId): void {
        $order = $this->orderModel->getWithItems($orderId);
        
        if (!$order) {
            $this->redirect('checkout');
            return;
        }
        
        $data = [
            'title' => 'Demo Ödeme',
            'order' => $order,
        ];
        
        $this->viewOnly('checkout/demo-payment', $data);
    }
    
    /**
     * Demo ödeme işlemi
     */
    public function processDemoPayment(): void {
        $orderId = (int) $this->input('order_id');
        $action = $this->input('action', 'success');
        
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            $this->redirect('checkout/fail');
            return;
        }
        
        $payment = $this->paymentModel->findByOrderId($orderId);
        
        if ($action === 'success') {
            // Ödeme başarılı
            $this->paymentModel->markAsSuccess($payment['id'], 'DEMO-' . uniqid(), ['demo' => true]);
            $this->orderModel->updatePaymentStatus($orderId, 'paid');
            $this->orderModel->updateStatus($orderId, 'confirmed');
            
            // Stokları düş
            $items = $this->orderModel->getItems($orderId);
            foreach ($items as $item) {
                $this->productModel->updateStock($item['product_id'], $item['size'], $item['quantity']);
            }
            
            // Sepeti temizle
            $_SESSION['cart'] = [];
            
            $_SESSION['completed_order'] = $order['order_number'];
            $this->redirect('checkout/success');
        } else {
            // Ödeme başarısız
            $this->paymentModel->markAsFailed($payment['id'], ['reason' => 'Demo - kullanıcı iptal etti']);
            $this->orderModel->updatePaymentStatus($orderId, 'failed');
            $this->redirect('checkout/fail');
        }
    }
    
    /**
     * PayTR webhook callback
     */
    public function callback(): void {
        $config = $this->paymentConfig['paytr'];
        
        $merchant_oid = $_POST['merchant_oid'] ?? '';
        $status = $_POST['status'] ?? '';
        $total_amount = $_POST['total_amount'] ?? '';
        $hash = $_POST['hash'] ?? '';
        
        // Hash doğrulama
        $hash_str = $merchant_oid . $config['merchant_salt'] . $status . $total_amount;
        $token = base64_encode(hash_hmac('sha256', $hash_str, $config['merchant_key'], true));
        
        if ($hash !== $token) {
            echo 'PAYTR notification failed: bad hash';
            return;
        }
        
        $order = $this->orderModel->findByOrderNumber($merchant_oid);
        if (!$order) {
            echo 'PAYTR notification failed: order not found';
            return;
        }
        
        $payment = $this->paymentModel->findByOrderId($order['id']);
        
        if ($status === 'success') {
            $this->paymentModel->markAsSuccess($payment['id'], $_POST['payment_transaction_id'] ?? '', $_POST);
            $this->orderModel->updatePaymentStatus($order['id'], 'paid');
            $this->orderModel->updateStatus($order['id'], 'confirmed');
            
            // Stokları düş
            $items = $this->orderModel->getItems($order['id']);
            foreach ($items as $item) {
                $this->productModel->updateStock($item['product_id'], $item['size'], $item['quantity']);
            }
        } else {
            $this->paymentModel->markAsFailed($payment['id'], $_POST);
            $this->orderModel->updatePaymentStatus($order['id'], 'failed');
        }
        
        echo 'OK';
    }
    
    /**
     * Başarılı ödeme sayfası
     */
    public function success(): void {
        $orderNumber = $_SESSION['completed_order'] ?? null;
        unset($_SESSION['completed_order'], $_SESSION['pending_order']);
        
        // Sepeti temizle
        $_SESSION['cart'] = [];
        
        $data = [
            'title' => 'Sipariş Tamamlandı',
            'order_number' => $orderNumber,
        ];
        
        $this->view('checkout/success', $data);
    }
    
    /**
     * Başarısız ödeme sayfası
     */
    public function fail(): void {
        unset($_SESSION['pending_order']);
        
        $data = [
            'title' => 'Ödeme Başarısız',
        ];
        
        $this->view('checkout/fail', $data);
    }
    
    /**
     * AJAX: İlçeleri getir
     */
    public function getDistricts(): void {
        $cityId = (int) $this->input('city_id');
        $turkeyAddress = new TurkeyAddress();
        $districts = $turkeyAddress->getDistricts($cityId);
        $this->json(['districts' => $districts]);
    }
    
    /**
     * AJAX: Mahalleleri getir
     */
    public function getNeighborhoods(): void {
        $districtId = (int) $this->input('district_id');
        $turkeyAddress = new TurkeyAddress();
        $neighborhoods = $turkeyAddress->getNeighborhoods($districtId);
        $this->json(['neighborhoods' => $neighborhoods]);
    }
}
