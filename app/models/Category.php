<?php
/**
 * Dızo Wear - Category Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Category extends Model {
    protected $table = 'categories';
    protected $fillable = ['name', 'slug', 'description', 'image', 'parent_id', 'sort_order', 'status'];
    
    /**
     * Aktif kategorileri getir
     */
    public function getActive(): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY sort_order ASC, name ASC"
        );
    }
    
    /**
     * Ana kategorileri getir (alt kategori olmayanlar)
     */
    public function getParents(): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC"
        );
    }
    
    /**
     * Alt kategorileri getir
     */
    public function getChildren(int $parentId): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE parent_id = ? AND status = 'active' ORDER BY sort_order ASC",
            [$parentId]
        );
    }
    
    /**
     * Slug ile kategori bul
     */
    public function findBySlug(string $slug): ?array {
        return $this->findBy('slug', $slug);
    }
    
    /**
     * Kategorideki ürün sayısını getir
     */
    public function getProductCount(int $categoryId): int {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ? AND status = 'active'",
            [$categoryId]
        );
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Menü için kategori ağacı
     */
    public function getTree(): array {
        $parents = $this->getParents();
        foreach ($parents as &$parent) {
            $parent['children'] = $this->getChildren($parent['id']);
        }
        return $parents;
    }
}
