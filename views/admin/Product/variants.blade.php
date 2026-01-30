{{-- XỬ LÝ HEADER: Kiểm tra biến is_iframe để hiển thị phù hợp --}}
@php
    $isIframe = !empty($is_iframe);
@endphp

@if(!$isIframe)
    @include('admin.layouts.header')
    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
        <!-- Breadcrumb & Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ BASE_URL }}/adminproduct" class="text-decoration-none text-muted small">Sản phẩm</a></li>
                        <li class="breadcrumb-item active text-dark small fw-bold" aria-current="page">Quản lý kho & Ảnh</li>
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
    {{-- Chế độ Iframe (trong Modal): Chỉ load CSS --}}
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quản lý biến thể</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            body { background-color: #f8fafc; overflow-x: hidden; font-family: sans-serif; }
            .color-dot { width: 16px; height: 16px; border-radius: 50%; display: inline-block; border: 1px solid rgba(0,0,0,0.1); }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: #f1f1f1; }
            ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
            .form-control-xs { height: 28px; padding: 2px 8px; font-size: 12px; }
            input:invalid { border-color: #dc3545; }
        </style>
    </head>
    <body class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h5 class="fw-bold text-dark mb-0"><span class="badge bg-primary me-2">#{{ $product['id'] }}</span> {{ $product['name'] }}</h5>
            
            <div class="d-flex gap-2">
                <button onclick="window.location.reload()" class="btn btn-sm btn-light border" title="Tải lại dữ liệu"><i class="bi bi-arrow-clockwise"></i></button>
                <a href="{{ BASE_URL }}/adminproduct" target="_top" class="btn btn-sm btn-success text-white fw-bold px-3 shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Hoàn tất
                </a>
            </div>
        </div>
@endif

    <!-- KHU VỰC QUẢN LÝ GALLERY (ALBUM ẢNH) -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-header bg-white fw-bold text-dark pt-3 px-3">
            <i class="bi bi-images me-1 text-primary"></i> Album Ảnh Sản Phẩm
        </div>
        <div class="card-body">
            @if(empty($gallery))
                <p class="text-muted small mb-0">Chưa có ảnh nào trong album. Hãy thêm ở Tab "Thông tin chung" hoặc khi Sửa sản phẩm.</p>
            @else
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($gallery as $img)
                        <div class="position-relative border rounded overflow-hidden group-hover-action" style="width: 80px; height: 80px;">
                            <img src="{{ BASE_URL }}/public/uploads/products/{{ $img['image'] }}" class="w-100 h-100 object-fit-cover">
                            <a href="{{ BASE_URL }}/adminproduct/deleteGalleryImage?id={{ $img['id'] }}&product_id={{ $product['id'] }}" 
                               class="position-absolute top-0 end-0 bg-danger text-white p-1 small" 
                               style="line-height: 1; border-bottom-left-radius: 5px;"
                               onclick="return confirm('Xóa ảnh này khỏi album?')">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <!-- CỘT TRÁI: FORM THÊM BIẾN THỂ -->
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                    <h6 class="fw-bold text-dark mb-0">Thêm Biến Thể Mới</h6>
                </div>
                <div class="card-body px-3 pt-0">
                    <!-- Thông báo lỗi/thành công -->
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

                    <form action="{{ BASE_URL }}/adminproduct/storeVariant" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        @if($isIframe) <input type="hidden" name="is_iframe" value="1"> @endif
                        
                        <!-- Màu sắc -->
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-secondary mb-1">MÀU SẮC <span class="text-danger">*</span></label>
                            <select name="color_id" class="form-select form-select-sm bg-light border-0" required>
                                <option value="">-- Chọn --</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color['id'] }}">{{ $color['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kích thước -->
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-secondary mb-1">KÍCH THƯỚC <span class="text-danger">*</span></label>
                            <select name="size_id" class="form-select form-select-sm bg-light border-0" required>
                                <option value="">-- Chọn --</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size['id'] }}">{{ $size['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Giá & Số lượng -->
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-secondary mb-1">GIÁ RIÊNG</label>
                                <input type="number" name="price" class="form-control form-control-sm border-0 bg-light fw-bold" placeholder="" min="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-secondary mb-1">SỐ LƯỢNG <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control form-control-sm border-0 bg-light fw-bold" value="" min="0" required>
                            </div>
                        </div>

                        <!-- [NEW] Ảnh biến thể -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary mb-1">ẢNH BIẾN THỂ (Tùy chọn)</label>
                            <input type="file" name="variant_image" class="form-control form-control-sm border-0 bg-light">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Lưu Biến Thể
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH BIẾN THỂ -->
        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">Danh Sách Cấu Hình</h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Tổng kho: {{ $total_stock ?? 0 }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light sticky-top z-1">
                                <tr>
                                    <th class="ps-3 py-2 text-secondary small fw-bold">Thuộc tính</th>
                                    <th class="py-2 text-secondary small fw-bold">Ảnh riêng</th>
                                    <th class="py-2 text-secondary small fw-bold text-center" width="100">Cập nhật</th>
                                    <th class="py-2 text-secondary small fw-bold text-end pe-3" width="50">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($variants as $variant)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                                <span class="fw-bold text-dark small">{{ $variant['size_name'] }}</span>
                                            </div>
                                            <div>
                                                <small class="mb-0 fw-bold text-dark d-block">{{ $variant['color_name'] }}</small>
                                                @if(!empty($variant['color_hex']))
                                                 <span class="color-dot" style="background-color: {{ $variant['color_hex'] }};"></span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- FORM CẬP NHẬT TRỰC TIẾP --}}
                                    <td colspan="2" class="p-0">
                                        <form action="{{ BASE_URL }}/adminproduct/updateVariantStock" method="POST" enctype="multipart/form-data" class="d-flex align-items-center justify-content-between h-100 py-2">
                                            <input type="hidden" name="variant_id" value="{{ $variant['id'] }}">
                                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                            @if($isIframe) <input type="hidden" name="is_iframe" value="1"> @endif

                                            <!-- [NEW] Cột Ảnh riêng -->
                                            <div class="mx-2 d-flex align-items-center gap-2" style="width: 180px;">
                                                <div class="border rounded overflow-hidden flex-shrink-0" style="width: 35px; height: 35px;">
                                                    @if(!empty($variant['image']))
                                                        <img src="{{ BASE_URL }}/public/uploads/products/{{ $variant['image'] }}" class="w-100 h-100 object-fit-cover">
                                                    @else
                                                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-secondary small"><i class="bi bi-image"></i></div>
                                                    @endif
                                                </div>
                                                <input type="file" name="variant_image" class="form-control form-control-sm text-secondary" style="font-size: 10px; width: 120px;">
                                            </div>

                                            <div class="d-flex align-items-center">
                                                <!-- Giá -->
                                                <input type="number" name="price" 
                                                       class="form-control form-control-sm text-end border-secondary-subtle fw-bold text-primary me-1" 
                                                       style="width: 90px;"
                                                       value="{{ $variant['price'] > 0 ? $variant['price'] : '' }}" 
                                                       placeholder="{{ number_format($product['price']) }}"
                                                       min="0" title="Giá biến thể">
                                                
                                                <!-- Số lượng -->
                                                <div class="input-group input-group-sm" style="width: 90px;">
                                                    <input type="number" name="quantity" 
                                                           class="form-control text-center border-secondary-subtle fw-bold" 
                                                           value="{{ $variant['quantity'] }}" 
                                                           min="0" required>
                                                    <button class="btn btn-outline-success" type="submit" title="Lưu">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>

                                    <td class="text-end pe-3">
                                        <a href="{{ BASE_URL }}/adminproduct/deleteVariant?id={{ $variant['id'] }}&product_id={{ $product['id'] }}{{ $isIframe ? '&iframe=true' : '' }}" 
                                           class="btn btn-sm btn-light text-danger border-0 rounded-circle p-1" 
                                           onclick="return confirm('Xóa biến thể này?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        <i class="bi bi-box-seam display-6 d-block mb-2 text-secondary opacity-50"></i>
                                        Chưa có biến thể nào được tạo.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@if(!$isIframe)
    </div>
    @include('admin.layouts.footer')
@else
    </body>
    </html>
@endif