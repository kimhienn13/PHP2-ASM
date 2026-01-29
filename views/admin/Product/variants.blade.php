{{-- Chỉ hiển thị Header nếu KHÔNG phải là iframe (chạy độc lập) --}}
@if(empty($is_iframe))
    @include('admin.layouts.header')
@else
    {{-- Nếu là iframe, thêm CSS để tối ưu hiển thị trong modal --}}
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quản lý biến thể</title>
        <!-- Import Bootstrap & Icons (vì không có header) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            body { background-color: #f8fafc; overflow-x: hidden; font-family: sans-serif; }
            .variant-card { border: 1px solid #f1f5f9; }
            .color-dot { width: 16px; height: 16px; border-radius: 50%; display: inline-block; border: 1px solid rgba(0,0,0,0.1); }
            /* Ẩn thanh cuộn thừa */
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: #f1f1f1; }
            ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
        </style>
    </head>
    <body class="p-3">
@endif

{{-- Nếu là iframe, ẩn breadcrumb --}}
@if(empty($is_iframe))
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
@else
    {{-- Tiêu đề nhỏ trong Modal --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0"><span class="badge bg-primary me-2">#{{ $product['id'] }}</span> {{ $product['name'] }}</h5>
        <button onclick="window.location.reload()" class="btn btn-sm btn-light border" title="Làm mới"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
@endif

    <div class="row g-3">
        <!-- LEFT: Add Variant Form -->
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                    <h6 class="fw-bold text-dark mb-0">Thêm Biến Thể</h6>
                </div>
                <div class="card-body px-3 pt-0">
                    <!-- Notification Area -->
                    @if(isset($_SESSION['success']))
                        <div class="alert alert-success border-0 bg-success-subtle text-success p-2 rounded-3 mb-2 small">
                            <i class="bi bi-check-circle me-1"></i> {{ $_SESSION['success'] }}
                        </div>
                        @php unset($_SESSION['success']); @endphp
                    @endif
                    @if(isset($_SESSION['error']))
                        <div class="alert alert-danger border-0 bg-danger-subtle text-danger p-2 rounded-3 mb-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i> {{ $_SESSION['error'] }}
                        </div>
                        @php unset($_SESSION['error']); @endphp
                    @endif

                    <form action="{{ BASE_URL }}/adminproduct/storeVariant" method="POST">
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        
                        {{-- Quan trọng: Truyền tham số is_iframe để controller biết đường redirect --}}
                        @if(!empty($is_iframe)) <input type="hidden" name="is_iframe" value="1"> @endif
                        
                        <!-- Select Color -->
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-secondary mb-1">MÀU SẮC</label>
                            <select name="color_id" class="form-select form-select-sm bg-light border-0" required>
                                <option value="">-- Chọn --</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color['id'] }}">{{ $color['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Select Size -->
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-secondary mb-1">KÍCH THƯỚC</label>
                            <select name="size_id" class="form-select form-select-sm bg-light border-0" required>
                                <option value="">-- Chọn --</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size['id'] }}">{{ $size['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary mb-1">SỐ LƯỢNG</label>
                            <input type="number" name="quantity" class="form-control form-control-sm border-0 bg-light fw-bold" value="10" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Lưu kho
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Variant List -->
        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Danh Sách Tồn Kho</h6>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Tổng: {{ $total_stock ?? 0 }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-3 py-2 text-secondary small fw-bold">Biến thể</th>
                                    <th class="py-2 text-secondary small fw-bold text-center">SL</th>
                                    <th class="py-2 text-secondary small fw-bold text-end pe-3">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($variants))
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">Chưa có dữ liệu kho.</td>
                                    </tr>
                                @else
                                    @foreach($variants as $variant)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                                    <span class="fw-bold text-dark small">{{ $variant['size_name'] }}</span>
                                                </div>
                                                <div>
                                                    <small class="mb-0 fw-bold text-dark d-block">{{ $variant['color_name'] }}</small>
                                                    @if(!empty($variant['color_hex']))
                                                     <span class="color-dot" style="background-color: {{ $variant['color_hex'] }}; width: 10px; height: 10px;"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ BASE_URL }}/adminproduct/updateVariantStock" method="POST" class="d-inline-flex align-items-center justify-content-center">
                                                <input type="hidden" name="variant_id" value="{{ $variant['id'] }}">
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                @if(!empty($is_iframe)) <input type="hidden" name="is_iframe" value="1"> @endif
                                                
                                                <div class="input-group input-group-sm" style="width: 90px;">
                                                    <input type="number" name="quantity" class="form-control px-1 text-center border-secondary-subtle fw-bold text-dark small" value="{{ $variant['quantity'] }}" min="0">
                                                    <button class="btn btn-outline-secondary px-2" type="submit" title="Lưu">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ BASE_URL }}/adminproduct/deleteVariant?id={{ $variant['id'] }}&product_id={{ $product['id'] }}@if(!empty($is_iframe))&iframe=true@endif" 
                                               class="btn btn-sm btn-light text-danger border-0 rounded-circle p-1" 
                                               onclick="return confirm('Xóa biến thể này?');">
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

@if(empty($is_iframe))
</div>
@include('admin.layouts.footer')
@else
    </body>
    </html>
@endif