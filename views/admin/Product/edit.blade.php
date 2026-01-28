<!-- MODAL EDIT PRODUCT: Clean Design -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editProductForm" action="" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <div class="modal-header bg-warning-subtle border-bottom-0 pt-4 px-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark fs-4">Cập Nhật Sản Phẩm</h5>
                    <p class="text-muted small mb-0">Chỉnh sửa thông tin chi tiết</p>
                </div>
                <button type="button" class="btn-close rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'edit')
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $_SESSION['error'] }}
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <!-- Tên Sản Phẩm -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">TÊN SẢN PHẨM</label>
                            <input type="text" name="name" id="edit_name"
                                   value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['name'] ?? '') : '' }}"
                                   class="form-control bg-light border-0 py-2 fw-bold" required>
                        </div>

                        <!-- Giá & Selects -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary">GIÁ BÁN</label>
                                <div class="input-group border-0 bg-light rounded-2 overflow-hidden">
                                    <span class="input-group-text border-0 bg-transparent text-muted">₫</span>
                                    <input type="number" name="price" id="edit_price"
                                           value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['price'] ?? '') : '' }}"
                                           class="form-control border-0 bg-transparent fw-bold text-dark shadow-none" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">DANH MỤC</label>
                                <select name="category_id" id="edit_category_id" class="form-select bg-light border-0 py-2" required>
                                    @foreach ($all_categories ?? [] as $c)
                                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">THƯƠNG HIỆU</label>
                                <select name="brand_id" id="edit_brand_id" class="form-select bg-light border-0 py-2" required>
                                    @foreach ($all_brands ?? [] as $b)
                                        <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cột Ảnh: Preview lớn hơn -->
                    <div class="col-lg-4">
                        <label class="form-label fw-bold small text-secondary">ẢNH HIỆN TẠI</label>
                        <div class="card border-0 shadow-sm text-center overflow-hidden">
                            <div class="bg-light p-2" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                <img id="edit_img_preview" src="" class="img-fluid rounded shadow-sm" style="max-height: 100%; max-width: 100%;">
                            </div>
                            <div class="card-body p-2 bg-white">
                                <label class="btn btn-outline-warning btn-sm w-100 fw-bold border-2 rounded-pill">
                                    <i class="bi bi-camera-fill me-1"></i> Thay đổi ảnh
                                    <input type="file" name="image" class="d-none" accept="image/*" 
                                           onchange="document.getElementById('edit_img_preview').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm text-dark">
                    <i class="bi bi-save me-1"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

@php
    if (isset($_SESSION['error_type']) && $_SESSION['error_type'] === 'edit') {
        unset($_SESSION['error']); unset($_SESSION['error_type']); unset($_SESSION['old']);
    }
@endphp