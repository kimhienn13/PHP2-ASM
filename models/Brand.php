<?php

/**
 * Brand Model - Xử lý tương tác dữ liệu với bảng 'brands'.
 * Tương thích hoàn toàn với AdminBrandController để quản lý Modal và hình ảnh.
 */
class Brand extends Model {
    
    // Tên bảng trong Cơ sở dữ liệu
    protected $table = 'brands';

    /**
     * Lấy danh sách thương hiệu có phân trang và tìm kiếm.
     * @param int $page Trang hiện tại.
     * @param int $limit Số bản ghi trên mỗi trang.
     * @param string $search Từ khóa tìm kiếm tên hãng.
     */
    public function list($page = 1, $limit = 10, $search = '') {
        return $this->paginate(
            $this->table,
            $page,
            $limit,
            $search,
            [],
            'name' // Cột thực hiện tìm kiếm mặc định
        );
    }

    /**
     * Lấy toàn bộ danh sách thương hiệu chưa bị xóa.
     * Thường dùng cho các dropdown (select) trong form sản phẩm.
     */
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Lấy thông tin chi tiết một thương hiệu theo ID.
     * Phục vụ việc lấy ảnh cũ và nạp dữ liệu lên Modal Sửa.
     */
    public function show($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$id])->fetch();
    }

    /**
     * Thêm thương hiệu mới.
     * @param array $data Bao gồm name, image, description.
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, image, description, created_at) 
                VALUES (:name, :image, :description, NOW())";
        
        return $this->query($sql, [
            'name'        => $data['name'],
            'image'       => $data['image'] ?? 'default.jpg',
            'description' => $data['description'] ?? null
        ]);
    }

    /**
     * Cập nhật thông tin thương hiệu hiện có.
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                image = :image, 
                description = :description, 
                updated_at = NOW() 
                WHERE id = :id";
        
        return $this->query($sql, [
            'id'          => $id,
            'name'        => $data['name'],
            'image'       => $data['image'],
            'description' => $data['description'] ?? null
        ]);
    }

    /**
     * Xóa mềm thương hiệu.
     */
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    /**
     * Kiểm tra tên thương hiệu đã tồn tại hay chưa (Chống trùng lặp).
     * @param string $name Tên cần kiểm tra.
     * @param int|null $excludeId ID cần loại trừ (dùng khi kiểm tra lúc Sửa).
     */
    public function exists($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }
}