<?php

class Variant extends Model {
    
    // Tên bảng trong CSDL
    protected $table = 'product_variants';

    /**
     * Lấy danh sách biến thể theo ID sản phẩm
     * Kèm theo tên Màu và tên Size (JOIN bảng attributes)
     */
    public function getByProductId($product_id) {
        $sql = "SELECT v.*, 
                       c.name as color_name, c.value as color_hex,
                       s.name as size_name
                FROM {$this->table} v
                LEFT JOIN attributes c ON v.color_id = c.id
                LEFT JOIN attributes s ON v.size_id = s.id
                WHERE v.product_id = :product_id
                ORDER BY s.id ASC, c.id ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra xem biến thể đã tồn tại chưa
     * ĐÃ ĐỔI TÊN HÀM TỪ checkExists -> checkVariantExists
     * Để tránh lỗi "Declaration must be compatible with Model::checkExists"
     */
    public function checkVariantExists($product_id, $color_id, $size_id) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE product_id = :pid AND color_id = :cid AND size_id = :sid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'pid' => $product_id, 
            'cid' => $color_id, 
            'sid' => $size_id
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Tạo biến thể mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (product_id, color_id, size_id, quantity) 
                VALUES (:product_id, :color_id, :size_id, :quantity)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Cập nhật số lượng
     */
    public function updateQuantity($id, $quantity) {
        $sql = "UPDATE {$this->table} SET quantity = :qty WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['qty' => $quantity, 'id' => $id]);
    }

    /**
     * Xóa biến thể
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}