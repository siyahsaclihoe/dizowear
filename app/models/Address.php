<?php
/**
 * Dızo Wear - Address Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Address extends Model {
    protected $table = 'addresses';
    protected $fillable = [
        'user_id', 'title', 'name', 'phone', 'city', 'district', 'neighborhood',
        'address', 'postal_code', 'is_default'
    ];
    
    /**
     * Kullanıcının adreslerini getir
     */
    public function getByUser(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY is_default DESC, id DESC",
            [$userId]
        );
    }
    
    /**
     * Varsayılan adresi getir
     */
    public function getDefault(int $userId): ?array {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND is_default = 1",
            [$userId]
        );
    }
    
    /**
     * Varsayılan adres olarak ayarla
     */
    public function setAsDefault(int $addressId, int $userId): bool {
        // Önce tüm adreslerin varsayılan özelliğini kaldır
        $this->db->query(
            "UPDATE {$this->table} SET is_default = 0 WHERE user_id = ?",
            [$userId]
        );
        
        // Seçilen adresi varsayılan yap
        return $this->update($addressId, ['is_default' => 1]);
    }
    
    /**
     * Adresi formatlı metin olarak getir
     */
    public function formatAddress(array $address): string {
        $neighborhood = $address['neighborhood'] ?? '';
        return sprintf(
            "%s\n%s\n%s, %s/%s\n%s",
            $address['name'],
            $address['address'],
            $neighborhood,
            $address['district'],
            $address['city'],
            $address['phone']
        );
    }
    
    /**
     * Kullanıcıya yeni adres ekle
     */
    public function addAddress(int $userId, array $data): int {
        $data['user_id'] = $userId;
        
        // Eğer bu ilk adres ise varsayılan yap
        $existingAddresses = $this->getByUser($userId);
        if (empty($existingAddresses)) {
            $data['is_default'] = 1;
        }
        
        return $this->create($data);
    }
    
    /**
     * Adresi güncelle (kullanıcı doğrulaması ile)
     */
    public function updateAddress(int $addressId, int $userId, array $data): bool {
        // Adresin bu kullanıcıya ait olduğunu kontrol et
        $address = $this->find($addressId);
        if (!$address || $address['user_id'] != $userId) {
            return false;
        }
        
        return $this->update($addressId, $data);
    }
    
    /**
     * Adresi sil (kullanıcı doğrulaması ile)
     */
    public function deleteAddress(int $addressId, int $userId): bool {
        $address = $this->find($addressId);
        if (!$address || $address['user_id'] != $userId) {
            return false;
        }
        
        return $this->delete($addressId);
    }
}

