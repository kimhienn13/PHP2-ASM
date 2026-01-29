<!-- MODAL ADD PRODUCT -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form action="{{ BASE_URL }}/adminproduct/store" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark fs-4">Thêm Sản Phẩm Mới</h5>
                    <p class="text-muted small mb-0">Nhập thông tin sản phẩm và cấu hình kho hàng ban đầu</p>
                </div>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <!-- CỘT TRÁI: THÔNG TIN CƠ BẢN -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white fw-bold text-primary border-bottom-0 pt-3">1. Thông tin chung</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Tên Sản Phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control bg-light border-0" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Giá Niêm Yết <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="price" class="form-control bg-light border-0" required>
                                        <span class="input-group-text bg-light border-0 text-secondary">VNĐ</span>
                                    </div>
                                </div>

                                <!-- KHU VỰC DANH MỤC & THƯƠNG HIỆU CÓ CHỨC NĂNG THÊM NHANH -->
                                <div class="row g-2 mb-3">
                                    
                                    <!-- Danh mục -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-secondary small">Danh Mục</label>
                                        <div class="input-group">
                                            <!-- Select Box (Mặc định hiện) -->
                                            <select name="category_id" id="cat_select" class="form-select bg-light border-0">
                                                <option value="">-- Chọn danh mục --</option>
                                                @foreach ($categories as $c) <option value="{{ $c['id'] }}">{{ $c['name'] }}</option> @endforeach
                                            </select>
                                            
                                            <!-- Input Text (Mặc định ẩn) -->
                                            <input type="text" name="new_category" id="cat_input" class="form-control bg-light border-0 d-none" placeholder="Nhập tên danh mục mới...">
                                            
                                            <!-- Nút chuyển đổi -->
                                            <button type="button" class="btn btn-outline-secondary border-0 bg-light" onclick="toggleQuickAdd('cat')" title="Thêm danh mục mới">
                                                <i class="bi bi-plus-lg" id="cat_icon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Thương hiệu -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-secondary small">Thương Hiệu</label>
                                        <div class="input-group">
                                            <select name="brand_id" id="brand_select" class="form-select bg-light border-0">
                                                <option value="">-- Chọn thương hiệu --</option>
                                                @foreach ($brands as $b) <option value="{{ $b['id'] }}">{{ $b['name'] }}</option> @endforeach
                                            </select>
                                            
                                            <input type="text" name="new_brand" id="brand_input" class="form-control bg-light border-0 d-none" placeholder="Nhập tên thương hiệu mới...">
                                            
                                            <button type="button" class="btn btn-outline-secondary border-0 bg-light" onclick="toggleQuickAdd('brand')" title="Thêm thương hiệu mới">
                                                <i class="bi bi-plus-lg" id="brand_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Ảnh Đại Diện</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 60px; height: 60px;" class="flex-shrink-0 rounded-3 overflow-hidden border">
                                            <img id="add_preview_img" src="https://placehold.co/60?text=IMG" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <input type="file" name="image" class="form-control bg-light border-0" accept="image/*" onchange="previewImage(this, 'add_preview_img')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CỘT PHẢI: CẤU HÌNH BIẾN THỂ -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white fw-bold text-success border-bottom-0 pt-3 d-flex justify-content-between align-items-center">
                                <span>2. Cấu hình Kho & Biến thể</span>
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" onclick="addVariantRow()">
                                    <i class="bi bi-plus-lg"></i> Thêm dòng
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-borderless mb-0 align-middle">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="ps-3 small text-secondary">Màu sắc</th>
                                                <th class="small text-secondary">Kích thước</th>
                                                <th class="small text-secondary" width="100">Số lượng</th>
                                                <th class="text-end pe-3" width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="variant-container">
                                            <!-- Dòng mặc định -->
                                            <tr class="variant-row border-bottom border-light">
                                                <td class="ps-3">
                                                    <select name="variants_color[]" class="form-select form-select-sm bg-light border-0">
                                                        <option value="">-- Chọn --</option>
                                                        @foreach($all_colors ?? [] as $col) <option value="{{ $col['id'] }}">{{ $col['name'] }}</option> @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="variants_size[]" class="form-select form-select-sm bg-light border-0">
                                                        <option value="">-- Chọn --</option>
                                                        @foreach($all_sizes ?? [] as $siz) <option value="{{ $siz['id'] }}">{{ $siz['name'] }}</option> @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="variants_qty[]" class="form-control form-control-sm bg-light border-0 text-center fw-bold" value="0" min="0">
                                                </td>
                                                <td class="text-end pe-3">
                                                    <button type="button" class="btn btn-sm btn-light text-danger border-0" onclick="removeVariantRow(this)"><i class="bi bi-x-lg"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 px-4 pb-4 pt-0 bg-light">
                <button type="button" class="btn btn-white border rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Lưu Tất Cả
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addVariantRow() {
        const container = document.getElementById('variant-container');
        const firstRow = container.querySelector('.variant-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        newRow.querySelector('input').value = 0;
        container.appendChild(newRow);
    }

    function removeVariantRow(btn) {
        const container = document.getElementById('variant-container');
        if (container.querySelectorAll('.variant-row').length > 1) {
            btn.closest('tr').remove();
        }
    }

    // Hàm chuyển đổi giữa Select và Input Text
    function toggleQuickAdd(type) {
        const select = document.getElementById(type + '_select');
        const input = document.getElementById(type + '_input');
        const icon = document.getElementById(type + '_icon');
        
        if (input.classList.contains('d-none')) {
            // Chuyển sang chế độ nhập mới
            select.classList.add('d-none');
            select.value = ""; // Reset select
            input.classList.remove('d-none');
            input.focus();
            icon.classList.remove('bi-plus-lg');
            icon.classList.add('bi-arrow-counterclockwise'); // Icon quay lại
        } else {
            // Quay lại chế độ chọn
            input.classList.add('d-none');
            input.value = ""; // Reset input
            select.classList.remove('d-none');
            icon.classList.remove('bi-arrow-counterclockwise');
            icon.classList.add('bi-plus-lg');
        }
    }
</script>