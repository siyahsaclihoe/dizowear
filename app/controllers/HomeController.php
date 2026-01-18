<?php
/**
 * Dızo Wear - Home Controller
 * Ana sayfa işlemleri
 */

require_once __DIR__ . '/../helpers/Controller.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Slider.php';

class HomeController extends Controller {
    private $productModel;
    private $categoryModel;
    private $sliderModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->sliderModel = new Slider();
    }
    
    /**
     * Ana sayfa
     */
    public function index(): void {
        $data = [
            'title' => 'Ana Sayfa',
            'sliders' => $this->sliderModel->getActive(),
            'featured' => $this->productModel->getFeatured(8),
            'newArrivals' => $this->productModel->getNewArrivals(8),
            'categories' => $this->categoryModel->getActive(),
        ];
        
        $this->view('home/index', $data);
    }
    
    /**
     * Arama
     */
    public function search(): void {
        $query = $this->input('q', '');
        
        if (strlen($query) < 2) {
            $this->redirect('products');
            return;
        }
        
        $products = $this->productModel->search($query);
        
        $data = [
            'title' => "Arama: $query",
            'query' => $query,
            'products' => $products,
        ];
        
        $this->view('products/index', $data);
    }
    
    /**
     * Hakkımızda
     */
    public function about(): void {
        $this->view('pages/about', ['title' => 'Hakkımızda']);
    }
    
    /**
     * İletişim
     */
    public function contact(): void {
        $this->view('pages/contact', ['title' => 'İletişim']);
    }
    
    /**
     * İletişim formu gönder
     */
    public function sendContact(): void {
        $this->verifyCsrf();
        
        $data = $this->only(['name', 'email', 'subject', 'message']);
        
        // Basit validasyon
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            $this->flash('error', 'Lütfen tüm alanları doldurun.');
            $this->redirect('contact');
            return;
        }
        
        // E-posta gönder (gerçek projede mail gönderimi yapılır)
        // mail($to, $subject, $message);
        
        $this->flash('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
        $this->redirect('contact');
    }
}
