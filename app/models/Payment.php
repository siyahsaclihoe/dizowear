<?php
/**
 * Dızo Wear - Payment Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Payment extends Model {
    protected $table = 'payments';
    protected $fillable = [
        'order_id', 'transaction_id', 'payment_method', 'amount',
        'status', 'response_data', 'ip_address'
    ];
    
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    
    /**
     * Ödeme kaydı oluştur
     */
    public function createPayment(int $orderId, string $method, float $amount): int {
        return $this->create([
            'order_id' => $orderId,
            'payment_method' => $method,
            'amount' => $amount,
            'status' => self::STATUS_PENDING,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
    
    /**
     * Ödemeyi başarılı olarak işaretle
     */
    public function markAsSuccess(int $paymentId, string $transactionId, array $responseData = []): bool {
        return $this->db->query(
            "UPDATE {$this->table} SET status = ?, transaction_id = ?, response_data = ?, updated_at = NOW() WHERE id = ?",
            [self::STATUS_SUCCESS, $transactionId, json_encode($responseData), $paymentId]
        ) !== false;
    }
    
    /**
     * Ödemeyi başarısız olarak işaretle
     */
    public function markAsFailed(int $paymentId, array $responseData = []): bool {
        return $this->db->query(
            "UPDATE {$this->table} SET status = ?, response_data = ?, updated_at = NOW() WHERE id = ?",
            [self::STATUS_FAILED, json_encode($responseData), $paymentId]
        ) !== false;
    }
    
    /**
     * Sipariş ID'sine göre ödemeyi bul
     */
    public function findByOrderId(int $orderId): ?array {
        return $this->findBy('order_id', $orderId);
    }
    
    /**
     * Transaction ID'sine göre bul
     */
    public function findByTransactionId(string $transactionId): ?array {
        return $this->findBy('transaction_id', $transactionId);
    }
    
    /**
     * Ödeme loglarını getir
     */
    public function getLogs(int $limit = 50): array {
        return $this->db->fetchAll(
            "SELECT p.*, o.order_number 
             FROM {$this->table} p
             LEFT JOIN orders o ON p.order_id = o.id
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
}
