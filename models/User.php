<?php

/**
 * User Model xử lý dữ liệu cho thành viên và xác thực
 * Kế thừa từ Model cơ sở để sử dụng các hàm query, paginate và checkExists.
 */
class User extends Model {
    
    // Tên bảng tương ứng trong Database
    protected $table = 'users';

    /**
     * Lấy danh sách thành viên có phân trang và tìm kiếm
     * @param int $page Trang hiện tại
     * @param int $limit Số bản ghi mỗi trang
     * @param string $search Từ khóa tìm kiếm (theo tên hoặc email)
     */
    public function list($page = 1, $limit = 10, $search = '') {
        return $this->paginate(
            $this->table, 
            $page, 
            $limit, 
            $search, 
            [], 
            'fullname' // Tìm kiếm mặc định theo họ tên
        );
    }

    /**
     * Tìm người dùng theo Email (phục vụ chức năng Đăng nhập)
     * @param string $email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$email])->fetch();
    }

    /**
     * Tìm người dùng theo ID (phục vụ hiển thị thông tin hoặc nạp Modal Sửa)
     * @param int $id
     */
    public function show($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$id])->fetch();
    }

    /**
     * Tạo tài khoản mới (Có xử lý mã hóa mật khẩu)
     * @param array $data Dữ liệu người dùng: fullname, email, password, role
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (fullname, email, password, role, created_at) 
                VALUES (:fullname, :email, :password, :role, NOW())";
        
        /**
         * BẢO MẬT: Mã hóa mật khẩu trước khi lưu vào DB.
         * Tuyệt đối không lưu mật khẩu văn bản thuần (plain text).
         */
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        return $this->query($sql, [
            'fullname' => $data['fullname'],
            'email'    => $data['email'],
            'password' => $hashedPassword,
            'role'     => $data['role'] ?? 'user'
        ]);
    }

    /**
     * Cập nhật thông tin thành viên
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                fullname = :fullname, 
                email = :email, 
                role = :role, 
                updated_at = NOW() 
                WHERE id = :id";
        
        $params = [
            'id'       => $id,
            'fullname' => $data['fullname'],
            'email'    => $data['email'],
            'role'     => $data['role']
        ];

        return $this->query($sql, $params);
    }

    /**
     * Cập nhật mật khẩu mới (Dành cho chức năng đổi mật khẩu hoặc quên mật khẩu)
     */
    public function updatePassword($email, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE {$this->table} SET password = ? WHERE email = ?";
        return $this->query($sql, [$hashed, $email]);
    }

    /**
     * Kiểm tra email đã tồn tại hay chưa (Để tránh đăng ký trùng email)
     * @param string $email
     * @param int|null $excludeId ID cần loại trừ khi kiểm tra lúc chỉnh sửa
     */
    public function exists($email, $excludeId = null) {
        return $this->checkExists($this->table, 'email', $email, $excludeId);
    }

    /**
     * Xóa mềm thành viên
     */
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}