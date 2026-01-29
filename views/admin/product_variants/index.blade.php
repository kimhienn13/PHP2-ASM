@include('admin.layouts.header')

<!-- Styles for Variant Page -->
<style>
    .variant-card { transition: all 0.3s ease; border: 1px solid #f1f5f9; }
    .variant-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-color: #e2e8f0; }
    .color-dot { width: 16px; height: 16px; border-radius: 50%; display: inline-block; border: 1px solid rgba(0,0,0,0.1); }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ BASE_URL }}/adminproduct" class="text-decoration-none text-muted small">Sản phẩm</a></li>
                    <li class="breadcrumb-item active text-dark small fw-bold" aria-current="page">Quản lý kho</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark d-flex align-items-center gap-2">
                <span class="text-primary">#{{ $product['id'] }}</span> {{ $product['name'] }}
            </h4>
        </div>
        <a href="{{ BASE_URL }}/adminproduct" class="btn btn-light border rounded-pill px-4 fw-bold text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row g-4">
        <!-- LEFT: Add Variant Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Thêm Biến Thể Mới</h5>
                    <p class="text-muted small">Kết hợp màu sắc & kích thước</p>
                </div>
                <div class="card-body px-4">
                    @if(isset($_SESSION['success']))
                        <div class="alert alert-success border-0 bg-success-subtle text-success rounded-3 mb-3 small">
                            <i class="bi bi-check-circle me-1"></i> {{ $_SESSION['success'] }}
                        </div>
                        @php unset($_SESSION['success']); @endphp
                    @endif
                    @if(isset($_SESSION['error']))
                        <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 mb-3 small">
                            <i class="bi bi-exclamation-triangle me-1"></i> {{ $_SESSION['error'] }}
                        </div>
                        @php unset($_SESSION['error']); @endphp
                    @endif

                    <form action="{{ BASE_URL }}/adminproduct/variants/store" method="POST">
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        
                        <!-- Select Color -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">MÀU SẮC</label>
                            <select name="color_id" class="form-select bg-light border-0 py-2 rounded-3" required>
                                <option value="">-- Chọn màu --</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color['id'] }}">{{ $color['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small"><a href="{{ BASE_URL }}/admin/attributes" class="text-decoration-none">Quản lý danh sách màu</a></div>
                        </div>

                        <!-- Select Size -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">KÍCH THƯỚC</label>
                            <select name="size_id" class="form-select bg-light border-0 py-2 rounded-3" required>
                                <option value="">-- Chọn size --</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size['id'] }}">{{ $size['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">SỐ LƯỢNG NHẬP</label>
                            <div class="input-group">
                                <button class="btn btn-light border-0" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                                <input type="number" name="quantity" class="form-control border-0 bg-light text-center fw-bold" value="10" min="0" required>
                                <button class="btn btn-light border-0" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Thêm vào kho
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Variant List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Danh Sách Tồn Kho</h5>
                        <p class="text-muted small mb-0">Tổng số lượng: <span class="fw-bold text-primary">{{ $total_stock ?? 0 }}</span> sản phẩm</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary small fw-bold">Biến thể</th>
                                    <th class="py-3 text-secondary small fw-bold text-center">Tồn kho</th>
                                    <th class="py-3 text-secondary small fw-bold text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($variants))
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">Chưa có biến thể nào được cấu hình.</td>
                                    </tr>
                                @else
                                    @foreach($variants as $variant)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;">
                                                    <span class="fw-bold text-dark">{{ $variant['size_name'] }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $variant['color_name'] }}</h6>
                                                    <!-- Optional: Display color dot if hex code exists -->
                                                    <!-- <span class="color-dot" style="background-color: {{ $variant['color_hex'] ?? '#ccc' }}"></span> -->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ BASE_URL }}/adminproduct/variants/update" method="POST" class="d-inline-flex align-items-center justify-content-center">
                                                <input type="hidden" name="variant_id" value="{{ $variant['id'] }}">
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <input type="number" name="quantity" class="form-control text-center border-secondary-subtle fw-bold text-dark" value="{{ $variant['quantity'] }}" min="0">
                                                    <button class="btn btn-outline-secondary" type="submit" title="Cập nhật số lượng">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ BASE_URL }}/adminproduct/variants/delete?id={{ $variant['id'] }}&product_id={{ $product['id'] }}" 
                                               class="btn btn-sm btn-light text-danger border-0 rounded-circle" 
                                               style="width: 32px; height: 32px;"
                                               onclick="return confirm('Xóa biến thể này khỏi kho?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.footer')