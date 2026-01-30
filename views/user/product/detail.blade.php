@php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
    // Pass dữ liệu biến thể sang JS
    $jsVariants = json_encode($variants ?? []);
    
    // Giá hiển thị mặc định
    $displayPrice = number_format($product['price'], 0, ',', '.');
    
    // Xử lý danh sách ảnh để hiển thị ở slider
    // Bao gồm: Ảnh đại diện chính + Ảnh trong Album (Gallery)
    $galleryImages = [];
    if (!empty($product['image'])) {
        $galleryImages[] = $product['image']; // Ảnh chính đầu tiên
    }
    if (!empty($gallery)) {
        foreach ($gallery as $img) {
            $galleryImages[] = $img['image'];
        }
    }
    // Loại bỏ ảnh trùng lặp nếu có
    $galleryImages = array_unique($galleryImages);
@endphp

@include('user.layouts.header')

<style>
    /* UI TWEAKS */
    .fw-black { font-weight: 900; }
    .text-justify { text-align: justify; }
    .cursor-pointer { cursor: pointer; }
    
    /* Product Image Gallery */
    .main-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid #f1f1f1;
        background: #fff;
        aspect-ratio: 1/1; /* Vuông */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .thumbnail-container {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 5px;
        scrollbar-width: thin;
    }
    .thumbnail-item {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        border: 2px solid transparent;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .thumbnail-item.active, .thumbnail-item:hover {
        border-color: #0d6efd;
        opacity: 1;
    }
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Variant Options */
    .variant-radio { display: none; }
    .variant-label {
        cursor: pointer;
        border: 1px solid #e9ecef;
        background-color: #fff;
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
        transition: all 0.2s ease;
        min-width: 50px;
        text-align: center;
    }
    .variant-label:hover { border-color: #adb5bd; background-color: #f8f9fa; }
    
    .variant-radio:checked + .variant-label {
        border-color: #0d6efd;
        background-color: #e7f1ff;
        color: #0d6efd;
        box-shadow: 0 0 0 1px #0d6efd;
    }
    
    .variant-radio:disabled + .variant-label {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f8f9fa;
        text-decoration: line-through;
        border-color: #e9ecef;
    }

    /* Color Swatches */
    .color-swatch-label { 
        padding: 3px; 
        border-radius: 50%; 
        width: 45px; 
        height: 45px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border: 2px solid #e9ecef;
        cursor: pointer;
        transition: all 0.2s;
    }
    .color-inner { width: 100%; height: 100%; border-radius: 50%; display: block; border: 1px solid rgba(0,0,0,0.05); }
    .variant-radio:checked + .color-swatch-label { border-color: #0d6efd; transform: scale(1.1); box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    .variant-radio:hover + .color-swatch-label { border-color: #adb5bd; }

    /* Quantity Input */
    .qty-btn { width: 40px; height: 45px; border: 1px solid #ced4da; background: #fff; font-weight: bold; color: #495057; transition: 0.2s; }
    .qty-btn:hover { background: #f8f9fa; }
    .qty-input { height: 45px; border-left: 0; border-right: 0; border-top: 1px solid #ced4da; border-bottom: 1px solid #ced4da; font-weight: bold; color: #212529; }
    .qty-input:focus { box-shadow: none; border-color: #ced4da; }

    /* Animations */
    @keyframes shake {
        0% { transform: translateX(0); } 25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } 100% { transform: translateX(0); }
    }
    .shake { animation: shake 0.4s ease-in-out; }
    .transition-fade { transition: opacity 0.3s ease; }
</style>

<!-- BREADCRUMB -->
<div class="bg-light py-3 mb-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small fw-bold">
                <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/" class="text-decoration-none text-muted text-uppercase">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="text-decoration-none text-muted text-uppercase">Sản phẩm</a></li>
                <li class="breadcrumb-item active text-dark text-uppercase" aria-current="page">{{ $product['category_name'] ?? 'Chi tiết' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    
    <!-- FLASH MESSAGES -->
    @if(isset($_SESSION['success']))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['success'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @php unset($_SESSION['success']); @endphp
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $_SESSION['error'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @php unset($_SESSION['error']); @endphp
    @endif

    <div class="row g-5">
        <!-- LEFT: HÌNH ẢNH SẢN PHẨM -->
        <div class="col-lg-6">
            <div class="sticky-top" style="top: 20px; z-index: 1;">
                <!-- 1. Ảnh Chính -->
                <div class="main-image-container mb-3 shadow-sm group">
                    <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $product['image'] ?? 'default.jpg' }}" 
                         class="img-fluid w-100 h-100 object-fit-contain"
                         id="main-product-image"
                         alt="{{ $product['name'] }}"
                         onerror="this.src='https://placehold.co/800x800?text=No+Image'">
                         
                    <div class="position-absolute top-0 end-0 m-3">
                         <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">HOT</span>
                    </div>
                </div>

                <!-- 2. Danh sách Thumbnail (Album + Ảnh biến thể) -->
                @if(count($galleryImages) > 0)
                <div class="thumbnail-container" id="gallery-thumbs">
                    @foreach($galleryImages as $index => $img)
                        <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" onclick="changeMainImage('{{ $img }}', this)">
                            <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $img }}" 
                                 onerror="this.src='https://placehold.co/100?text=Err'">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- RIGHT: THÔNG TIN CHI TIẾT -->
        <div class="col-lg-6">
            <div class="ps-lg-3">
                <div class="mb-3">
                    <a href="#" class="text-decoration-none badge bg-light text-primary border mb-2">
                        {{ $product['brand_name'] ?? 'Thương hiệu' }}
                    </a>
                    <h1 class="fw-black text-dark mb-1 lh-sm">{{ $product['name'] }}</h1>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span>Mã SP: <strong>#{{ $product['id'] }}</strong></span>
                        <span>|</span>
                        <div class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <span>(56 đánh giá)</span>
                    </div>
                </div>
                
                <!-- Giá và Trạng thái -->
                <div class="d-flex align-items-end gap-3 mb-4 bg-light p-3 rounded-4">
                    <h2 class="text-danger fw-bold mb-0 display-6" id="display-price-text">{{ $displayPrice }}₫</h2>
                    <!-- Giá gốc giả định để đẹp giao diện -->
                    <span class="text-muted text-decoration-line-through fs-5 align-self-center">{{ number_format($product['price'] * 1.2, 0, ',', '.') }}₫</span>
                    
                    <div class="ms-auto" id="stock-badge">
                        @if(empty($variants))
                            <span class="badge bg-secondary rounded-pill px-3">Tạm hết hàng</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3">Chọn phân loại</span>
                        @endif
                    </div>
                </div>

                <!-- Mô tả ngắn -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark"><i class="bi bi-card-text me-1"></i> Mô tả ngắn:</h6>
                    <p class="text-muted small text-justify mb-0">
                        {{ !empty($product['description']) ? substr($product['description'], 0, 250) . '...' : 'Sản phẩm chính hãng, chất lượng cao, bảo hành dài hạn. Thiết kế hiện đại phù hợp với mọi nhu cầu sử dụng...' }}
                    </p>
                </div>

                <hr class="text-muted opacity-25 my-4">

                <!-- FORM MUA HÀNG -->
                <form action="{{ rtrim(BASE_URL, '/') }}/cart/add" method="POST" id="addToCartForm">
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <input type="hidden" name="variant_id" id="selected_variant_id" required>

                    <!-- 1. Chọn Màu Sắc -->
                    <div class="mb-4">
                        <label class="fw-bold small text-uppercase text-secondary mb-2 d-block">Màu sắc:</label>
                        <div class="d-flex flex-wrap gap-2" id="color-options">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                        </div>
                    </div>

                    <!-- 2. Chọn Kích Thước -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label class="fw-bold small text-uppercase text-secondary mb-2">Kích thước:</label>
                            <a href="#" class="small text-decoration-none text-primary"><i class="bi bi-ruler"></i> Bảng size</a>
                        </div>
                        <div class="d-flex flex-wrap gap-2" id="size-options">
                            <span class="text-muted fst-italic small">Vui lòng chọn màu trước...</span>
                        </div>
                    </div>

                    <!-- 3. Số Lượng & Nút Mua -->
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-sm-4">
                            <label class="fw-bold small text-uppercase text-secondary mb-2">Số lượng:</label>
                            <div class="d-flex">
                                <button class="btn qty-btn rounded-start" type="button" onclick="updateQty(-1)"><i class="bi bi-dash"></i></button>
                                <input type="number" name="quantity" id="quantity" class="form-control qty-input text-center" value="1" min="1">
                                <button class="btn qty-btn rounded-end" type="button" onclick="updateQty(1)"><i class="bi bi-plus"></i></button>
                            </div>
                            <div class="small text-success mt-1 fw-bold" id="stock-detail" style="font-size: 11px; min-height: 17px;"></div>
                        </div>
                        <div class="col-sm-8">
                            <button type="submit" id="btn-add-to-cart" class="btn btn-dark w-100 py-2 h-100 rounded-3 fw-bold shadow-sm" disabled style="min-height: 45px;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="text-uppercase"><i class="bi bi-bag-plus-fill me-2"></i> Thêm vào giỏ</span>
                                    <span class="small fw-normal opacity-75" style="font-size: 10px;">Giao hàng tận nơi miễn phí</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div id="validation-msg" class="alert alert-danger py-2 small text-center d-none rounded-3">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Vui lòng chọn đầy đủ <strong>Màu sắc</strong> và <strong>Kích thước</strong>!
                    </div>
                </form>

                <!-- Policy -->
                <div class="row g-2 mt-4 pt-2 border-top">
                    <div class="col-4">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-shield-check fs-4 text-success"></i>
                            <div style="line-height: 1.2;"><small class="fw-bold text-dark d-block">Bảo hành</small><small style="font-size: 10px;">Chính hãng 12T</small></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-arrow-repeat fs-4 text-primary"></i>
                            <div style="line-height: 1.2;"><small class="fw-bold text-dark d-block">Đổi trả</small><small style="font-size: 10px;">Trong 30 ngày</small></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-truck fs-4 text-warning"></i>
                            <div style="line-height: 1.2;"><small class="fw-bold text-dark d-block">Giao hàng</small><small style="font-size: 10px;">Toàn quốc 2-4 ngày</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS CHI TIẾT -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom px-4 pt-3">
                    <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="productTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold text-dark border-0 border-bottom border-3 border-primary py-3 px-4" data-bs-toggle="tab" data-bs-target="#desc-tab">Mô tả sản phẩm</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-muted border-0 py-3 px-4" data-bs-toggle="tab" data-bs-target="#spec-tab">Thông số kỹ thuật</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-muted border-0 py-3 px-4" data-bs-toggle="tab" data-bs-target="#review-tab">Đánh giá (56)</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <div class="tab-content">
                        <!-- Tab Mô tả -->
                        <div class="tab-pane fade show active" id="desc-tab">
                            <h5 class="fw-bold text-dark mb-3">Chi tiết sản phẩm</h5>
                            <div class="text-muted" style="line-height: 1.8;">
                                {!! nl2br($product['description'] ?? 'Đang cập nhật nội dung...') !!}
                                <p class="mt-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                <img src="https://placehold.co/1000x400?text=Product+Banner+Detail" class="img-fluid rounded-4 my-3 w-100">
                                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                            </div>
                        </div>
                        
                        <!-- Tab Thông số -->
                        <div class="tab-pane fade" id="spec-tab">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr><th width="30%">Thương hiệu</th><td>{{ $product['brand_name'] ?? 'N/A' }}</td></tr>
                                    <tr><th>Danh mục</th><td>{{ $product['category_name'] ?? 'N/A' }}</td></tr>
                                    <tr><th>Chất liệu</th><td>Cao cấp</td></tr>
                                    <tr><th>Xuất xứ</th><td>Việt Nam / Nhập khẩu</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tab Đánh giá -->
                        <div class="tab-pane fade" id="review-tab">
                            <div class="text-center py-5">
                                <i class="bi bi-chat-square-quote display-4 text-muted opacity-25"></i>
                                <p class="mt-3 text-muted">Chức năng đánh giá đang được phát triển.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Dữ liệu từ Server
    const variants = {!! $jsVariants !!};
    const productBasePrice = {{ $product['price'] }};
    const baseUrl = '{{ rtrim(BASE_URL, '/') }}/public/uploads/products/'; // Đường dẫn ảnh
    
    let selectedColor = null;
    let selectedSize = null;
    let currentStock = 0;

    // Helper format tiền tệ VNĐ
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    };

    // --- HÀM ĐỔI ẢNH CHÍNH ---
    function changeMainImage(imgName, thumbEl = null) {
        if (!imgName) return;
        const mainImg = document.getElementById('main-product-image');
        const fullPath = imgName.startsWith('http') ? imgName : baseUrl + imgName;
        
        // Hiệu ứng fade nhẹ
        mainImg.style.opacity = 0.5;
        setTimeout(() => {
            mainImg.src = fullPath;
            mainImg.style.opacity = 1;
        }, 150);

        // Active thumbnail nếu được click
        if (thumbEl) {
            document.querySelectorAll('.thumbnail-item').forEach(el => el.classList.remove('active'));
            thumbEl.classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderColors();
        
        // Tab logic (nếu Bootstrap JS chưa load tự động)
        const triggerTabList = document.querySelectorAll('#productTabs button')
        triggerTabList.forEach(triggerEl => {
            const tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', event => {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });

    // --- RENDER MÀU SẮC ---
    function renderColors() {
        const colorContainer = document.getElementById('color-options');
        const uniqueColors = {};
        let hasStock = false;

        variants.forEach(v => {
            if (!uniqueColors[v.color_id]) {
                uniqueColors[v.color_id] = { id: v.color_id, name: v.color_name, hex: v.color_hex };
            }
            if(parseInt(v.quantity) > 0) hasStock = true;
        });

        if (!hasStock && variants.length > 0) {
             colorContainer.innerHTML = '<span class="text-danger fw-bold">Sản phẩm tạm hết hàng toàn bộ</span>';
             return;
        }

        let html = '';
        for (const key in uniqueColors) {
            const c = uniqueColors[key];
            const isHex = c.hex && c.hex !== '';
            
            if (isHex) {
                html += `
                <div class="d-inline-block text-center" data-bs-toggle="tooltip" title="${c.name}">
                    <input type="radio" name="color_opt" id="color_${c.id}" value="${c.id}" class="variant-radio" onchange="selectColor('${c.id}')">
                    <label for="color_${c.id}" class="color-swatch-label">
                        <span class="color-inner" style="background-color: ${c.hex};"></span>
                    </label>
                </div>`;
            } else {
                html += `
                <input type="radio" name="color_opt" id="color_${c.id}" value="${c.id}" class="variant-radio" onchange="selectColor('${c.id}')">
                <label for="color_${c.id}" class="variant-label">${c.name}</label>`;
            }
        }
        
        if(html === '') html = '<span class="text-muted small">Mặc định</span>';
        colorContainer.innerHTML = html;
        
        // Kích hoạt tooltip bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) })
    }

    // --- CHỌN MÀU ---
    function selectColor(colorId) {
        selectedColor = colorId;
        selectedSize = null;
        
        // Reset UI
        document.getElementById('selected_variant_id').value = '';
        document.getElementById('btn-add-to-cart').disabled = true;
        document.getElementById('validation-msg').classList.add('d-none');
        document.getElementById('stock-badge').innerHTML = '<span class="badge bg-warning text-dark">Vui lòng chọn size</span>';
        document.getElementById('stock-detail').innerText = '';
        
        const sizeContainer = document.getElementById('size-options');
        const availableSizes = variants.filter(v => v.color_id == colorId);
        
        if (availableSizes.length === 0) {
            sizeContainer.innerHTML = '<span class="text-danger small">Hết hàng màu này</span>';
            return;
        }

        let html = '';
        availableSizes.forEach(v => {
            const stock = parseInt(v.quantity);
            const disabled = stock <= 0 ? 'disabled' : '';
            const stockLabel = stock <= 0 ? ' (Hết)' : '';
            
            html += `
                <input type="radio" name="size_opt" id="size_${v.size_id}" value="${v.size_id}" class="variant-radio" ${disabled} onchange="selectSize('${v.size_id}')">
                <label for="size_${v.size_id}" class="variant-label">${v.size_name}${stockLabel}</label>
            `;
        });
        sizeContainer.innerHTML = html;
    }

    // --- CHỌN SIZE -> UPDATE INFO & ẢNH ---
    function selectSize(sizeId) {
        selectedSize = sizeId;
        document.getElementById('validation-msg').classList.add('d-none');
        updateVariantInfo();
    }

    function updateVariantInfo() {
        if (!selectedColor || !selectedSize) return;

        const variant = variants.find(v => v.color_id == selectedColor && v.size_id == selectedSize);
        
        if (variant) {
            currentStock = parseInt(variant.quantity);
            const hiddenId = document.getElementById('selected_variant_id');
            const btn = document.getElementById('btn-add-to-cart');
            const badge = document.getElementById('stock-badge');
            const stockDetail = document.getElementById('stock-detail');
            const priceText = document.getElementById('display-price-text');
            const qtyInput = document.getElementById('quantity');

            // 1. Cập nhật ID biến thể
            hiddenId.value = variant.id;

            // 2. Cập nhật GIÁ
            let finalPrice = parseFloat(variant.price);
            if(finalPrice <= 0) finalPrice = productBasePrice;
            
            priceText.style.opacity = 0;
            setTimeout(() => {
                priceText.innerText = formatCurrency(finalPrice);
                priceText.style.opacity = 1;
            }, 150);

            // 3. TỰ ĐỘNG ĐỔI ẢNH THEO BIẾN THỂ (NẾU CÓ)
            if (variant.image && variant.image.trim() !== '') {
                changeMainImage(variant.image);
            }

            // 4. Cập nhật TỒN KHO & NÚT MUA
            if (currentStock > 0) {
                badge.innerHTML = '<span class="badge bg-success rounded-pill px-3">Còn hàng</span>';
                stockDetail.innerText = `Có sẵn ${currentStock} sản phẩm`;
                btn.disabled = false;
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-dark');
                qtyInput.value = 1;
            } else {
                badge.innerHTML = '<span class="badge bg-danger rounded-pill px-3">Hết hàng</span>';
                stockDetail.innerText = '';
                btn.disabled = true;
                btn.classList.remove('btn-dark');
                btn.classList.add('btn-secondary');
            }
        }
    }

    // --- TĂNG GIẢM SỐ LƯỢNG ---
    function updateQty(change) {
        if (!selectedColor || !selectedSize) {
            const form = document.getElementById('addToCartForm');
            form.classList.add('shake');
            setTimeout(() => form.classList.remove('shake'), 500);
            document.getElementById('validation-msg').classList.remove('d-none');
            return;
        }
        
        const input = document.getElementById('quantity');
        let newVal = parseInt(input.value) + change;
        
        if (newVal < 1) newVal = 1;
        if (newVal > currentStock) {
            newVal = currentStock;
            alert(`Kho chỉ còn ${currentStock} sản phẩm!`);
        }
        
        input.value = newVal;
    }
</script>