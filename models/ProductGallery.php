<?php
class ProductGallery extends Model {
    protected $table = 'product_gallery';

    public function getByProductId($productId) {
        return $this->query("SELECT * FROM {$this->table} WHERE product_id = ?", [$productId])->fetchAll();
    }

    public function add($productId, $image) {
        return $this->query("INSERT INTO {$this->table} (product_id, image) VALUES (?, ?)", [$productId, $image]);
    }

    public function delete($id) {
        return $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}