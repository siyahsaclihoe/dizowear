<?php
/**
 * Dızo Wear - API Konfigürasyonu
 * Türkiye adres API ve diğer harici servis ayarları
 */

return [
    // Türkiye Adres API
    'address' => [
        // İl/İlçe/Mahalle verisi için
        'provider' => 'local', // 'local' veya 'api'
        
        // Harici API kullanılacaksa
        'api_url' => 'https://api.example.com/address',
        'api_key' => 'YOUR_API_KEY',
        
        // Cache ayarları
        'cache_enabled' => true,
        'cache_duration' => 86400, // 24 saat (saniye)
        'cache_path' => __DIR__ . '/../cache/address/',
    ],
    
    // Kargo API (Opsiyonel)
    'cargo' => [
        'provider' => 'yurtici', // yurtici, aras, mng, ptt
        'api_key' => '',
        'api_secret' => '',
        'test_mode' => true,
    ],
    
    // SMS API (Opsiyonel)
    'sms' => [
        'provider' => 'netgsm', // netgsm, iletimerkezi, mutlucell
        'api_key' => '',
        'api_secret' => '',
        'sender' => 'DIZOWEAR',
        'enabled' => false,
    ],
    
    // E-posta Ayarları
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'from_address' => 'info@dizowear.com',
        'from_name' => 'Dızo Wear',
    ],
];
