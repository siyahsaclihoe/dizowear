<?php
/**
 * Dızo Wear - Account Controller
 * Kullanıcı hesap yönetimi
 */

require_once __DIR__ . '/../helpers/Controller.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Address.php';

class AccountController extends Controller {
    private $userModel;
    private $orderModel;
    private $addressModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->addressModel = new Address();
    }
    
    /**
     * Hesap ana sayfası
     */
    public function index(): void {
        $userId = $_SESSION['user']['id'];
        
        $data = [
            'title' => 'Hesabım',
            'user' => $this->userModel->find($userId),
            'recentOrders' => array_slice($this->orderModel->getByUser($userId), 0, 5),
            'addressCount' => count($this->addressModel->getByUser($userId)),
        ];
        
        $this->view('account/index', $data);
    }
    
    /**
     * Siparişlerim
     */
    public function orders(): void {
        $userId = $_SESSION['user']['id'];
        $orders = $this->orderModel->getByUser($userId);
        
        $data = [
            'title' => 'Siparişlerim',
            'orders' => $orders,
            'statusLabels' => Order::getStatusLabels(),
            'paymentStatusLabels' => Order::getPaymentStatusLabels(),
        ];
        
        $this->view('account/orders', $data);
    }
    
    /**
     * Sipariş detay
     */
    public function orderDetail(int $orderId): void {
        $userId = $_SESSION['user']['id'];
        $order = $this->orderModel->getWithItems($orderId);
        
        if (!$order || $order['user_id'] != $userId) {
            $this->redirect('account/orders');
            return;
        }
        
        $data = [
            'title' => 'Sipariş #' . $order['order_number'],
            'order' => $order,
            'statusLabels' => Order::getStatusLabels(),
            'paymentStatusLabels' => Order::getPaymentStatusLabels(),
        ];
        
        $this->view('account/order-detail', $data);
    }
    
    /**
     * Adreslerim
     */
    public function addresses(): void {
        $userId = $_SESSION['user']['id'];
        $addresses = $this->addressModel->getByUser($userId);
        
        $turkeyAddress = new TurkeyAddress();
        
        $data = [
            'title' => 'Adreslerim',
            'addresses' => $addresses,
            'cities' => $turkeyAddress->getCities(),
        ];
        
        $this->view('account/addresses', $data);
    }
    
    /**
     * Adres ekle
     */
    public function addAddress(): void {
        $this->verifyCsrf();
        
        $data = $this->only([
            'title', 'name', 'phone', 'city', 'district', 'neighborhood',
            'address', 'postal_code'
        ]);
        
        $data['user_id'] = $_SESSION['user']['id'];
        $data['is_default'] = $this->addressModel->count("user_id = ?", [$data['user_id']]) === 0 ? 1 : 0;
        
        $this->addressModel->create($data);
        
        $this->flash('success', 'Adres eklendi.');
        $this->redirect('account/addresses');
    }
    
    /**
     * Adres sil
     */
    public function deleteAddress(int $addressId): void {
        $userId = $_SESSION['user']['id'];
        $address = $this->addressModel->find($addressId);
        
        if ($address && $address['user_id'] == $userId) {
            $this->addressModel->delete($addressId);
            $this->flash('success', 'Adres silindi.');
        }
        
        $this->redirect('account/addresses');
    }
    
    /**
     * Varsayılan adres yap
     */
    public function setDefaultAddress(int $addressId): void {
        $userId = $_SESSION['user']['id'];
        $address = $this->addressModel->find($addressId);
        
        if ($address && $address['user_id'] == $userId) {
            $this->addressModel->setAsDefault($addressId, $userId);
            $this->flash('success', 'Varsayılan adres güncellendi.');
        }
        
        $this->redirect('account/addresses');
    }
    
    /**
     * Profil ayarları
     */
    public function profile(): void {
        $user = $this->userModel->find($_SESSION['user']['id']);
        
        $data = [
            'title' => 'Profil Ayarları',
            'profile' => $user,
        ];
        
        $this->view('account/profile', $data);
    }
    
    /**
     * Profil güncelle
     */
    public function updateProfile(): void {
        $this->verifyCsrf();
        
        $userId = $_SESSION['user']['id'];
        $data = $this->only(['name', 'phone']);
        
        if (empty($data['name'])) {
            $this->flash('error', 'Ad soyad gereklidir.');
            $this->redirect('account/profile');
            return;
        }
        
        $this->userModel->update($userId, $data);
        $_SESSION['user']['name'] = $data['name'];
        $_SESSION['user']['phone'] = $data['phone'];
        
        $this->flash('success', 'Profil güncellendi.');
        $this->redirect('account/profile');
    }
    
    /**
     * Şifre değiştir
     */
    public function changePassword(): void {
        $this->verifyCsrf();
        
        $userId = $_SESSION['user']['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Mevcut şifreyi doğrula
        $user = $this->userModel->find($userId);
        if (!password_verify($currentPassword, $user['password'])) {
            $this->flash('error', 'Mevcut şifre hatalı.');
            $this->redirect('account/profile');
            return;
        }
        
        // Yeni şifre kontrolü
        if (strlen($newPassword) < 6) {
            $this->flash('error', 'Yeni şifre en az 6 karakter olmalıdır.');
            $this->redirect('account/profile');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->flash('error', 'Şifreler eşleşmiyor.');
            $this->redirect('account/profile');
            return;
        }
        
        $this->userModel->changePassword($userId, $newPassword);
        
        $this->flash('success', 'Şifreniz değiştirildi.');
        $this->redirect('account/profile');
    }
}
