<?php

/**
 * Product Model xử lý dữ liệu cho bảng 'products'
 * Kế thừa từ Model cơ sở để sử dụng các hàm paginate, query và checkExists
 */
class Product extends Model {
    
    // Tên bảng trong Database (khớp với file php-lop.sql của bạn là 'products')
    protected $table = 'products';

    /**
     * Lấy danh sách sản phẩm có phân trang, tìm kiếm và lọc
     * @param int $page Trang hiện tại
     * @param int $limit Số bản ghi mỗi trang
     * @param string $search Từ khóa tìm kiếm tên sản phẩm
     * @param array $filters Bộ lọc giá hoặc danh mục (ví dụ: ['price <' => 10000000])
     */
    public function list($page = 1, $limit = 10, $search = '', $filters = []) {
        /**
         * Sử dụng hàm paginate từ Model cơ sở.
         * Thực hiện LEFT JOIN với bảng 'category' (danh mục) và 'brands' (thương hiệu)
         * để lấy tên hiển thị thay vì chỉ lấy ID.
         */
        return $this->paginate(
            $this->table,
            $page,
            $limit,
            $search,
            $filters,
            'name', // Cột tìm kiếm mặc định theo tên sản phẩm
            "main_t.*, c.name as category_name, b.name as brand_name",
            "LEFT JOIN category c ON main_t.category_id = c.id 
             LEFT JOIN brands b ON main_t.brand_id = b.id"
        );
    }

    /**
     * Lấy thông tin chi tiết một sản phẩm theo ID
     * @param int $id
     */
    public function show($id) {
        $sql = "SELECT main_t.*, c.name as category_name, b.name as brand_name 
                FROM {$this->table} main_t 
                LEFT JOIN category c ON main_t.category_id = c.id 
                LEFT JOIN brands b ON main_t.brand_id = b.id 
                WHERE main_t.id = ? AND main_t.deleted_at IS NULL LIMIT 1";
        
        return $this->query($sql, [$id])->fetch();
    }

    /**
     * Thêm sản phẩm mới vào Database
     * @param array $data Mảng dữ liệu sản phẩm
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, price, image, category_id, brand_id, created_at) 
                VALUES (:name, :price, :image, :category_id, :brand_id, NOW())";
        
        return $this->query($sql, [
            'name'        => $data['name'],
            'price'       => $data['price'],
            'image'       => $data['image'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'brand_id'    => $data['brand_id'] ?? null
        ]);
    }

    /**
     * Cập nhật thông tin sản phẩm hiện có
     * @param int $id
     * @param array $data
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                price = :price, 
                image = :image, 
                category_id = :category_id, 
                brand_id = :brand_id,
                updated_at = NOW()
                WHERE id = :id";
        
        $params = [
            'id'          => $id,
            'name'        => $data['name'],
            'price'       => $data['price'],
            'image'       => $data['image'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'brand_id'    => $data['brand_id'] ?? null
        ];

        return $this->query($sql, $params);
    }

    /**
     * Xóa mềm sản phẩm (Soft Delete)
     * Chỉ cập nhật cột deleted_at để giữ lại lịch sử dữ liệu
     * @param int $id
     */
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    /**
     * Kiểm tra tên sản phẩm đã tồn tại hay chưa
     * Thường dùng để tránh trùng lặp khi thêm mới hoặc cập nhật
     */
    public function exists($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }
}