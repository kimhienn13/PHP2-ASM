<?php

class ProductVariant extends Model {
    
    protected $table = 'product_variants';

    /**
     * Lấy danh sách biến thể theo ID sản phẩm để hiển thị ở trang chi tiết
     * Sắp xếp theo Size và Màu để hiển thị đẹp mắt
     */
    public function getByProductId($product_id) {
        $sql = "SELECT pv.*, pv.stock as quantity,
                       c.name as color_name, c.value as color_hex,
                       s.name as size_name 
                FROM {$this->table} pv
                LEFT JOIN attributes c ON pv.color_id = c.id
                LEFT JOIN attributes s ON pv.size_id = s.id
                WHERE pv.product_id = :pid
                ORDER BY s.id ASC, c.id ASC"; // Sắp xếp size nhỏ lên trước
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [QUAN TRỌNG] Lấy chi tiết 1 biến thể cụ thể để thêm vào giỏ hàng
     * Hàm này join với bảng products để lấy thêm tên và ảnh gốc nếu biến thể không có ảnh
     */
    public function findVariantDetail($variantId) {
        $sql = "SELECT pv.*, 
                       p.name as product_name, 
                       p.image as product_image,
                       p.price as product_base_price,
                       c.name as color_name, 
                       s.name as size_name
                FROM {$this->table} pv
                JOIN products p ON pv.product_id = p.id
                LEFT JOIN attributes c ON pv.color_id = c.id
                LEFT JOIN attributes s ON pv.size_id = s.id
                WHERE pv.id = :id LIMIT 1";
        
        return $this->query($sql, ['id' => $variantId])->fetch();
    }
    
    /**
     * Kiểm tra trùng lặp biến thể (Màu + Size)
     */
    public function checkDuplicate($product_id, $color_id, $size_id) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE product_id = :pid AND color_id = :cid AND size_id = :sid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $product_id, 'cid' => $color_id, 'sid' => $size_id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Thêm mới biến thể (Admin)
     */
    public function create($data) {
        $stock = $data['quantity'] ?? 0;
        $name  = $data['name'] ?? 'Variant'; 
        $sku   = $data['sku'] ?? ('SKU-' . uniqid());
        $price = $data['price'] ?? 0;

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

    /**
     * Cập nhật thông tin biến thể
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        if (empty($fields)) return false;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
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

    public function deleteByProductId($product_id) {
        $sql = "DELETE FROM {$this->table} WHERE product_id = :pid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['pid' => $product_id]);
    }
}