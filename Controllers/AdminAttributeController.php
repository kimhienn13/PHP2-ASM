<?php

/**
 * AdminAttributeController - Quản lý Màu sắc và Kích thước
 */
class AdminAttributeController extends AdminController {
    
    public function index() {
        // GỌI MODEL MỚI: ProductAttribute (Tránh trùng tên Attribute của PHP 8)
        $model = $this->model('ProductAttribute');
        
        $colors = $model->getByType('color');
        $sizes = $model->getByType('size');

        $this->view('admin.Attribute.index', [
            'title'  => 'Quản lý Thuộc tính',
            'colors' => $colors,
            'sizes'  => $sizes
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'] ?? 'size';
            $name = trim($_POST['name'] ?? '');
            $value = trim($_POST['value'] ?? ''); 

            if (empty($name)) {
                $this->redirect('adminattribute'); 
                return;
            }

            // GỌI MODEL MỚI
            $this->model('ProductAttribute')->create([
                'name'  => $name,
                'type'  => $type,
                'value' => ($type === 'color') ? $value : null
            ]);

            $this->redirect('adminattribute');
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // GỌI MODEL MỚI
            $this->model('ProductAttribute')->delete($id);
        }
        $this->redirect('adminattribute');
    }
}