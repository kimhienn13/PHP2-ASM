<?php

class ProductAttribute extends Model {
    
    // Tên bảng trong CSDL vẫn là 'attributes'
    protected $table = 'attributes';

    /**
     * Lấy tất cả thuộc tính
     */
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thuộc tính theo loại (color hoặc size)
     */
    public function getByType($type) {
        $sql = "SELECT * FROM {$this->table} WHERE type = :type ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm thuộc tính mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, value, type) VALUES (:name, :value, :type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}