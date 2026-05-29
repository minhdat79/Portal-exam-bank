Dashmix.helpersOnLoad(["jq-select2"]);

Dashmix.onLoad((() => class {
    static initValidation() {
        Dashmix.helpers("jq-validation"), jQuery(".form-add-group").validate({
            rules: {
                "ten-phong": {
                    required: !0,
                },
                "ghi-chu": {
                    required: !0,
                },
                "mon-hoc": {
                    required: !0,
                },
                "nam-hoc": {
                    required: !0
                },
                "hoc-ky": {
                    required: !0,
                },
            },
            messages: {
                "ten-phong": {
                    required: "Vui lòng nhập tên nhóm",
                },
                "ghi-chu": {
                    required: "Vui lòng không để trống trường này",
                },
                "mon-hoc": {
                    required: "Vui lòng chọn môn học",
                },
                "nam-hoc": {
                    required: "Vui lòng chọn năm học",
                },
                "hoc-ky": {
                    required: "Vui lòng chọn học kỳ",
                },
            }
        })
    }

    static init() {
        this.initValidation()
    }
}.init()));

$(document).ready(function () {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success me-2",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });

    let groups = [];
    let mode = 1;

    function loadDataGroup(hienthi) {
        $.ajax({
            type: "post",
            url: "./module/loadData",
            data: {
                hienthi: hienthi
            },
            dataType: "json",
            success: function (response) {
                showGroup(response);
                groups = response;
            }
        });
    }

    loadDataGroup(mode);

    function showGroup(list) {
        console.log(list)
        let html = "";
        let d = 0;
        if (list.length == 0) {
            html += `<p class="text-center mt-5">Không có dữ liệu</p>`
        } else {
            list.forEach((item, index) => {
                let htmlbtnhide = mode == 1 ? `<button data-index="${index}" type="button" class="btn btn-alt-secondary btn-sm btn-hide-all ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Ẩn tất cả"><i
                class="far fa-eye-slash"></i></button>` : `<button data-index="${index}" type="button" class="btn btn-alt-secondary btn-sm btn-unhide-all ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Huỷ tất cả"><i
                class="fa fa-rotate-left"></i></button>`
                html += `<div>
                    <div class="heading-group d-flex align-items-center">
                        <h2 class="content-heading pb-0" id="${d++}">${"<span class='machude'>" + item.machude + "</span>" + " - " + "<span class='tenchude'>" + item.tenchude + "</span>"}</h2>
                        ${htmlbtnhide}
                    </div>
                    <div class="row">`;
                item.phong.forEach((nhom_item) => {
                    let btn_hide = "";
                    if (nhom_item.hienthi == 1) {
                        btn_hide = `<a class="nav-main-link dropdown-item btn-hide-group" href="javascript:void(0)" data-id="${nhom_item.maphong}">
                            <i class="nav-main-link-icon si si-eye me-2 text-dark"></i>
                            <span class="nav-main-link-name fw-normal">Ẩn nhóm</span>
                        </a>`
                    } else {
                        btn_hide = `<a class="nav-main-link dropdown-item btn-unhide-group" href="javascript:void(0)" data-id="${nhom_item.maphong}">
                            <i class="nav-main-link-icon si si-action-undo me-2 text-dark"></i>
                            <span class="nav-main-link-name fw-normal">Khôi phục</span>
                        </a>`;
                    }
                    html += `
                        <div class="col-sm-6 col-lg-6 col-xl-3">
                        <div class="block block-rounded">
                            <div class="block-header block-header-default">
                                <h3 class="block-title block-class-name">${nhom_item.tenphong}</h3>
                                <div class="block-options">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-alt-secondary dropdown-toggle module__dropdown" id="dropdown-default-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="${nhom_item.maphong}">
                                    <i class="si si-settings"></i>
                                    </button>
                                    <div class="dropdown-menu  fs-sm" aria-labelledby="dropdown-default-light" style="">
                                    <a class="nav-main-link dropdown-item maphong" href="module/detail/${nhom_item.maphong}">
                                        <i class="nav-main-link-icon si si-info me-2 text-dark"></i>
                                        <span class="nav-main-link-name fw-normal">Danh sách sinh viên</span>
                                    </a>
                                    <a class="nav-main-link dropdown-item btn-update-group" href="javascript:void(0)" data-id="${nhom_item.maphong}" data-role="hocphan" data-action="update">
                                        <i class="nav-main-link-icon si si-pencil me-2 text-dark"></i>
                                        <span class="nav-main-link-name fw-normal">Sửa thông tin</span>
                                    </a>
                                    ${btn_hide}
                                    <a class="nav-main-link dropdown-item btn-delete-group" href="javascript:void(0)" data-id="${nhom_item.maphong}" data-role="hocphan" data-action="delete">
                                        <i class="nav-main-link-icon si si-trash me-2 text-danger"></i>
                                        <span class="nav-main-link-name fw-normal text-danger">Xoá nhóm</span>
                                    </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block-content">
                            <p class="block-class-note">${nhom_item.ghichu}</p>
                            <p class="Si-So">Sỉ số: <span>${nhom_item.siso}</span></p>
                        </div>
                        </div>
                    </div>`;
                });
                html += `</div></div>`;
            });
        }
        $("#class-group").html(html);
        $('[data-bs-toggle="tooltip"]').tooltip();
    }


    $.get(
        "./subject/getSubjectAssignment",
        function (data) {
            let html = "<option></option>";
            data.forEach((item) => {
                html += `<option value="${item.machude}">${item.machude + " - " + item.tenchude}</option>`;
            });
            $("#mon-hoc").html(html);
        },
        "json"
    );

    function renderListYear() {
        let html = "<option></option>";
        let currentYear = new Date().getFullYear();
        let start = currentYear - 10;
        let end = currentYear + 10;
        for (let i = start; i <= end; i++) {
            html += `<option value="${i}">${i + " - " + (i + 1)}</option>`;
        }
        $("#nam-hoc").html(html);
        $("#nam-hoc").val(currentYear).trigger("change")
    }

    renderListYear();

    $("#add-group").click(function (e) {
        e.preventDefault();
        if($(".form-add-group").valid()) {
            $.ajax({
                type: "post",
                url: "./module/add",
                data: {
                    tenphong: $("#ten-phong").val(),
                    ghichu: $("#ghi-chu").val(),
                    chude: $("#mon-hoc").val()
                },
                success: function (response) {
                    if (response) {
                        $("#modal-add-group").modal("hide");
                        loadDataGroup(mode);
                        Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: 'Thêm nhóm thành công!' });
                    } else {
                        Dashmix.helpers('jq-notify', { type: 'danger', icon: 'fa fa-times me-1', message: 'Thêm nhóm không thành công!' });
                    }
                }
            });
        }
    });

    $(document).on("click", ".btn-hide-all", function () {
        let index = $(this).data("index");
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "Bạn có chắc chắn muốn ẩn hết các nhóm môn học này không!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Chắc chắn!",
                cancelButtonText: "Không!",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    groups[index].phong.forEach(item => {
                        updateHide(item.maphong, 0);
                    });
                    groups.splice(index, 1);
                    Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: 'Ẩn nhóm thành công!' });
                    showGroup(groups);
                }
            });
    })

    $(document).on("click", ".btn-unhide-all", function () {
        let index = $(this).data("index");
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "Bạn có chắc chắn muốn huỷ ẩn hết các nhóm môn học này không!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Chắc chắn!",
                cancelButtonText: "Không!",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    groups[index].phong.forEach(item => {
                        updateHide(item.maphong, 1);
                    });
                    groups.splice(index, 1);
                    Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: 'Ẩn nhóm thành công!' });
                    showGroup(groups);
                }
            });
    })

    $(document).on("click", ".btn-delete-group", function () {
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "./module/delete",
                        data: {
                            maphong: $(this).data("id"),
                        },
                        success: function (response) {
                            if (response) {
                                swalWithBootstrapButtons.fire(
                                    "Xoá thành công!",
                                    "Nhóm đã được xoá thành công",
                                    "success"
                                );
                                loadDataGroup(mode);
                            }
                        },
                    });
                }
            });
    })

    $(document).on("click", ".btn-hide-group", function () {
        let maphong = $(this).data("id");
        updateHide(maphong, 0).then(response => {
            removeItem(maphong);
            showGroup(groups);
            Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: 'Ẩn nhóm thành công!' });
        }).catch(error => {
            Dashmix.helpers('jq-notify', { type: 'danger', icon: 'fa fa-times me-1', message: 'Ẩn nhóm không thành công!' });
        });
    });

    function removeItem(maphong) {
        for (let i = 0; i < groups.length; i++) {
            let index = groups[i].phong.findIndex(item => item.maphong == maphong)
            if (index != -1) {
                groups[i].phong.splice(index, 1);
                if (groups[i].phong.length == 0) groups.splice(i, 1);
                break;
            }
        }
    }

    $(document).on("click", ".btn-unhide-group", function () {
        let maphong = $(this).data("id");
        updateHide(maphong, 1).then(response => {
            removeItem(maphong);
            showGroup(groups);
            Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: 'Huỷ ẩn nhóm thành công!' });
        }).catch(error => {
            Dashmix.helpers('jq-notify', { type: 'danger', icon: 'fa fa-times me-1', message: 'Huỷ ẩn nhóm không thành công!' });
        });
    });

    function updateHide(maphong, giatri) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: "./module/hide",
                data: {
                    maphong: maphong,
                    giatri: giatri
                },
                success: function (response) {
                    resolve(response);
                },
                error: function (error) {
                    reject(error);
                }
            });
        });
    }

    $(document).on("click", ".btn-update-group", function () {
        $(".add-group-element").hide();
        $(".update-group-element").show();
        $("#modal-add-group").modal("show");
        let id = $(this).data("id")
        $("#update-group").data("id", id)
        $.ajax({
            type: "post",
            url: "./module/getDetail",
            data: {
                maphong: id
            },
            dataType: "json",
            success: function (response) {
                $("#ten-phong").val(response.tenphong),
                $("#ghi-chu").val(response.ghichu),
                $("#mon-hoc").val(response.machude).trigger("change")
            }
        });
    });

    $("#update-group").click(function (e) {
        e.preventDefault();
        if($(".form-add-group").valid()) { 
            $.ajax({
                type: "post",
                url: "./module/update",
                data: {
                    maphong: $(this).data("id"),
                    tenphong: $("#ten-phong").val(),
                    ghichu: $("#ghi-chu").val(),
                    chude: $("#mon-hoc").val()
                },
                success: function (response) {
                    console.log(response)
                    if (response == "true") {
                        $("#modal-add-group").modal("hide");
                        loadDataGroup(mode);
                        Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: 'Cập nhật nhóm thành công!' });
                    } else {
                        Dashmix.helpers('jq-notify', { type: 'danger', icon: 'fa fa-times me-1', message: 'Cập nhật nhóm không thành công!' });
                    }
                }
            });
        }
    });

    $("[data-bs-target='#modal-add-group']").click(function (e) {
        e.preventDefault();
        $(".add-group-element").show();
        $(".update-group-element").hide();
    });

    // Reset form khi đóng modal
    $("#modal-add-group").on('hidden.bs.modal', function () {
        $("#ten-phong").val(""),
            $("#ghi-chu").val(""),
            $("#mon-hoc").val("").trigger("change"),
            $("#nam-hoc").val("").trigger("change"),
            $("#hoc-ky").val("").trigger("change")
    });

    // Thay đổi text khi nhấn vào dropdown
    $(".filter-search").click(function (e) {
        e.preventDefault();
        $(".btn-filter").text($(this).text());
        mode = $(this).data("value")
        loadDataGroup(mode);
    });

    $("#form-search-group").on("input", function () {
        let result = [];
        let content = $(this).val().toLowerCase();
        console.log(groups);
        for (let i = 0; i < groups.length; i++) {
            if (groups[i].machude.includes(content) || groups[i].tenchude.toLowerCase().includes(content)) {
                result.push(groups[i]);
            }
        }
        showGroup(result);
    });

});