<?php
/**
 * Dızo Wear - Payment Configuration
 * Ödeme Sistemi Ayarları - Production Ready
 */

return [
    // Aktif ödeme yöntemi
    'active_gateway' => 'paytr', // 'paytr' veya 'iyzico'
    
    // PayTR Ayarları
    'paytr' => [
        'merchant_id' => '',      // PayTR Merchant ID - Admin panelden girilecek
        'merchant_key' => '',     // PayTR Merchant Key
        'merchant_salt' => '',    // PayTR Merchant Salt
        'test_mode' => false,     // false = Canlı mod
        'api_url' => 'https://www.paytr.com/odeme/api/get-token',
        'iframe_url' => 'https://www.paytr.com/odeme/guvenli/',
        'debug_on' => 0,          // 0 = kapalı, 1 = açık
        'timeout_limit' => 30,    // Dakika
        'no_installment' => 0,    // 0 = taksit var, 1 = taksit yok
        'max_installment' => 12,  // Maksimum taksit sayısı
        'currency' => 'TL',
        'lang' => 'tr',
    ],
    
    // İyzico Ayarları
    'iyzico' => [
        'api_key' => '',          // Admin panelden girilecek
        'secret_key' => '',
        'test_mode' => false,
        'base_url' => 'https://api.iyzipay.com', // Canlı: https://api.iyzipay.com
        'callback_url' => '/checkout/callback',
    ],
    
    // Genel Ödeme Ayarları
    'general' => [
        'currency' => 'TRY',
        'currency_symbol' => '₺',
        'min_order_amount' => 50,        // Minimum sipariş tutarı (TL)
        'free_shipping_limit' => 500,    // Ücretsiz kargo limiti (TL)
        'shipping_cost' => 29.90,        // Standart kargo ücreti (TL)
    ],
    
    // Callback URL'leri (dinamik oluşturulur)
    'callbacks' => [
        'success' => '/checkout/success',
        'fail' => '/checkout/fail',
        'webhook' => '/checkout/callback',
    ],
];
