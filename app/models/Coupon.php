<?php
/**
 * Dızo Wear - Coupon Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Coupon extends Model {
    protected $table = 'coupons';
    protected $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_discount',
        'usage_limit', 'used_count', 'start_date', 'end_date', 'status'
    ];
    
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';
    
    /**
     * Kupon kodu ile bul
     */
    public function findByCode(string $code): ?array {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [strtoupper(trim($code))]
        );
    }
    
    /**
     * Kuponu doğrula
     */
    public function validate(string $code, float $orderTotal): array {
        $coupon = $this->findByCode($code);
        
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Kupon kodu bulunamadı.'];
        }
        
        if ($coupon['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Bu kupon artık aktif değil.'];
        }
        
        // Tarih kontrolü
        $now = date('Y-m-d H:i:s');
        if ($coupon['start_date'] && $now < $coupon['start_date']) {
            return ['valid' => false, 'message' => 'Bu kupon henüz aktif değil.'];
        }
        if ($coupon['end_date'] && $now > $coupon['end_date']) {
            return ['valid' => false, 'message' => 'Bu kuponun süresi dolmuş.'];
        }
        
        // Kullanım limiti kontrolü
        if ($coupon['usage_limit'] !== null && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Bu kupon kullanım limitine ulaştı.'];
        }
        
        // Minimum sipariş tutarı kontrolü
        if ($coupon['min_order_amount'] > 0 && $orderTotal < $coupon['min_order_amount']) {
            return [
                'valid' => false, 
                'message' => sprintf('Bu kupon için minimum sipariş tutarı %.2f TL.', $coupon['min_order_amount'])
            ];
        }
        
        // İndirim hesapla
        $discount = $this->calculateDiscount($coupon, $orderTotal);
        
        return [
            'valid' => true,
            'message' => 'Kupon uygulandı!',
            'coupon' => $coupon,
            'discount' => $discount
        ];
    }
    
    /**
     * İndirim tutarını hesapla
     */
    public function calculateDiscount(array $coupon, float $orderTotal): float {
        if ($coupon['type'] === self::TYPE_PERCENTAGE) {
            $discount = $orderTotal * ($coupon['value'] / 100);
        } else {
            $discount = $coupon['value'];
        }
        
        // Maksimum indirim kontrolü
        if ($coupon['max_discount'] !== null && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }
        
        // İndirim sipariş tutarını aşamaz
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }
        
        return round($discount, 2);
    }
    
    /**
     * Kupon kullanımını artır
     */
    public function incrementUsage(int $couponId): bool {
        return $this->db->query(
            "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = ?",
            [$couponId]
        ) !== false;
    }
    
    /**
     * Aktif kuponları getir
     */
    public function getActive(): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC"
        );
    }
    
    /**
     * Kupon kodu oluştur
     */
    public static function generateCode(int $length = 8): string {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }
}
