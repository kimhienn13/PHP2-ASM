@include('admin.layouts.header')

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Quản lý Thuộc tính & Giá trị</h4>
    </div>

    <div class="row g-4">
        <!-- CỘT 1: QUẢN LÝ MÀU SẮC -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-palette me-2"></i>Màu sắc</h5>
                </div>
                <div class="card-body px-4">
                    <!-- Form thêm Màu -->
                    <!-- SỬA: Action trỏ về adminattribute -->
                    <form action="{{ BASE_URL }}/adminattribute/store" method="POST" class="row g-2 mb-4 align-items-end">
                        <input type="hidden" name="type" value="color">
                        <div class="col-5">
                            <label class="small fw-bold text-secondary">Tên màu</label>
                            <input type="text" name="name" class="form-control bg-light border-0" placeholder="VD: Đỏ, Xanh..." required>
                        </div>
                        <div class="col-4">
                            <label class="small fw-bold text-secondary">Mã màu</label>
                            <input type="color" name="value" class="form-control form-control-color w-100 border-0 bg-light" value="#000000" title="Chọn màu">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </form>

                    <!-- Danh sách Màu -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Màu</th>
                                    <th>Mã Hex</th>
                                    <th class="text-end">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($colors as $color)
                                <tr>
                                    <td>
                                        <span class="d-inline-block border rounded-circle me-2 align-middle" 
                                              style="width: 20px; height: 20px; background-color: {{ $color['value'] }};"></span>
                                        {{ $color['name'] }}
                                    </td>
                                    <td class="small text-muted">{{ $color['value'] }}</td>
                                    <td class="text-end">
                                        <!-- SỬA: Link xóa trỏ về adminattribute -->
                                        <a href="{{ BASE_URL }}/adminattribute/delete?id={{ $color['id'] }}" onclick="return confirm('Xóa màu này?')" class="text-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT 2: QUẢN LÝ KÍCH THƯỚC (SIZE) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold text-success mb-0"><i class="bi bi-rulers me-2"></i>Kích thước (Size)</h5>
                </div>
                <div class="card-body px-4">
                    <!-- Form thêm Size -->
                    <!-- SỬA: Action trỏ về adminattribute -->
                    <form action="{{ BASE_URL }}/adminattribute/store" method="POST" class="row g-2 mb-4 align-items-end">
                        <input type="hidden" name="type" value="size">
                        <div class="col-9">
                            <label class="small fw-bold text-secondary">Tên kích thước</label>
                            <input type="text" name="name" class="form-control bg-light border-0" placeholder="VD: S, M, L, XL, 39, 40..." required>
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-success w-100 fw-bold"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </form>

                    <!-- Danh sách Size -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tên Size</th>
                                    <th class="text-end">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sizes as $size)
                                <tr>
                                    <td class="fw-bold">{{ $size['name'] }}</td>
                                    <td class="text-end">
                                        <!-- SỬA: Link xóa trỏ về adminattribute -->
                                        <a href="{{ BASE_URL }}/adminattribute/delete?id={{ $size['id'] }}" onclick="return confirm('Xóa size này?')" class="text-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.footer')