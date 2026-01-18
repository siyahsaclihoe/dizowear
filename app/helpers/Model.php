<?php
/**
 * Dızo Wear - Base Model
 * Tüm modellerin miras aldığı ana sınıf
 */

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = Database::getInstance();
    }
    
    /**
     * Tüm kayıtları getir
     */
    public function all(string $orderBy = 'id', string $direction = 'DESC'): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * ID ile tek kayıt bul
     */
    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Koşula göre tek kayıt bul
     */
    public function findBy(string $column, $value): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetch($sql, [$value]);
    }
    
    /**
     * Koşula göre tüm kayıtları getir
     */
    public function where(string $column, $value, string $operator = '='): array {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
        return $this->db->fetchAll($sql, [$value]);
    }
    
    /**
     * Yeni kayıt ekle
     */
    public function create(array $data): int {
        // Sadece fillable alanları al
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->query($sql, array_values($data));
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Kayıt güncelle
     */
    public function update(int $id, array $data): bool {
        // Sadece fillable alanları al
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = ?";
        $values = array_merge(array_values($data), [$id]);
        
        $this->db->query($sql, $values);
        return true;
    }
    
    /**
     * Kayıt sil
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $this->db->query($sql, [$id]);
        return true;
    }
    
    /**
     * Kayıt sayısı
     */
    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $result = $this->db->fetch($sql, $params);
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Sayfalama ile getir
     */
    public function paginate(int $page = 1, int $perPage = 12, string $orderBy = 'id', string $direction = 'DESC'): array {
        $offset = ($page - 1) * $perPage;
        $total = $this->count();
        $totalPages = ceil($total / $perPage);
        
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->fetchAll($sql);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages,
        ];
    }
    
    /**
     * Ham SQL sorgusu
     */
    public function raw(string $sql, array $params = []): array {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Tek satır döndüren ham SQL
     */
    public function rawOne(string $sql, array $params = []): ?array {
        return $this->db->fetch($sql, $params);
    }
}
