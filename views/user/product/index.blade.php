@php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $searchKeyword = $_GET['search'] ?? '';
@endphp

@include('user.layouts.header')

<!-- 1. HERO BANNER -->
<section class="position-relative overflow-hidden mb-5">
    <div class="position-relative" style="height: 450px; background-color: #f1f5f9;">
        <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?q=80&w=2070&auto=format&fit=crop" 
             alt="Header Banner" 
             class="w-100 h-100 object-fit-cover">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(15,23,42,0.7) 0%, rgba(15,23,42,0.3) 60%, transparent 100%);"></div>
        <div class="position-absolute top-50 start-0 translate-middle-y container text-white" style="z-index: 2;">
            <div class="col-lg-6 ps-lg-4 animate-up">
                <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider">Mùa hè 2026</span>
                <h1 class="display-3 fw-bolder mb-3">Sống Tiện Nghi<br>Thỏa Đam Mê</h1>
                <p class="fs-5 mb-4 text-white-50">Công nghệ gia dụng thông minh cho ngôi nhà hiện đại.</p>
                <a href="#productList" class="btn btn-accent btn-lg rounded-pill px-5 shadow-lg fw-bold">Mua Sắm Ngay</a>
            </div>
        </div>
    </div>
</section>

<div class="container" id="productList">
    <!-- 2. SEARCH & TOOLBAR -->
    <div class="bg-white p-4 rounded-4 shadow-sm border mb-5 mt-n5 position-relative z-3">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <h3 class="fw-bold text-dark m-0">Sản phẩm <span class="text-primary">nổi bật</span></h3>
            </div>
            
            <div class="col-md-8">
                <!-- FORM TÌM KIẾM TỨC THÌ -->
                <form action="{{ rtrim(BASE_URL, '/') }}/product/index" method="GET" id="searchForm" class="d-flex gap-2 justify-content-md-end">
                    <div class="position-relative w-100" style="max-width: 450px;">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        
                        <!-- Input có sự kiện oninput để xử lý JS -->
                        <input type="text" 
                               name="search" 
                               id="instantSearch"
                               value="{{ $searchKeyword }}" 
                               class="form-control rounded-pill ps-5 py-2 border-secondary-subtle bg-light focus-ring" 
                               placeholder="Gõ để tìm kiếm thiết bị..."
                               autocomplete="off">

                        <!-- Nút Reset: Chỉ hiện khi có từ khóa -->
                        @if(!empty($searchKeyword))
                            <a href="{{ rtrim(BASE_URL, '/') }}/product/index" 
                               class="position-absolute top-50 end-0 translate-middle-y me-2 btn btn-sm btn-secondary rounded-circle"
                               style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;"
                               title="Xóa lọc">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                        
                        <!-- Loading Indicator (Ẩn mặc định) -->
                        <div id="searchLoading" class="position-absolute top-50 end-0 translate-middle-y me-3 spinner-border spinner-border-sm text-primary d-none" role="status"></div>
                    </div>
                    
                    @if(!empty($searchKeyword))
                        <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-outline-danger rounded-pill px-3 fw-bold d-none d-md-block text-nowrap">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Làm mới
                        </a>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Hiển thị từ khóa đang tìm -->
        @if(!empty($searchKeyword))
            <div class="mt-3 pt-3 border-top">
                <p class="mb-0 text-muted">
                    Kết quả tìm kiếm cho: <strong class="text-dark">"{{ $searchKeyword }}"</strong>
                    <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="text-decoration-none ms-2 small text-danger">(Xóa)</a>
                </p>
            </div>
        @endif
    </div>

    <!-- 3. PRODUCT GRID -->
    <div class="row g-4 mb-5">
        @if(isset($products) && !empty($products))
            @foreach($products as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-hover rounded-4 product-card overflow-hidden">
                        <!-- Ảnh & Actions -->
                        <div class="position-relative overflow-hidden p-4 text-center bg-white rounded-top-4" style="height: 260px;">
                            <a href="{{ rtrim(BASE_URL, '/') }}/product/show/{{ $item['id'] }}">
                                <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $item['image'] ?? 'default.jpg' }}" 
                                     class="img-fluid h-100 w-100 object-fit-contain transition-transform" 
                                     alt="{{ $item['name'] }}"
                                     onerror="this.src='https://placehold.co/400x400?text=TechMart'">
                            </a>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-3 rounded-2 shadow-sm">-20%</span>
                            
                            <!-- HOVER ACTIONS: CHỈ HIỆN KHI RÊ CHUỘT -->
                            <div class="product-action position-absolute bottom-0 start-50 translate-middle-x mb-3 d-flex gap-2 opacity-0">
                                <a href="{{ rtrim(BASE_URL, '/') }}/cart/add/{{ $item['id'] }}" 
                                   class="btn btn-primary rounded-circle shadow d-flex align-items-center justify-content-center"
                                   style="width: 45px; height: 45px;" 
                                   title="Thêm vào giỏ" data-bs-toggle="tooltip">
                                    <i class="bi bi-bag-plus-fill fs-5 text-white"></i>
                                </a>
                                <a href="{{ rtrim(BASE_URL, '/') }}/product/show/{{ $item['id'] }}" 
                                   class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center"
                                   style="width: 45px; height: 45px;" 
                                   title="Xem chi tiết" data-bs-toggle="tooltip">
                                    <i class="bi bi-eye-fill fs-5 text-dark"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Thông tin -->
                        <div class="card-body bg-light-subtle rounded-bottom-4 border-top border-light">
                            <div class="text-primary text-uppercase fw-bold" style="font-size: 0.7rem;">{{ $item['category_name'] ?? 'Gia dụng' }}</div>
                            <h6 class="card-title fw-bold text-dark text-truncate mt-1 mb-2">
                                <a href="{{ rtrim(BASE_URL, '/') }}/product/show/{{ $item['id'] }}" class="text-decoration-none text-dark">
                                    {{ $item['name'] }}
                                </a>
                            </h6>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fw-bolder fs-5 text-dark">{{ number_format($item['price'], 0, ',', '.') }}đ</span>
                                <span class="text-muted small text-decoration-line-through">{{ number_format($item['price']*1.2, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 py-5 text-center">
                <div class="bg-light rounded-4 p-5 border border-dashed">
                    <i class="bi bi-search fs-1 text-muted opacity-50 mb-3 d-block"></i>
                    <h5 class="text-muted fw-bold">Không tìm thấy sản phẩm nào!</h5>
                    <p class="text-secondary mb-4">Hãy thử tìm với từ khóa khác hoặc xóa bộ lọc.</p>
                    <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-primary rounded-pill px-4">Xem tất cả sản phẩm</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Phân trang -->
    @if(isset($totalPages) && $totalPages > 1)
        <nav class="mb-5 d-flex justify-content-center">
            <ul class="pagination gap-2">
                @for($i = 1; $i <= $totalPages; $i++)
                    <li class="page-item">
                        <a class="page-link rounded-3 border-0 fw-bold px-3 py-2 {{ ($currentPage == $i) ? 'bg-primary text-white shadow' : 'bg-white text-dark shadow-sm' }}" 
                           href="{{ rtrim(BASE_URL, '/') }}/product/index?page={{ $i }}&search={{ $searchKeyword }}">
                            {{ $i }}
                        </a>
                    </li>
                @endfor
            </ul>
        </nav>
    @endif
</div>

<style>
    .shadow-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .shadow-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    
    /* Hiệu ứng Zoom ảnh khi hover */
    .product-card:hover img { transform: scale(1.08); }
    
    /* Hiệu ứng hiện nút Action khi hover */
    .product-action { transition: all 0.3s ease; transform: translateX(-50%) translateY(10px); }
    .product-card:hover .product-action { opacity: 1 !important; transform: translateX(-50%) translateY(0); }
    
    .animate-up { animation: fadeInUp 0.8s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<!-- SCRIPT TÌM KIẾM TỨC THÌ (DEBOUNCE) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('instantSearch');
        const searchForm = document.getElementById('searchForm');
        const loadingIcon = document.getElementById('searchLoading');
        let timeout = null;

        if (searchInput) {
            // Tự động focus vào ô tìm kiếm nếu đang có từ khóa
            const val = searchInput.value;
            if(val) {
                searchInput.focus();
                searchInput.setSelectionRange(val.length, val.length);
            }

            searchInput.addEventListener('input', function() {
                // Xóa timeout cũ
                clearTimeout(timeout);
                
                // Hiển thị loading (nếu muốn cầu kỳ hơn)
                if(loadingIcon) loadingIcon.classList.remove('d-none');

                // Đặt timeout mới (600ms sau khi ngừng gõ sẽ submit)
                timeout = setTimeout(function() {
                    searchForm.submit();
                }, 600);
            });
            
            // Xử lý nút X trong input
            searchInput.addEventListener('search', function(event) {
                if (searchInput.value === "") {
                    window.location.href = "{{ rtrim(BASE_URL, '/') }}/product/index";
                }
            });
        }
        
        // Kích hoạt tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@include('user.layouts.footer')