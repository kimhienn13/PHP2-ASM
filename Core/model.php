<?php

/**
 * DỰ ÁN PHP2 - MODEL CƠ SỞ
 * Xử lý kết nối Database (PDO) và cung cấp các phương thức CRUD dùng chung
 */
class Model
{
    // Biến lưu trữ đối tượng kết nối PDO
    protected $db;

    public function __construct()
    {
        /**
         * Lấy thông tin cấu hình từ file .env thông qua biến $_ENV
         * Đã được nạp tại bootstrap.php
         */
        $host     = $_ENV['HOST'] ?? '127.0.0.1';
        $database = $_ENV['DATABASE'] ?? 'php-lop';
        $username = $_ENV['USERNAME'] ?? 'root';
        $password = $_ENV['PASSWORD'] ?? '';
        $charset  = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Báo lỗi qua Exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Trả về mảng kết hợp
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Sử dụng Prepared Statement thật
        ];

        try {
            $this->db = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage());
        }
    }

    /**
     * Phương thức thực thi câu lệnh SQL tổng quát
     * @param string $sql Câu lệnh SQL (với tham số ?)
     * @param array $params Mảng các giá trị tương ứng
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * PHƯƠNG THỨC PHÂN TRANG (PAGINATE) DÙNG CHUNG
     * Hỗ trợ tìm kiếm, lọc và JOIN bảng
     */
    public function paginate(
        string $table, 
        int $page = 1, 
        int $limit = 10, 
        string $search = '', 
        array $filters = [], 
        string $searchCol = 'name',
        string $customSelect = 'main_t.*',
        string $customJoin = ''
    ) {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = " WHERE main_t.deleted_at IS NULL";

        // Xử lý tìm kiếm
        if (!empty($search)) {
            $where .= " AND main_t.{$searchCol} LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        // Xử lý các bộ lọc (filters)
        foreach ($filters as $col => $value) {
            if ($value !== '' && $value !== null) {
                // Tách toán tử nếu có (ví dụ 'price <' => 'price' và '<')
                $parts = explode(' ', trim($col));
                $operator = $parts[1] ?? '=';
                $pureCol = $parts[0];
                
                $paramKey = "f_" . str_replace('.', '_', $pureCol);
                $where .= " AND main_t.{$pureCol} {$operator} :{$paramKey}";
                $params[":{$paramKey}"] = $value;
            }
        }

        // 1. Lấy dữ liệu thực tế
        $sql = "SELECT $customSelect FROM $table main_t $customJoin $where 
                ORDER BY main_t.id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        // 2. Tính toán tổng số trang
        $countSql = "SELECT COUNT(*) FROM $table main_t $customJoin $where";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) { $countStmt->bindValue($k, $v); }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return [
            'data'        => $data,
            'total'       => $total,
            'totalPages'  => ceil($total / $limit)
        ];
    }

    /**
     * Kiểm tra tồn tại dữ liệu (Ví dụ kiểm tra trùng Email hoặc Tên sản phẩm)
     */
    public function checkExists(string $table, string $column, $value, $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM $table WHERE $column = ? AND deleted_at IS NULL";
        $params = [$value];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->query($sql, $params);
        return (int)$stmt->fetchColumn() > 0;
    }
}