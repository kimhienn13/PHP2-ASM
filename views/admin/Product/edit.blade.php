<!-- MODAL EDIT PRODUCT (TABBED) -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" style="min-height: 600px;">
            
            <div class="modal-header bg-white border-bottom px-4 pt-3 pb-0">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="modal-title fw-bold text-dark">Chỉnh sửa sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- TABS -->
                    <ul class="nav nav-tabs border-bottom-0" id="editTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold border-0 border-bottom border-3 border-primary text-primary" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i> Thông tin chung
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold border-0 text-secondary" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock-pane" type="button" role="tab">
                                <i class="bi bi-box-seam me-1"></i> Kho & Biến thể & Album
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="modal-body p-0 bg-light">
                <div class="tab-content h-100" id="editTabsContent">
                    
                    <!-- TAB 1: EDIT INFO -->
                    <div class="tab-pane fade show active h-100 p-4" id="info-pane" role="tabpanel">
                        <!-- Form Action sẽ được set bằng JS -->
                        <form id="editProductForm" action="" method="POST" enctype="multipart/form-data">
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-secondary">TÊN SẢN PHẨM</label>
                                        <input type="text" name="name" id="edit_name" class="form-control bg-white border-0 py-2 fw-bold shadow-sm" required>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-secondary">GIÁ BÁN</label>
                                            <input type="number" name="price" id="edit_price" class="form-control border-0 bg-white fw-bold shadow-sm" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-secondary">DANH MỤC</label>
                                            <select name="category_id" id="edit_category_id" class="form-select bg-white border-0 py-2 shadow-sm">
                                                @foreach ($all_categories ?? [] as $c) <option value="{{ $c['id'] }}">{{ $c['name'] }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-secondary">THƯƠNG HIỆU</label>
                                            <select name="brand_id" id="edit_brand_id" class="form-select bg-white border-0 py-2 shadow-sm">
                                                @foreach ($all_brands ?? [] as $b) <option value="{{ $b['id'] }}">{{ $b['name'] }}</option> @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- [NEW] INPUT THÊM ẢNH GALLERY -->
                                    <div class="mt-4">
                                        <label class="form-label fw-bold small text-secondary">THÊM ẢNH VÀO ALBUM</label>
                                        <input type="file" name="gallery[]" class="form-control bg-white border-0 shadow-sm" multiple accept="image/*">
                                        <div class="form-text small">Để quản lý/xóa ảnh cũ, vui lòng chuyển sang tab "Kho & Biến thể & Album".</div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label fw-bold small text-secondary">ẢNH ĐẠI DIỆN HIỆN TẠI</label>
                                    <div class="card border-0 shadow-sm text-center">
                                        <div class="bg-white p-2" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                            <img id="edit_img_preview" src="" class="img-fluid rounded" style="max-height: 100%;">
                                        </div>
                                        <div class="card-body p-2">
                                            <input type="hidden" name="current_image" id="edit_current_image">
                                            <input type="file" name="image" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="bi bi-save me-1"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: MANAGE VARIANTS (IFRAME) -->
                    <div class="tab-pane fade h-100" id="stock-pane" role="tabpanel">
                        <div id="edit-stock-loader" class="d-flex align-items-center justify-content-center h-100" style="min-height: 400px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <iframe id="edit-stock-iframe" src="" class="w-100 h-100 border-0 d-none" style="min-height: 500px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('#editTabs button');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => {
                    t.classList.remove('border-primary', 'text-primary');
                    t.classList.add('text-secondary', 'border-transparent');
                });
                this.classList.add('border-primary', 'text-primary');
                this.classList.remove('text-secondary', 'border-transparent');
            });
        });

        const iframe = document.getElementById('edit-stock-iframe');
        const loader = document.getElementById('edit-stock-loader');
        iframe.onload = function() {
            if (iframe.src) {
                loader.classList.add('d-none');
                iframe.classList.remove('d-none');
            }
        };
    });
</script>