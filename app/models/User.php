<?php
/**
 * Dızo Wear - User Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'phone', 'role', 'status'];
    
    /**
     * Email ile kullanıcı bul
     */
    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
    }
    
    /**
     * Yeni kullanıcı oluştur
     */
    public function register(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role'] = 'customer';
        $data['status'] = 'active';
        return $this->create($data);
    }
    
    /**
     * Giriş doğrulama
     */
    public function authenticate(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                return null;
            }
            
            // Son giriş zamanını güncelle
            $this->db->query(
                "UPDATE {$this->table} SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // Şifreyi kaldır
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Şifre değiştir
     */
    public function changePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($userId, ['password' => $hash]);
    }
    
    /**
     * Kullanıcı adreslerini getir
     */
    public function getAddresses(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC",
            [$userId]
        );
    }
    
    /**
     * Kullanıcı siparişlerini getir
     */
    public function getOrders(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }
}
