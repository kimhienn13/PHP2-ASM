@php
    /**
     * KHỞI TẠO SESSION
     */
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
@endphp

@include('user.layouts.header')

<!-- Custom CSS cho trang Gia dụng -->
<style>
    :root {
        --house-primary: #20bf6b; /* Xanh lá tươi */
        --house-secondary: #f7b731; /* Vàng cam ấm áp */
        --house-bg: #f1f2f6;
    }
    
    .bg-house-gradient {
        background: linear-gradient(135deg, #20bf6b 0%, #0fb9b1 100%);
    }

    .text-house-primary { color: var(--house-primary) !important; }
    
    .voucher-card {
        border: none;
        background: #fff;
        transition: all 0.3s ease;
    }
    
    .voucher-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(32, 191, 107, 0.15) !important;
    }

    /* Đường cắt voucher dạng răng cưa tròn */
    .voucher-split {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 130px;
        width: 4px;
        border-left: 2px dashed #e2e8f0;
        z-index: 10;
    }

    .cutout-top, .cutout-bottom {
        position: absolute;
        left: 130px;
        width: 24px;
        height: 24px;
        background-color: #f8f9fa; /* Trùng màu nền body */
        border-radius: 50%;
        transform: translateX(-50%);
        z-index: 20;
    }
    .cutout-top { top: -12px; }
    .cutout-bottom { bottom: -12px; }

    .btn-copy-code {
        background-color: var(--house-secondary);
        color: #fff;
        border: none;
    }
    .btn-copy-code:hover {
        background-color: #fa8231;
        color: #fff;
    }
    
    .search-input:focus {
        border-color: var(--house-primary);
        box-shadow: 0 0 0 0.25rem rgba(32, 191, 107, 0.25);
    }
</style>

