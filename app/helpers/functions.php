<?php
/**
 * Dızo Wear - Helper Functions
 * Genel yardımcı fonksiyonlar
 */

/**
 * URL oluştur
 */
function url(string $path = ''): string {
    $base = '/dizowear';
    return $base . '/' . ltrim($path, '/');
}

/**
 * Asset URL oluştur
 */
function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Upload URL oluştur
 */
function upload(string $path): string {
    return url('uploads/' . ltrim($path, '/'));
}

/**
 * Fiyat formatla
 */
function formatPrice($price, string $currency = 'TL'): string {
    return number_format((float)$price, 2, ',', '.') . ' ' . $currency;
}

/**
 * Tarih formatla
 */
function formatDate(string $date, string $format = 'd.m.Y H:i'): string {
    return date($format, strtotime($date));
}

/**
 * Slug oluştur
 */
function slug(string $text): string {
    // Türkçe karakterleri değiştir
    $turkish = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
    $english = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
    $text = str_replace($turkish, $english, $text);
    
    // Küçük harfe çevir
    $text = strtolower($text);
    
    // Alfanumerik olmayan karakterleri tire ile değiştir
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    
    // Baş ve sondaki tireleri kaldır
    return trim($text, '-');
}

/**
 * Kısa metin oluştur
 */
function excerpt(string $text, int $length = 100): string {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * CSRF token input alanı
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . ($_SESSION['csrf_token'] ?? '') . '">';
}

/**
 * Flash mesajı göster
 */
function flash(): string {
    if (!isset($_SESSION['flash'])) {
        return '';
    }
    
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    
    $type = $flash['type'];
    $message = htmlspecialchars($flash['message']);
    
    $typeClass = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    
    $class = $typeClass[$type] ?? 'alert-info';
    
    return "<div class=\"alert {$class} alert-dismissible fade show\" role=\"alert\">
        {$message}
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
    </div>";
}

/**
 * Aktif sayfa kontrolü
 */
function isActive(string $path): string {
    $current = $_SERVER['REQUEST_URI'] ?? '';
    return strpos($current, $path) !== false ? 'active' : '';
}

/**
 * Güvenli dosya yükleme
 */
function uploadFile(array $file, string $folder = 'products', array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp']): ?string {
    // Hata kontrolü
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // MIME type kontrolü
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }
    
    // Boyut kontrolü (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }
    
    // Benzersiz dosya adı
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    
    // Klasörü oluştur
    $uploadDir = __DIR__ . '/../../uploads/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Dosyayı taşı
    $destination = $uploadDir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $folder . '/' . $filename;
    }
    
    return null;
}

/**
 * Dosya sil
 */
function deleteFile(string $path): bool {
    $fullPath = __DIR__ . '/../../uploads/' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * JSON response
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Sepet toplam tutarını hesapla
 */
function calculateCartTotal(array $cart): array {
    $subtotal = 0;
    $itemCount = 0;
    
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $itemCount += $item['quantity'];
    }
    
    $shippingCost = $subtotal >= 500 ? 0 : 29.90;
    $total = $subtotal + $shippingCost;
    
    return [
        'subtotal' => $subtotal,
        'shipping' => $shippingCost,
        'total' => $total,
        'item_count' => $itemCount,
        'free_shipping' => $subtotal >= 500,
    ];
}

/**
 * Sipariş numarası oluştur
 */
function generateOrderNumber(): string {
    return 'DZW-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Beden sıralaması
 */
function getSizeOrder(): array {
    return ['XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL'];
}

/**
 * Sepetteki ürün sayısını al
 */
function getCartCount(): int {
    $cart = $_SESSION['cart'] ?? [];
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'] ?? 1;
    }
    return $count;
}

/**
 * Temayı al
 */
function getTheme(): string {
    return $_COOKIE['theme'] ?? 'light';
}

