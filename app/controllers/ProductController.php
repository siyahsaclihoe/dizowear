<?php
/**
 * Dızo Wear - Product Controller
 * Ürün listeleme ve detay işlemleri
 */

require_once __DIR__ . '/../helpers/Controller.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    /**
     * Ürün listesi
     */
    public function index(): void {
        $page = (int) ($this->input('page', 1));
        $categorySlug = $this->input('category');
        $sort = $this->input('sort', 'newest');
        
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        // Kategoriye göre filtrele
        if ($categorySlug) {
            $category = $this->categoryModel->findBySlug($categorySlug);
            if ($category) {
                $products = $this->productModel->getByCategory($category['id'], $perPage, $offset);
                $total = $this->productModel->count("category_id = ? AND status = 'active'", [$category['id']]);
            } else {
                $products = [];
                $total = 0;
            }
        } else {
            $products = $this->productModel->getActive($perPage, $offset);
            $total = $this->productModel->count("status = 'active'");
            $category = null;
        }
        
        $totalPages = ceil($total / $perPage);
        
        $data = [
            'title' => $category ? $category['name'] : 'Tüm Ürünler',
            'products' => $products,
            'categories' => $this->categoryModel->getActive(),
            'currentCategory' => $category,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'hasMore' => $page < $totalPages,
            ],
            'sort' => $sort,
        ];
        
        $this->view('products/index', $data);
    }
    
    /**
     * Ürün detay
     */
    public function show(string $slug): void {
        $product = $this->productModel->findBySlug($slug);
        
        if (!$product) {
            header('HTTP/1.1 404 Not Found');
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }
        
        // Ürün resimlerini ve bedenlerini al
        $product['images'] = $this->productModel->getImages($product['id']);
        $product['sizes'] = $this->productModel->getSizes($product['id']);
        
        // Benzer ürünler
        $related = [];
        if ($product['category_id']) {
            $related = $this->productModel->getByCategory($product['category_id'], 4);
            // Mevcut ürünü çıkar
            $related = array_filter($related, fn($p) => $p['id'] !== $product['id']);
        }
        
        $data = [
            'title' => $product['name'],
            'product' => $product,
            'related' => array_values($related),
        ];
        
        $this->view('products/show', $data);
    }
    
    /**
     * Kategoriye göre ürünler (ayrı route)
     */
    public function category(string $slug): void {
        $_GET['category'] = $slug;
        $this->index();
    }
    
    /**
     * AJAX: Ürün arama (autocomplete)
     */
    public function ajaxSearch(): void {
        $query = $this->input('q', '');
        
        if (strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }
        
        $products = $this->productModel->search($query, 5);
        
        $results = array_map(function($p) {
            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'price' => formatPrice($p['sale_price'] ?: $p['price']),
                'image' => $p['image'] ? upload($p['image']) : asset('images/no-image.jpg'),
                'url' => url('product/' . $p['slug']),
            ];
        }, $products);
        
        $this->json(['results' => $results]);
    }
}