<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container">
        
        <!-- HEADER TRANG -->
        <div class="text-center mb-5 animate-fade-in">
            <div class="d-inline-block p-3 rounded-circle bg-white shadow-sm mb-3">
                <i class="bi bi-house-heart-fill fs-1 text-house-primary" style="color: #20bf6b;"></i>
            </div>
            <h1 class="fw-bold display-6 mb-2" style="color: #2d3436;">
                Săn Deal <span style="color: #20bf6b;">Nhà Xinh</span>
            </h1>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Chăm chút tổ ấm với hàng ngàn ưu đãi cho đồ gia dụng, thiết bị bếp và tiện ích gia đình.
            </p>
        </div>

        <!-- THANH TÌM KIẾM -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 col-lg-6">
                <div class="position-relative">
                    <input type="text" 
                           id="ajax-search-input" 
                           value="{{ $search ?? '' }}" 
                           class="form-control form-control-lg rounded-pill border-0 shadow-sm ps-5 py-3 search-input"
                           placeholder="Bạn đang tìm ưu đãi cho nồi, chảo, máy xay...?">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-4 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <div id="search-loader" class="position-absolute top-50 end-0 translate-middle-y me-4 d-none spinner-border spinner-border-sm text-success"></div>
                </div>
            </div>
        </div>

        <!-- DANH SÁCH VOUCHER -->
        <div id="coupon-list-container" class="row g-4">
            @if(empty($coupons))
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="Empty" width="100" class="mb-3 opacity-50">
                    <h5 class="text-muted">Chưa tìm thấy mã giảm giá nào phù hợp cho gian bếp của bạn!</h5>
                </div>
            @else
                @foreach($coupons as $coupon)
                    <div class="col-md-6 col-lg-6 col-xl-4">
                        <div class="voucher-card rounded-4 shadow-sm position-relative h-100 overflow-hidden">
                            <!-- Hình trang trí cutout -->
                            <div class="cutout-top"></div>
                            <div class="cutout-bottom"></div>
                            <div class="voucher-split"></div>

                            <div class="d-flex h-100">
                                <!-- Phần TRÁI: Giá trị (Màu xanh/cam) -->
                                <div class="d-flex flex-column justify-content-center align-items-center text-white p-3 text-center" 
                                     style="width: 130px; background: {{ $coupon['type'] == 'percent' ? 'linear-gradient(135deg, #f7b731, #fa8231)' : 'linear-gradient(135deg, #20bf6b, #0fb9b1)' }};">
                                    <div class="fw-bold small opacity-75 mb-1">GIẢM</div>
                                    <div class="lh-1">
                                        <span class="display-6 fw-bold">
                                            {{ $coupon['type'] == 'percent' ? $coupon['value'] : number_format($coupon['value']/1000) }}
                                        </span>
                                        <span class="fs-5 fw-bold">{{ $coupon['type'] == 'percent' ? '%' : 'K' }}</span>
                                    </div>
                                    <div class="mt-2 badge bg-white bg-opacity-25 rounded-pill fw-normal">
                                        {{ $coupon['type'] == 'percent' ? 'Toàn sàn' : 'Tiền mặt' }}
                                    </div>
                                </div>

                                <!-- Phần PHẢI: Thông tin -->
                                <div class="p-3 flex-grow-1 d-flex flex-column ps-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="fw-bold text-dark mb-1 font-monospace">{{ $coupon['code'] }}</h5>
                                        <i class="bi bi-bag-heart text-muted"></i>
                                    </div>
                                    <p class="text-muted small mb-0 flex-grow-1" style="font-size: 0.9rem;">
                                        Áp dụng cho các sản phẩm gia dụng, nhà bếp.
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-end mt-3 pt-2 border-top border-light">
                                        <small class="text-muted fst-italic" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock"></i> HSD: Vô hạn
                                        </small>
                                        <button class="btn btn-sm btn-copy-code rounded-pill px-3 fw-bold shadow-sm" 
                                                data-code="{{ $coupon['code'] }}">
                                            Lưu Mã
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- PHÂN TRANG -->
        <div id="pagination-container" class="mt-5 d-flex justify-content-center gap-2">
            @if(isset($totalPages) && $totalPages > 1)
                @for($i = 1; $i <= $totalPages; $i++)
                    <button class="btn {{ $currentPage == $i ? 'btn-success text-white' : 'btn-white bg-white text-dark shadow-sm' }} rounded-circle px-0 fw-bold page-btn-ajax" 
                            style="width: 40px; height: 40px;"
                            data-page="{{ $i }}">
                        {{ $i }}
                    </button>
                @endfor
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ajax-search-input');
    const listContainer = document.getElementById('coupon-list-container');
    const paginationContainer = document.getElementById('pagination-container');
    const loader = document.getElementById('search-loader');

    // Hàm gọi API
    function fetchCoupons(query = '', page = 1) {
        loader.classList.remove('d-none');
        const currentPath = window.location.pathname;
        const url = currentPath + "?ajax=1&search=" + encodeURIComponent(query) + "&page=" + page;

        fetch(url)
            .then(res => res.json())
            .then(res => {
                renderList(res.data);
                renderPagination(res.totalPages, page);
                loader.classList.add('d-none');
            })
            .catch(err => {
                console.error('Lỗi tải dữ liệu:', err);
                loader.classList.add('d-none');
            });
    }

    // Hàm render danh sách (Phải đồng bộ HTML với phần Blade ở trên)
    function renderList(coupons) {
        if (!coupons || coupons.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-5 w-100">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="Empty" width="100" class="mb-3 opacity-50">
                    <h5 class="text-muted">Không tìm thấy mã giảm giá nào phù hợp!</h5>
                </div>`;
            return;
        }

        listContainer.innerHTML = coupons.map(c => {
            const val = c.type === 'percent' ? c.value : (c.value / 1000);
            const unit = c.type === 'percent' ? '%' : 'K';
            const typeLabel = c.type === 'percent' ? 'Toàn sàn' : 'Tiền mặt';
            // Logic màu nền: Phần trăm dùng màu Cam, Tiền dùng màu Xanh
            const bgGradient = c.type === 'percent' 
                ? 'linear-gradient(135deg, #f7b731, #fa8231)' 
                : 'linear-gradient(135deg, #20bf6b, #0fb9b1)';

            return `
                <div class="col-md-6 col-lg-6 col-xl-4 animate-fade-in">
                    <div class="voucher-card rounded-4 shadow-sm position-relative h-100 overflow-hidden">
                        <div class="cutout-top"></div>
                        <div class="cutout-bottom"></div>
                        <div class="voucher-split"></div>

                        <div class="d-flex h-100">
                            <!-- Phần TRÁI -->
                            <div class="d-flex flex-column justify-content-center align-items-center text-white p-3 text-center" 
                                 style="width: 130px; background: ${bgGradient};">
                                <div class="fw-bold small opacity-75 mb-1">GIẢM</div>
                                <div class="lh-1">
                                    <span class="display-6 fw-bold">${val}</span>
                                    <span class="fs-5 fw-bold">${unit}</span>
                                </div>
                                <div class="mt-2 badge bg-white bg-opacity-25 rounded-pill fw-normal">
                                    ${typeLabel}
                                </div>
                            </div>

                            <!-- Phần PHẢI -->
                            <div class="p-3 flex-grow-1 d-flex flex-column ps-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="fw-bold text-dark mb-1 font-monospace">${c.code}</h5>
                                    <i class="bi bi-bag-heart text-muted"></i>
                                </div>
                                <p class="text-muted small mb-0 flex-grow-1" style="font-size: 0.9rem;">
                                    Áp dụng cho các sản phẩm gia dụng, nhà bếp.
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-end mt-3 pt-2 border-top border-light">
                                    <small class="text-muted fst-italic" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock"></i> HSD: Vô hạn
                                    </small>
                                    <button class="btn btn-sm btn-copy-code rounded-pill px-3 fw-bold shadow-sm" 
                                            data-code="${c.code}">
                                        Lưu Mã
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    // Hàm render phân trang
    function renderPagination(total, current) {
        if (total <= 1) { paginationContainer.innerHTML = ''; return; }
        let html = '';
        for (let i = 1; i <= total; i++) {
            const cls = (i == current) ? 'btn-success text-white' : 'btn-white bg-white text-dark shadow-sm';
            html += `<button class="btn ${cls} rounded-circle px-0 fw-bold page-btn-ajax" 
                             style="width: 40px; height: 40px;" data-page="${i}">${i}</button> `;
        }
        paginationContainer.innerHTML = html;
    }

    // Xử lý tìm kiếm (Debounce)
    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchCoupons(this.value, 1), 400);
    });

    // Xử lý click (Pagination & Copy)
    document.addEventListener('click', function(e) {
        // Click phân trang
        if (e.target.classList.contains('page-btn-ajax')) {
            fetchCoupons(searchInput.value, e.target.dataset.page);
            // Scroll nhẹ lên đầu danh sách thay vì đầu trang
            document.getElementById('coupon-list-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Click copy mã
        if (e.target.classList.contains('btn-copy-code')) {
            const btn = e.target;
            navigator.clipboard.writeText(btn.dataset.code).then(() => {
                const oldText = btn.innerText;
                btn.innerText = 'Đã Lưu';
                btn.classList.replace('btn-copy-code', 'btn-secondary'); // Đổi màu xám tạm thời
                
                setTimeout(() => {
                    btn.innerText = oldText;
                    btn.classList.replace('btn-secondary', 'btn-copy-code');
                }, 2000);
            });
        }
    });
});
</script>

<style>
/* Animation nhẹ nhàng */
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; transform: translateY(20px); }
@keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
</style>

@include('user.layouts.footer')