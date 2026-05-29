<div class="content">
    <div class="row">
        <div class="col-6 flex-grow-1">
            <div class="input-group">
                <button class="btn btn btn-alt-primary dropdown-toggle btn-filter" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">Phòng ban hoạt động</button>
                <ul class="dropdown-menu mt-1">
                    <li><a class="dropdown-item filter-search" href="javascript:void(0)" data-value="1">Phòng ban hoạt động</a></li>
                    <li><a class="dropdown-item filter-search" href="javascript:void(0)" data-value="0">Đã ẩn</a></li>
                </ul>
                <input type="text" class="form-control" placeholder="Tìm kiếm phòng ban..." id="form-search-group">
            </div>
        </div>
        <div class="col-6 d-flex align-items-center justify-content-end gap-3">
            <button type="button" class="btn btn-hero btn-primary" data-bs-toggle="modal"
                data-bs-target="#modal-add-group" data-role="phong" data-action="create"><i class="fa fa-fw fa-plus me-1"></i> Thêm phòng ban</button>
        </div>
    </div>
    <div class="class-group" id="class-group">
    </div>
</div>
<div class="modal fade" id="modal-add-group" tabindex="-1" role="dialog" aria-labelledby="modal-add-group"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title add-group-element">Thêm phòng ban</h3>
                    <h3 class="block-title update-group-element">Cập nhật phòng ban</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="block-content fs-sm form-add-group">
                    <div class="mb-3">
                        <label for="ten-phong" class="form-label">Tên phòng ban</label>
                        <input type="text" class="form-control" name="ten-phong" id="ten-phong"
                            placeholder="Nhập tên phòng ban (VD: Phòng GD 1)">
                    </div>
                    <div class="mb-3">
                        <label for="ghi-chu" class="form-label">Ghi chú</label>
                        <input type="text" class="form-control" name="ghi-chu" id="ghi-chu" placeholder="Nhập ghi chú">
                    </div>
                    <div class="mb-3">
                        <label for="mon-hoc" class="form-label">Chủ đề nghiệp vụ</label>
                        <select class="js-select2 form-select" id="mon-hoc" name="mon-hoc" style="width: 100%;"
                            data-placeholder="Chọn chủ đề nghiệp vụ">
                        </select>
                    </div>
                    </form>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-primary add-group-element" id="add-group">Lưu</button>
                    <button type="button" class="btn btn-sm btn-primary update-group-element" id="update-group"
                        data-id="">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</div>