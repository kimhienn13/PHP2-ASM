@include('admin.layouts.header')

<!-- Custom CSS cho trang này -->
<style>
    :root { --primary-color: #4f46e5; --secondary-color: #64748b; }
    .bg-glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    .table-hover tbody tr:hover { background-color: #f8fafc; transform: translateY(-1px); transition: all 0.2s; }
    .product-img-box { width: 60px; height: 60px; border-radius: 12px; overflow: hidden; position: relative; }
    .product-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .badge-soft-primary { background-color: #e0e7ff; color: #4f46e5; }
    .badge-soft-success { background-color: #dcfce7; color: #166534; }
    .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; }
    .action-btn:hover { background-color: #f1f5f9; }
    .search-input { padding-left: 40px; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
</style>

<div class="container-fluid px-4 py-4 bg-light" style="min-height: 100vh;">
    
    {{-- Toast Thông báo (Góc trên phải) --}}
    @if(isset($_SESSION['success']))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
            <div class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['success'] }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        @php unset($_SESSION['success']) @endphp
    @endif

    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Quản lý Sản phẩm</h4>
            <p class="text-muted small mb-0">Tổng quan danh sách và kho hàng</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-white border shadow-sm fw-bold text-secondary">
                <i class="bi bi-download me-1"></i> Xuất Excel
            </button>
            <button class="btn btn-primary shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-lg me-1"></i> Thêm mới
            </button>
        </div>
    </div>

    <!-- Thanh Công Cụ & Tìm Kiếm -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" method="GET" class="row g-3 align-items-center">
                <!-- Tìm kiếm -->
                <div class="col-md-4 position-relative">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search" class="form-control border-0 bg-light rounded-pill search-input py-2" 
                           placeholder="Tìm kiếm sản phẩm, mã ID..." value="{{ $search ?? '' }}">
                </div>

                <!-- Bộ lọc -->
                <div class="col-md-3">
                    @php 
                        // Lấy giá trị sort từ URL để giữ trạng thái select
                        $currentSort = $_GET['sort'] ?? ''; 
                    @endphp
                    <select class="form-select border-0 bg-light rounded-pill py-2 text-muted" name="sort" onchange="this.form.submit()">
                        <option value="" {{ $currentSort == '' ? 'selected' : '' }}>Sắp xếp: Mặc định</option>
                        <option value="price_asc" {{ $currentSort == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="price_desc" {{ $currentSort == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="name_asc" {{ $currentSort == 'name_asc' ? 'selected' : '' }}>Tên: A-Z</option>
                    </select>
                </div>

                <!-- Nút Submit -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold">Lọc dữ liệu</button>
                </div>

                <!-- Nút Xóa Lọc -->
                @if(!empty($search) || !empty($currentSort))
                <div class="col-md-auto ms-auto">
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" class="text-danger text-decoration-none small fw-bold">
                        <i class="bi bi-x-circle me-1"></i> Xóa lọc
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Bảng Dữ Liệu -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light text-secondary small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4" width="20"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                        <th width="80">Hình ảnh</th>
                        <th>Thông tin sản phẩm</th>
                        <th>Danh mục & Hãng</th>
                        <th>Giá bán</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse ($products as $p)
                    <tr>
                        <td class="ps-4"><input type="checkbox" class="form-check-input item-check"></td>
                        <td>
                            @php 
                                $urlFix = rtrim(BASE_URL, '/');
                                $urlFix = str_replace('/index.php', '', $urlFix);
                                $imagePath = $urlFix . '/public/uploads/products/' . ($p['image'] ?: 'default.jpg');
                            @endphp
                            <div class="product-img-box shadow-sm border">
                                <img src="{{ $imagePath }}" onerror="this.src='https://placehold.co/100x100?text=SP'" alt="{{ $p['name'] }}">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $p['name'] }}</div>
                            <small class="text-muted">Mã ID: <span class="font-monospace">#{{ $p['id'] }}</span></small>
                        </td>
                        <td>
                            <span class="badge badge-soft-primary px-3 py-2 rounded-pill mb-1">{{ $p['category_name'] ?? 'Chưa phân loại' }}</span>
                            <div class="small text-muted ps-1"><i class="bi bi-tag-fill me-1"></i>{{ $p['brand_name'] ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">{{ number_format($p['price']) }} ₫</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="action-btn text-primary bg-white shadow-sm border-0 btn-edit-product" 
                                    data-bs-toggle="tooltip" title="Chỉnh sửa"
                                    data-id="{{ $p['id'] }}"
                                    data-name="{{ htmlspecialchars($p['name']) }}"
                                    data-price="{{ $p['price'] }}"
                                    data-category="{{ $p['category_id'] }}"
                                    data-brand="{{ $p['brand_id'] }}"
                                    data-image="{{ $p['image'] }}"
                                    data-bs-target="#editProductModal"
                                    data-bs-toggle="modal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/destroy/{{ $p['id'] }}" 
                                   class="action-btn text-danger bg-white shadow-sm border-0"
                                   data-bs-toggle="tooltip" title="Xóa"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm: {{ $p['name'] }}?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                Không tìm thấy dữ liệu nào phù hợp.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer Bảng / Phân trang -->
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">Hiển thị {{ count($products) }} kết quả</small>
            
            @if (isset($totalPages) && $totalPages > 1)
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    @php 
                        // Lấy biến sort hiện tại để append vào link pagination
                        $sortParam = isset($currentSort) && $currentSort ? "&sort=$currentSort" : "";
                        $searchParam = isset($search) ? "&search=" . urlencode($search) : "";
                    @endphp
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                            <a class="page-link rounded-2 border-0 px-3 py-2 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white shadow' : 'bg-light text-dark' }}" 
                               href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index?page={{ $i }}{{ $searchParam }}{{ $sortParam }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- Include Modals --}}
@include('admin.Product.them')
@include('admin.Product.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tooltip initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Xử lý dữ liệu Edit Modal
        const editButtons = document.querySelectorAll('.btn-edit-product');
        const editForm = document.getElementById('editProductForm');
        const imgPreview = document.getElementById('edit_img_preview');
        const jsBaseUrl = '{{ rtrim(BASE_URL, "/") }}'.replace('/index.php', '');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Manual trigger modal để tránh conflict data
                const myModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                myModal.show();

                const id = this.dataset.id;
                if (editForm) editForm.action = '{{ rtrim(BASE_URL, "/") }}/adminproduct/update/' + id;

                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_price').value = this.dataset.price;
                document.getElementById('edit_category_id').value = this.dataset.category;
                document.getElementById('edit_brand_id').value = this.dataset.brand;

                const img = this.dataset.image;
                if (imgPreview) {
                    imgPreview.src = (img && img !== '') 
                        ? jsBaseUrl + '/public/uploads/products/' + img 
                        : 'https://placehold.co/120x120?text=No+Image';
                }
            });
        });

        // Auto show modal if error
        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addProductModal' : '#editProductModal' }}";
            const targetModal = document.querySelector(modalId);
            if(targetModal) {
                const bootstrapModal = new bootstrap.Modal(targetModal);
                bootstrapModal.show();
            }
        @endif

        // Checkbox All logic
        const checkAll = document.getElementById('checkAll');
        const itemChecks = document.querySelectorAll('.item-check');
        if(checkAll){
            checkAll.addEventListener('change', function(){
                itemChecks.forEach(c => c.checked = this.checked);
            });
        }
    });
</script>

@include('admin.layouts.footer')