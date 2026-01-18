<?php
/**
 * Dızo Wear - Slider Model
 */

require_once __DIR__ . '/../helpers/Model.php';

class Slider extends Model {
    protected $table = 'sliders';
    protected $fillable = ['title', 'subtitle', 'image', 'button_text', 'button_link', 'sort_order', 'status'];
    
    /**
     * Aktif sliderları getir
     */
    public function getActive(): array {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY sort_order ASC"
        );
    }
    
    /**
     * Sıralama güncelle
     */
    public function updateOrder(array $ids): void {
        foreach ($ids as $index => $id) {
            $this->update($id, ['sort_order' => $index]);
        }
    }
}
