@php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
@endphp

@include('user.layouts.header')

<!-- NEW LAYOUT: MODERN E-COMMERCE -->
<div class="bg-white border-bottom mb-4">
    <div class="container py-3">
        <!-- Breadcrumb đơn giản -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">
                <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="text-decoration-none text-muted">Sản phẩm</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Chi tiết</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-5">
        <!-- LEFT COLUMN: PRODUCT VISUAL -->
        <div class="col-lg-6">
            <div class="sticky-top" style="top: 100px; z-index: 1;">
                <div class="p-5 bg-light rounded-5 border border-light-subtle d-flex align-items-center justify-content-center mb-3 position-relative overflow-hidden group">
                    <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $product['image'] ?? 'default.jpg' }}" 
                         class="img-fluid w-100 object-fit-contain shadow-lg rounded-4 transition-transform duration-700"
                         style="max-height: 500px;"
                         alt="{{ $product['name'] }}"
                         onerror="this.src='https://placehold.co/800x800?text=TechMart+Product'">
                    
                    <!-- Zoom Hint -->
                    <div class="position-absolute bottom-0 end-0 m-4 text-muted small opacity-50">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </div>
                </div>
                
                <!-- Thumbnails (Giả lập để đẹp giao diện) -->
                <div class="row g-2">
                    <div class="col-3">
                        <div class="border rounded-3 p-2 bg-white text-center cursor-pointer border-primary">
                             <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $product['image'] ?? 'default.jpg' }}" class="img-fluid" style="max-height: 50px;">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 p-2 bg-white text-center cursor-pointer opacity-50">
                             <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $product['image'] ?? 'default.jpg' }}" class="img-fluid grayscale" style="max-height: 50px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: PRODUCT INFO & ACCORDIONS -->
        <div class="col-lg-6">
            <div class="ps-lg-4">
                <!-- Header Info -->
                <div class="mb-4">
                    <h5 class="text-primary fw-bold text-uppercase mb-2 small ls-wider">{{ $product['category_name'] ?? 'Gia dụng' }}</h5>
                    <h1 class="fw-black text-dark display-5 mb-3 lh-sm">{{ $product['name'] }}</h1>
                    
                    <div class="d-flex align-items-center gap-3">
                        <h2 class="text-danger fw-bolder mb-0 display-6">{{ number_format($product['price'], 0, ',', '.') }}đ</h2>
                        <div class="d-flex flex-column lh-1">
                            <span class="text-decoration-line-through text-muted small">{{ number_format($product['price']*1.25, 0, ',', '.') }}đ</span>
                            <span class="badge bg-danger-subtle text-danger px-2 rounded-1" style="font-size: 0.65rem;">TIẾT KIỆM 20%</span>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary opacity-10 my-4">

                <!-- Short Description -->
                <p class="text-secondary lead fs-6 mb-4">
                    {{ $product['description'] ?? 'Sản phẩm chính hãng với thiết kế hiện đại, độ bền cao và tích hợp nhiều công nghệ thông minh giúp cuộc sống tiện nghi hơn.' }}
                </p>

                <!-- Action Buttons -->
                <div class="d-grid gap-3 mb-5">
                    <a href="{{ rtrim(BASE_URL, '/') }}/cart/add/{{ $product['id'] }}" 
                       class="btn btn-dark btn-lg rounded-pill py-3 fw-bold d-flex align-items-center justify-content-center shadow-lg hover-scale">
                        <span>THÊM VÀO GIỎ HÀNG</span>
                        <i class="bi bi-arrow-right ms-3"></i>
                    </a>
                    
                    <div class="row g-2">
                        <div class="col-6">
                             <button class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-semibold">
                                <i class="bi bi-heart me-2"></i>Yêu thích
                             </button>
                        </div>
                        <div class="col-6">
                             <button class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-semibold">
                                <i class="bi bi-share me-2"></i>Chia sẻ
                             </button>
                        </div>
                    </div>
                </div>

                <!-- ACCORDION SECTIONS (Thay thế Tab cũ) -->
                <div class="accordion accordion-flush border rounded-4 overflow-hidden" id="productAccordion">
                    
                    <!-- Mục 1: Thông tin chi tiết -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark bg-white py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true">
                                <i class="bi bi-file-text me-2 text-primary"></i> MÔ TẢ SẢN PHẨM
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#productAccordion">
                            <div class="accordion-body text-muted small lh-lg">
                                <p>Sản phẩm <strong>{{ $product['name'] }}</strong> là giải pháp tối ưu cho không gian sống hiện đại. Được sản xuất trên dây chuyền công nghệ cao, kiểm định nghiêm ngặt về chất lượng.</p>
                                <ul class="mb-0 ps-3">
                                    <li>Chất liệu cao cấp, an toàn sức khỏe.</li>
                                    <li>Thiết kế Ergonomic thân thiện người dùng.</li>
                                    <li>Tiết kiệm năng lượng tiêu chuẩn Quốc tế.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Mục 2: Thông số kỹ thuật -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark bg-white py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                <i class="bi bi-sliders me-2 text-primary"></i> THÔNG SỐ KỸ THUẬT
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                            <div class="accordion-body p-0">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr><td class="ps-4 py-2 text-muted">Thương hiệu</td><td class="fw-bold text-end pe-4">{{ $product['brand_name'] ?? 'TechMart' }}</td></tr>
                                        <tr><td class="ps-4 py-2 text-muted">Danh mục</td><td class="fw-bold text-end pe-4">{{ $product['category_name'] ?? 'Gia dụng' }}</td></tr>
                                        <tr><td class="ps-4 py-2 text-muted">Xuất xứ</td><td class="fw-bold text-end pe-4">Chính hãng</td></tr>
                                        <tr><td class="ps-4 py-2 text-muted">Bảo hành</td><td class="fw-bold text-end pe-4">24 Tháng</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Mục 3: Chính sách -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark bg-white py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                <i class="bi bi-shield-check me-2 text-primary"></i> BẢO HÀNH & ĐỔI TRẢ
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                            <div class="accordion-body small text-muted">
                                <div class="d-flex gap-3 mb-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Bảo hành điện tử 12 tháng toàn quốc.</span>
                                </div>
                                <div class="d-flex gap-3 mb-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Đổi mới trong 30 ngày nếu có lỗi nhà sản xuất.</span>
                                </div>
                                <div class="d-flex gap-3">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Hoàn tiền 111% nếu phát hiện hàng giả.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: RELATED/SUGGESTED (Trang trí thêm) -->
<div class="bg-light py-5 mt-5 border-top">
    <div class="container">
        <h4 class="fw-bold text-center mb-4">CÓ THỂ BẠN SẼ THÍCH</h4>
        <div class="row justify-content-center">
            <div class="col-md-8 text-center text-muted">
                <p>Khám phá thêm các sản phẩm cùng bộ sưu tập để đồng bộ không gian sống của bạn.</p>
                <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-outline-dark rounded-pill px-4 mt-2">Xem thêm sản phẩm</a>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .ls-wider { letter-spacing: 1.5px; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.02); }
    .grayscale { filter: grayscale(100%); }
    .grayscale:hover { filter: grayscale(0%); opacity: 1; }
    
    /* Custom Accordion Style */
    .accordion-button:not(.collapsed) {
        background-color: #f8fafc;
        color: var(--primary-color);
        box-shadow: none;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }
</style>

@include('user.layouts.footer')