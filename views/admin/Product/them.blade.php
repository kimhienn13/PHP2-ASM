<!-- MODAL ADD PRODUCT: Clean Design -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ BASE_URL }}/adminproduct/store" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark fs-4">Thêm Sản Phẩm Mới</h5>
                    <p class="text-muted small mb-0">Vui lòng điền đầy đủ thông tin bên dưới</p>
                </div>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Error Alert -->
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'add')
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        <div>{{ $_SESSION['error'] }}</div>
                        @php unset($_SESSION['error']); unset($_SESSION['error_type']); @endphp
                    </div>
                @endif

                <div class="row g-4">
                    <!-- Cột Trái: Thông tin chính -->
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">TÊN SẢN PHẨM <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 py-2 fw-bold" placeholder="VD: iPhone 15 Pro Max..." required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">DANH MỤC</label>
                                <select name="category_id" class="form-select bg-light border-0 py-2" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @if(!empty($all_categories))
                                        @foreach ($all_categories as $c)
                                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">THƯƠNG HIỆU</label>
                                <select name="brand_id" class="form-select bg-light border-0 py-2" required>
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @if(!empty($all_brands))
                                        @foreach ($all_brands as $b)
                                            <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold small text-secondary">GIÁ BÁN <span class="text-danger">*</span></label>
                            <div class="input-group border-0 bg-light rounded-2 overflow-hidden">
                                <span class="input-group-text border-0 bg-transparent text-muted">₫</span>
                                <input type="number" name="price" class="form-control border-0 bg-transparent fw-bold text-dark shadow-none" placeholder="0" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Cột Phải: Upload Ảnh -->
                    <div class="col-lg-4">
                        <label class="form-label fw-bold small text-secondary">ẢNH ĐẠI DIỆN</label>
                        <div class="upload-zone text-center p-3 border-2 border-dashed rounded-3 bg-light position-relative h-100 d-flex flex-column justify-content-center align-items-center" style="min-height: 200px; border-color: #cbd5e1 !important;">
                            <i class="bi bi-cloud-arrow-up-fill fs-1 text-primary mb-2 opacity-50"></i>
                            <span class="small fw-bold text-dark">Tải ảnh lên</span>
                            <span class="extra-small text-muted d-block mt-1">(JPG, PNG, WEBP)</span>
                            <input type="file" name="image" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept="image/*" 
                                   onchange="previewImage(this, 'add_preview_img')">
                            <!-- Preview Image Container (Hidden by default) -->
                            <img id="add_preview_img" src="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rounded-3 d-none pointer-events-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Lưu sản phẩm
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
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .pointer-events-none { pointer-events: none; }
    .upload-zone:hover { border-color: #4f46e5 !important; background-color: #eef2ff !important; transition: 0.3s; }
</style>