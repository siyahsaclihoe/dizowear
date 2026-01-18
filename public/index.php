<?php
/**
 * Dızo Wear - Public Entry Point
 * Ana giriş dosyası - Tüm istekler buradan yönlendirilir
 */

// Hata raporlama (geliştirme için, canlıda kapatın)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session başlat
session_start();

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Temel yollar
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('APP_PATH', BASE_PATH . '/app');

// Lisans kontrolü
require_once CONFIG_PATH . '/license.php';
License::check();

// Veritabanı bağlantısı
require_once CONFIG_PATH . '/database.php';

// Helper fonksiyonları
require_once APP_PATH . '/helpers/functions.php';
require_once APP_PATH . '/helpers/Router.php';
require_once APP_PATH . '/helpers/Controller.php';

// Router oluştur
$router = new Router();

// ========== ROUTES ==========

// Ana sayfa
$router->get('/', 'HomeController@index');
$router->get('/search', 'HomeController@search');
$router->get('/about', 'HomeController@about');
$router->get('/contact', 'HomeController@contact');
$router->post('/contact', 'HomeController@sendContact');

// Ürünler
$router->get('/products', 'ProductController@index');
$router->get('/product/{slug}', 'ProductController@show');
$router->get('/category/{slug}', 'ProductController@category');
$router->get('/products/search', 'ProductController@ajaxSearch');

// Sepet
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/apply-coupon', 'CartController@applyCoupon');
$router->get('/cart/summary', 'CartController@summary');
$router->get('/cart/clear', 'CartController@clear');

// Checkout
$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout/place-order', 'CheckoutController@placeOrder');
$router->get('/checkout/payment/{id}', 'CheckoutController@payment');
$router->get('/checkout/demo-payment/{id}', 'CheckoutController@demoPayment');
$router->post('/checkout/process-demo', 'CheckoutController@processDemoPayment');
$router->post('/checkout/callback', 'CheckoutController@callback');
$router->get('/checkout/success', 'CheckoutController@success');
$router->get('/checkout/fail', 'CheckoutController@fail');
$router->get('/checkout/districts', 'CheckoutController@getDistricts');
$router->get('/checkout/neighborhoods', 'CheckoutController@getNeighborhoods');

// Auth
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/forgot-password', 'AuthController@forgotPassword');

// Hesap
$router->get('/account', 'AccountController@index');
$router->get('/account/orders', 'AccountController@orders');
$router->get('/account/order/{id}', 'AccountController@orderDetail');
$router->get('/account/addresses', 'AccountController@addresses');
$router->post('/account/address/add', 'AccountController@addAddress');
$router->get('/account/address/delete/{id}', 'AccountController@deleteAddress');
$router->get('/account/address/default/{id}', 'AccountController@setDefaultAddress');
$router->get('/account/profile', 'AccountController@profile');
$router->post('/account/update-profile', 'AccountController@updateProfile');
$router->post('/account/change-password', 'AccountController@changePassword');

// ========== DISPATCH ==========
$router->dispatch();
