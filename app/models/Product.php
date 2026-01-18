<?php
/**
 * Dızo Wear - Product Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Product extends Model {
    protected $table = 'products';
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'price', 'sale_price', 'status', 'is_featured', 'is_new'];
    
    /**
     * Aktif ürünleri getir (frontend için)
     */
    public function getActive(int $limit = 12, int $offset = 0): array {
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }
    
    /**
     * Öne çıkan ürünleri getir
     */
    public function getFeatured(int $limit = 8): array {
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Yeni gelen ürünleri getir
     */
    public function getNewArrivals(int $limit = 8): array {
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_new = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    /**
     * Kategoriye göre ürünleri getir
     */
    public function getByCategory(int $categoryId, int $limit = 12, int $offset = 0): array {
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.category_id = ?
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$categoryId, $limit, $offset]);
    }
    
    /**
     * Slug ile ürün bul
     */
    public function findBySlug(string $slug): ?array {
        $sql = "SELECT p.*, c.name as category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? AND p.status = 'active'";
        return $this->db->fetch($sql, [$slug]);
    }
    
    /**
     * Ürün detayını tüm bilgileriyle getir
     */
    public function getFullDetail(int $id): ?array {
        $product = $this->find($id);
        if (!$product) {
            return null;
        }
        
        // Görseller
        $product['images'] = $this->getImages($id);
        
        // Bedenler ve stoklar
        $product['sizes'] = $this->getSizes($id);
        
        // Kategori
        if ($product['category_id']) {
            $product['category'] = $this->db->fetch(
                "SELECT * FROM categories WHERE id = ?",
                [$product['category_id']]
            );
        }
        
        return $product;
    }
    
    /**
     * Ürün görsellerini getir
     */
    public function getImages(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC",
            [$productId]
        );
    }
    
    /**
     * Ürün bedenlerini getir
     */
    public function getSizes(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM product_sizes WHERE product_id = ? ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')",
            [$productId]
        );
    }
    
    /**
     * Beden stoku güncelle
     */
    public function updateStock(int $productId, string $size, int $quantity): bool {
        $this->db->query(
            "UPDATE product_sizes SET stock = stock - ? WHERE product_id = ? AND size = ?",
            [$quantity, $productId, $size]
        );
        return true;
    }
    
    /**
     * Stok kontrolü
     */
    public function checkStock(int $productId, string $size, int $quantity): bool {
        $result = $this->db->fetch(
            "SELECT stock FROM product_sizes WHERE product_id = ? AND size = ?",
            [$productId, $size]
        );
        return $result && $result['stock'] >= $quantity;
    }
    
    /**
     * Ürün ara
     */
    public function search(string $query, int $limit = 20): array {
        $query = '%' . $query . '%';
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
                ORDER BY p.name ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$query, $query, $limit]);
    }
    
    /**
     * Görsel ekle
     */
    public function addImage(int $productId, string $imagePath, bool $isPrimary = false): int {
        if ($isPrimary) {
            // Diğer primary'leri kaldır
            $this->db->query(
                "UPDATE product_images SET is_primary = 0 WHERE product_id = ?",
                [$productId]
            );
        }
        
        $this->db->query(
            "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)",
            [$productId, $imagePath, $isPrimary ? 1 : 0]
        );
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Beden ekle/güncelle
     */
    public function setSize(int $productId, string $size, int $stock): void {
        $exists = $this->db->fetch(
            "SELECT id FROM product_sizes WHERE product_id = ? AND size = ?",
            [$productId, $size]
        );
        
        if ($exists) {
            $this->db->query(
                "UPDATE product_sizes SET stock = ? WHERE id = ?",
                [$stock, $exists['id']]
            );
        } else {
            $this->db->query(
                "INSERT INTO product_sizes (product_id, size, stock) VALUES (?, ?, ?)",
                [$productId, $size, $stock]
            );
        }
    }
    
    /**
     * Belirli bir beden stokunu güncelle (admin için)
     */
    public function updateSize(int $productId, string $size, int $stock): void {
        $this->db->query(
            "UPDATE product_sizes SET stock = ? WHERE product_id = ? AND size = ?",
            [$stock, $productId, $size]
        );
    }
    
    /**
     * Ürün görselini sil
     */
    public function deleteImage(int $imageId): bool {
        $image = $this->db->fetch("SELECT * FROM product_images WHERE id = ?", [$imageId]);
        if ($image) {
            // Dosyayı sil
            $filePath = __DIR__ . '/../../uploads/' . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->db->query("DELETE FROM product_images WHERE id = ?", [$imageId]);
            return true;
        }
        return false;
    }
    
    /**
     * Ürünü sil (görseller ve bedenlerle birlikte)
     */
    public function deleteProduct(int $productId): bool {
        // Görselleri sil
        $images = $this->getImages($productId);
        foreach ($images as $image) {
            $this->deleteImage($image['id']);
        }
        
        // Bedenleri sil
        $this->db->query("DELETE FROM product_sizes WHERE product_id = ?", [$productId]);
        
        // Ürünü sil
        return $this->delete($productId);
    }
}
