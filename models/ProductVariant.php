<?php

class ProductVariant extends Model {
    
    protected $table = 'product_variants';

    // ... (Giữ nguyên getByProductId, getSummary, checkDuplicate, updateStock, delete) ...

    public function getByProductId($product_id) {
        $sql = "SELECT pv.*, pv.stock as quantity,
                       c.name as color_name, c.value as color_hex,
                       s.name as size_name 
                FROM {$this->table} pv
                LEFT JOIN attributes c ON pv.color_id = c.id
                LEFT JOIN attributes s ON pv.size_id = s.id
                WHERE pv.product_id = :pid
                ORDER BY pv.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function checkDuplicate($product_id, $color_id, $size_id) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE product_id = :pid AND color_id = :cid AND size_id = :sid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $product_id, 'cid' => $color_id, 'sid' => $size_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function create($data) {
        $stock = $data['quantity'] ?? 0;
        $name = 'Variant'; // Có thể custom
        $sku  = 'SKU-' . uniqid();
        $price = 0;

        $sql = "INSERT INTO {$this->table} (product_id, color_id, size_id, stock, name, sku, price) 
                VALUES (:product_id, :color_id, :size_id, :stock, :name, :sku, :price)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'product_id' => $data['product_id'],
            'color_id'   => $data['color_id'],
            'size_id'    => $data['size_id'],
            'stock'      => $stock,
            'name'       => $name,
            'sku'        => $sku,
            'price'      => $price
        ]);
    }

    public function updateStock($id, $quantity) {
        $sql = "UPDATE {$this->table} SET stock = :qty WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['qty' => $quantity, 'id' => $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function getTotalStock($product_id) {
        $sql = "SELECT SUM(stock) FROM {$this->table} WHERE product_id = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $product_id]);
        return (int)$stmt->fetchColumn(); 
    }

    /**
     * MỚI: Xóa tất cả biến thể theo Product ID (Dùng khi xóa sản phẩm cha)
     */
    public function deleteByProductId($product_id) {
        $sql = "DELETE FROM {$this->table} WHERE product_id = :pid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['pid' => $product_id]);
    }
}