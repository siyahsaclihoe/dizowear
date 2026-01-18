<?php
/**
 * Dızo Wear - Auth Controller
 * Kullanıcı giriş/çıkış/kayıt işlemleri
 */

require_once __DIR__ . '/../helpers/Controller.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Giriş sayfası
     */
    public function loginForm(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('account');
            return;
        }
        
        $this->view('auth/login', ['title' => 'Giriş Yap']);
    }
    
    /**
     * Giriş işlemi
     */
    public function login(): void {
        $this->verifyCsrf();
        
        $email = $this->input('email');
        $password = $_POST['password'] ?? ''; // Şifreyi temizleme
        $remember = $this->input('remember') === 'on';
        
        if (empty($email) || empty($password)) {
            $this->flash('error', 'E-posta ve şifre gereklidir.');
            $this->redirect('login');
            return;
        }
        
        $user = $this->userModel->authenticate($email, $password);
        
        if (!$user) {
            $this->flash('error', 'E-posta veya şifre hatalı.');
            $this->redirect('login');
            return;
        }
        
        // Session'a kullanıcıyı kaydet
        $_SESSION['user'] = $user;
        
        // "Beni hatırla" cookie
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            // Token'ı veritabanına kaydet (opsiyonel)
            setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);
        }
        
        $this->flash('success', 'Hoş geldiniz, ' . $user['name'] . '!');
        
        // Redirect to intended URL or account
        $redirect = $_SESSION['intended_url'] ?? 'account';
        unset($_SESSION['intended_url']);
        $this->redirect($redirect);
    }
    
    /**
     * Kayıt sayfası
     */
    public function registerForm(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('account');
            return;
        }
        
        $this->view('auth/register', ['title' => 'Kayıt Ol']);
    }
    
    /**
     * Kayıt işlemi
     */
    public function register(): void {
        $this->verifyCsrf();
        
        $data = $this->only(['name', 'email', 'phone']);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validasyon
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Ad soyad gereklidir.';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi girin.';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Şifre en az 6 karakter olmalıdır.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Şifreler eşleşmiyor.';
        }
        
        // E-posta kontrolü
        if ($this->userModel->findByEmail($data['email'])) {
            $errors[] = 'Bu e-posta adresi zaten kayıtlı.';
        }
        
        if (!empty($errors)) {
            $this->flash('error', implode('<br>', $errors));
            $this->redirect('register');
            return;
        }
        
        // Kullanıcıyı kaydet
        $data['password'] = $password;
        $userId = $this->userModel->register($data);
        
        // Otomatik giriş yap
        $user = $this->userModel->find($userId);
        unset($user['password']);
        $_SESSION['user'] = $user;
        
        $this->flash('success', 'Hesabınız oluşturuldu. Hoş geldiniz!');
        $this->redirect('account');
    }
    
    /**
     * Çıkış işlemi
     */
    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
        
        // Remember token cookie'sini sil
        setcookie('remember_token', '', time() - 3600, '/');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->flash('success', 'Başarıyla çıkış yaptınız.');
        $this->redirect('');
    }
    
    /**
     * Şifremi unuttum sayfası
     */
    public function forgotPasswordForm(): void {
        $this->view('auth/forgot-password', ['title' => 'Şifremi Unuttum']);
    }
    
    /**
     * Şifre sıfırlama e-postası gönder
     */
    public function forgotPassword(): void {
        $this->verifyCsrf();
        
        $email = $this->input('email');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Geçerli bir e-posta adresi girin.');
            $this->redirect('forgot-password');
            return;
        }
        
        $user = $this->userModel->findByEmail($email);
        
        // Güvenlik: Kullanıcı olup olmadığını belli etme
        $this->flash('success', 'E-posta adresinize şifre sıfırlama linki gönderildi.');
        $this->redirect('login');
        
        if ($user) {
            // Gerçek projede: Token oluştur ve e-posta gönder
            // $token = bin2hex(random_bytes(32));
            // E-posta gönderimi
        }
    }
}
