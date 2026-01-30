@include('admin.layouts.header')

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Quản lý Sản phẩm</h4>
            <p class="text-secondary small mb-0">Tổng số: {{ $totalPages * 8 }} sản phẩm (ước tính)</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg me-1"></i> Thêm mới
        </button>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group bg-light rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none" placeholder="Tìm tên sản phẩm..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small fw-bold">Sản phẩm</th>
                        <th class="py-3 text-secondary small fw-bold">Giá bán</th>
                        <th class="py-3 text-secondary small fw-bold">Kho</th>
                        <th class="py-3 text-secondary small fw-bold">Danh mục / Thương hiệu</th>
                        <th class="pe-4 py-3 text-secondary small fw-bold text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($products))
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Không tìm thấy sản phẩm nào.</td>
                        </tr>
                    @else
                        @foreach($products as $p)
                        <tr>
                            <!-- Product Info -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white border rounded-3 p-1 flex-shrink-0" style="width: 50px; height: 50px;">
                                        <img src="{{ BASE_URL }}/public/uploads/products/{{ $p['image'] }}" 
                                             onerror="this.src='https://placehold.co/50'" 
                                             class="w-100 h-100 object-fit-cover rounded-2">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 250px;">{{ $p['name'] }}</h6>
                                        <small class="text-muted d-block" style="font-size: 11px;">ID: #{{ $p['id'] }}</small>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Price -->
                            <td class="fw-bold text-primary">
                                {{ number_format($p['price'], 0, ',', '.') }} đ
                            </td>

                            <!-- Stock Summary -->
                            <td>
                                @if($p['total_stock'] > 0)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2">
                                        Còn hàng: {{ $p['total_stock'] }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Hết hàng</span>
                                @endif
                                <div class="small text-muted mt-1" style="font-size: 11px;">
                                    {{ $p['color_list'] ?: 'Chưa có biến thể' }}
                                </div>
                            </td>

                            <!-- Category/Brand -->
                            <td>
                                <span class="badge bg-light text-secondary border fw-normal">{{ $p['category_name'] ?? 'N/A' }}</span>
                                <span class="badge bg-light text-secondary border fw-normal ms-1">{{ $p['brand_name'] ?? 'N/A' }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="pe-4 text-end">
                                <button type="button" 
                                        class="btn btn-sm btn-light text-primary border rounded-circle p-2 me-1" 
                                        title="Sửa"
                                        onclick="editProduct({
                                            id: {{ $p['id'] }},
                                            name: '{{ addslashes($p['name']) }}',
                                            price: {{ $p['price'] }},
                                            category_id: '{{ $p['category_id'] }}',
                                            brand_id: '{{ $p['brand_id'] }}',
                                            image: '{{ $p['image'] }}'
                                        })">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <a href="{{ BASE_URL }}/adminproduct/destroy?id={{ $p['id'] }}" 
                                   class="btn btn-sm btn-light text-danger border rounded-circle p-2" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" 
                                   title="Xóa">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($totalPages > 1)
        <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-center">
            <nav>
                <ul class="pagination pagination-sm mb-0 shadow-sm rounded-pill overflow-hidden">
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                            <a class="page-link border-0 fw-bold {{ $currentPage == $i ? 'bg-primary' : 'text-secondary' }}" 
                               href="?page={{ $i }}&search={{ $search }}">{{ $i }}</a>
                        </li>
                    @endfor
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- INCLUDE CÁC MODAL -->
@include('admin.Product.them')
@include('admin.Product.edit')

<script>
    // Hàm xử lý khi bấm nút Sửa
    function editProduct(data) {
        // 1. Điền thông tin vào Tab "Thông tin chung"
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_category_id').value = data.category_id;
        document.getElementById('edit_brand_id').value = data.brand_id;
        document.getElementById('edit_current_image').value = data.image;
        
        // Hiển thị ảnh preview
        const imgPreview = document.getElementById('edit_img_preview');
        if(data.image) {
            imgPreview.src = '{{ BASE_URL }}/public/uploads/products/' + data.image;
        } else {
            imgPreview.src = 'https://placehold.co/100?text=No+Image';
        }

        // Cập nhật Action cho Form (Thêm query param id vào URL)
        const form = document.getElementById('editProductForm');
        form.action = '{{ BASE_URL }}/adminproduct/update?id=' + data.id;

        // 2. XỬ LÝ QUAN TRỌNG: Load Iframe Variants
        // Reset iframe để hiện loading
        const iframe = document.getElementById('edit-stock-iframe');
        const loader = document.getElementById('edit-stock-loader');
        
        iframe.classList.add('d-none');
        loader.classList.remove('d-none');
        
        // Set src cho iframe (Có thêm tham số iframe=true để ẩn header/footer trong view variants)
        iframe.src = '{{ BASE_URL }}/adminproduct/variants/' + data.id + '?iframe=true';

        // 3. Mở Modal
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
        
        // Reset tab về tab đầu tiên
        const firstTab = new bootstrap.Tab(document.querySelector('#editTabs button[data-bs-target="#info-pane"]'));
        firstTab.show();
    }
</script>

@include('admin.layouts.footer')