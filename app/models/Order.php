<?php
/**
 * Dızo Wear - Order Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Order extends Model {
    protected $table = 'orders';
    protected $fillable = [
        'user_id', 'order_number', 'name', 'email', 'phone',
        'address_id', 'shipping_address', 'billing_address',
        'subtotal', 'shipping_cost', 'total', 'status', 'payment_status',
        'payment_method', 'notes'
    ];
    
    // Sipariş durumları
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    
    // Ödeme durumları
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    
    /**
     * Yeni sipariş oluştur
     */
    public function createOrder(array $data, array $items): int {
        // Sipariş numarası oluştur
        $data['order_number'] = $this->generateOrderNumber();
        $data['status'] = self::STATUS_PENDING;
        $data['payment_status'] = self::PAYMENT_PENDING;
        
        $orderId = $this->create($data);
        
        // Sipariş kalemlerini ekle
        foreach ($items as $item) {
            $this->addItem($orderId, $item);
        }
        
        return $orderId;
    }
    
    /**
     * Sipariş kalemi ekle
     */
    private function addItem(int $orderId, array $item): void {
        $this->db->query(
            "INSERT INTO order_items (order_id, product_id, product_name, size, quantity, price, total)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $orderId,
                $item['product_id'],
                $item['name'],
                $item['size'],
                $item['quantity'],
                $item['price'],
                $item['price'] * $item['quantity']
            ]
        );
    }
    
    /**
     * Sipariş numarası oluştur
     */
    private function generateOrderNumber(): string {
        return 'DZW-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    /**
     * Sipariş numarası ile bul
     */
    public function findByOrderNumber(string $orderNumber): ?array {
        return $this->findBy('order_number', $orderNumber);
    }
    
    /**
     * Sipariş detayları ile birlikte getir
     */
    public function getWithItems(int $orderId): ?array {
        $order = $this->find($orderId);
        if (!$order) {
            return null;
        }
        
        $order['items'] = $this->getItems($orderId);
        return $order;
    }
    
    /**
     * Sipariş kalemlerini getir
     */
    public function getItems(int $orderId): array {
        return $this->db->fetchAll(
            "SELECT oi.*, p.slug as product_slug,
             (SELECT image_path FROM product_images WHERE product_id = oi.product_id ORDER BY is_primary DESC LIMIT 1) as image
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }
    
    /**
     * Kullanıcının siparişlerini getir
     */
    public function getByUser(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }
    
    /**
     * Sipariş durumunu güncelle
     */
    public function updateStatus(int $orderId, string $status): bool {
        return $this->update($orderId, ['status' => $status]);
    }
    
    /**
     * Ödeme durumunu güncelle
     */
    public function updatePaymentStatus(int $orderId, string $status): bool {
        return $this->update($orderId, ['payment_status' => $status]);
    }
    
    /**
     * Duruma göre siparişleri getir
     */
    public function getByStatus(string $status): array {
        return $this->where('status', $status);
    }
    
    /**
     * Bugünün siparişlerini getir
     */
    public function getTodayOrders(): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC"
        );
    }
    
    /**
     * Son siparişleri getir
     */
    public function getRecent(int $limit = 10): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Toplam satış tutarını getir
     */
    public function getTotalSales(): float {
        $result = $this->db->fetch(
            "SELECT SUM(total) as total FROM {$this->table} WHERE payment_status = 'paid'"
        );
        return (float) ($result['total'] ?? 0);
    }
    
    /**
     * Durum etiketlerini getir
     */
    public static function getStatusLabels(): array {
        return [
            self::STATUS_PENDING => 'Beklemede',
            self::STATUS_CONFIRMED => 'Onaylandı',
            self::STATUS_PROCESSING => 'Hazırlanıyor',
            self::STATUS_SHIPPED => 'Kargoya Verildi',
            self::STATUS_DELIVERED => 'Teslim Edildi',
            self::STATUS_CANCELLED => 'İptal Edildi',
        ];
    }
    
    /**
     * Ödeme durumu etiketlerini getir
     */
    public static function getPaymentStatusLabels(): array {
        return [
            self::PAYMENT_PENDING => 'Beklemede',
            self::PAYMENT_PAID => 'Ödendi',
            self::PAYMENT_FAILED => 'Başarısız',
            self::PAYMENT_REFUNDED => 'İade Edildi',
        ];
    }
}
