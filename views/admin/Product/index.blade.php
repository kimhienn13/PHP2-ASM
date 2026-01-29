@include('admin.layouts.header')

<style>
    :root { --primary-color: #4f46e5; --secondary-color: #64748b; }
    .product-img-box { width: 50px; height: 50px; border-radius: 8px; overflow: hidden; }
    .product-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .variant-badge { font-size: 0.75rem; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 12px; display: inline-block; margin-right: 4px; margin-bottom: 4px;}
    
    /* Style cho Tabs trong Modal Edit */
    .nav-tabs .nav-link { background: none; }
    .nav-tabs .nav-link.active { background: none; }
</style>

<div class="container-fluid py-4" style="background-color: #f1f5f9; min-height: 100vh;">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Danh Sách Sản Phẩm</h4>
            <p class="text-secondary small mb-0">Quản lý kho hàng và thông tin sản phẩm</p>
        </div>
        <!-- Nút Thêm Sản Phẩm -->
        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg fs-5"></i>
            <span>Thêm Mới & Nhập Kho</span>
        </button>
    </div>

    <!-- Alerts -->
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 bg-success-subtle text-success rounded-3 mb-4 d-flex align-items-center shadow-sm">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <span class="fw-medium">{{ $_SESSION['success'] }}</span>
        </div>
        @php unset($_SESSION['success']); @endphp
    @endif

    <!-- Main Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 p-4 pb-0">
            <div class="row g-3">
                <div class="col-md-4 position-relative">
                    <i class="bi bi-search position-absolute text-muted" style="left: 25px; top: 10px;"></i>
                    <input type="text" class="form-control rounded-pill bg-light border-0 ps-5" placeholder="Tìm kiếm...">
                </div>
            </div>
        </div>

        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small fw-bold">Sản Phẩm</th>
                            <th class="py-3 text-secondary small fw-bold">Danh Mục</th>
                            <th class="py-3 text-secondary small fw-bold">Giá</th>
                            <th class="py-3 text-secondary small fw-bold" width="200">Tồn kho</th>
                            <th class="py-3 text-secondary small fw-bold text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @if(empty($products))
                            <tr><td colspan="5" class="text-center py-5 text-muted">Không có sản phẩm nào.</td></tr>
                        @else
                            @foreach ($products as $product)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-img-box border bg-white">
                                                <img src="{{ !empty($product['image']) ? BASE_URL . '/public/uploads/products/' . $product['image'] : 'https://placehold.co/100?text=IMG' }}" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 200px;">{{ $product['name'] }}</h6>
                                                <small class="text-muted">#{{ $product['id'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-secondary border fw-normal">{{ $product['category_name'] ?? '-' }}</span></td>
                                    <td class="fw-bold text-dark">{{ number_format($product['price']) }} đ</td>
                                    <td>
                                        <span class="badge {{ $product['total_stock'] > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill mb-1">
                                            Tổng: {{ $product['total_stock'] }}
                                        </span>
                                        @if(!empty($product['color_list']))
                                            <small class="text-muted d-block text-truncate" style="max-width: 150px;">
                                                {{ $product['color_list'] }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Nút Edit: Mở Modal Edit + Tab Kho -->
                                        <button class="action-btn text-primary border-0 bg-transparent btn-edit" 
                                            data-bs-toggle="modal" data-bs-target="#editProductModal"
                                            data-id="{{ $product['id'] }}"
                                            data-name="{{ $product['name'] }}"
                                            data-price="{{ $product['price'] }}"
                                            data-category="{{ $product['category_id'] }}"
                                            data-brand="{{ $product['brand_id'] }}"
                                            data-image="{{ $product['image'] }}"
                                            title="Sửa & Quản lý Kho">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <a href="{{ BASE_URL }}/adminproduct/delete?id={{ $product['id'] }}" 
                                           class="action-btn text-danger border-0 bg-transparent"
                                           onclick="return confirm('CẢNH BÁO: Xóa sản phẩm sẽ xóa toàn bộ biến thể trong kho!\nBạn có chắc chắn?');">
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
        <div class="card-footer bg-white border-top-0 py-3"></div>
    </div>
</div>

<!-- Include Các Modal đã chỉnh sửa -->
@include('admin.Product.them')
@include('admin.Product.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jsBaseUrl = "{{ BASE_URL }}";

        // Xử lý nút Edit: Đổ dữ liệu vào Modal Edit và Load Iframe Kho
        const editBtns = document.querySelectorAll('.btn-edit');
        const iframe = document.getElementById('edit-stock-iframe');
        const loader = document.getElementById('edit-stock-loader');
        const editModal = document.getElementById('editProductModal');

        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // 1. Điền thông tin vào Tab "Thông tin chung"
                const form = document.getElementById('editProductForm');
                form.action = jsBaseUrl + '/adminproduct/update?id=' + this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_price').value = this.dataset.price;
                
                const catSelect = document.getElementById('edit_category_id');
                if(catSelect) catSelect.value = this.dataset.category;
                const brandSelect = document.getElementById('edit_brand_id');
                if(brandSelect) brandSelect.value = this.dataset.brand;

                document.getElementById('edit_current_image').value = this.dataset.image;
                const imgPreview = document.getElementById('edit_img_preview');
                const img = this.dataset.image;
                if (imgPreview) {
                    imgPreview.src = (img && img !== '') ? jsBaseUrl + '/public/uploads/products/' + img : 'https://placehold.co/120x120?text=No+Img';
                }

                // 2. Load iframe cho Tab "Quản lý Kho"
                // Reset iframe trước
                iframe.src = "";
                iframe.classList.add('d-none');
                loader.classList.remove('d-none');
                
                // Set src mới
                iframe.src = `${jsBaseUrl}/adminproduct/variants/${this.dataset.id}?iframe=true`;
                
                iframe.onload = function() {
                    loader.classList.add('d-none');
                    iframe.classList.remove('d-none');
                }

                // Reset về tab đầu tiên
                const infoTab = new bootstrap.Tab(document.querySelector('#info-tab'));
                infoTab.show();
            });
        });

        // Khi đóng Edit Modal -> Reload trang để cập nhật số tồn kho
        editModal.addEventListener('hidden.bs.modal', function () {
            window.location.reload();
        });
    });
</script>

@include('admin.layouts.footer')