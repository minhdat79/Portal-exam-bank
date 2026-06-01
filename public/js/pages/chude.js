Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery(".form-add-subject").validate({
          rules: {
            machude: {
              required: !0,
              digits: true,
            },
            tenchude: {
              required: !0,
            },
          },
          messages: {
            machude: {
              required: "Vui lòng nhập mã chủ đề",
              digits: "Mã chủ đề phải là các ký tự số",
            },
            tenchude: {
              required: "Vui lòng cung cấp tên chủ đề",
            },
          },
        });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);

function showData(subjects) {
  let html = "";
  subjects.forEach((subject) => {
    html += `<tr tid="${subject.machude}">
              <td class="text-center fs-sm"><strong>${subject.machude}</strong></td>
              <td>${subject.tenchude}</td>
              <td class="text-center col-action">
                  <a data-role="chuong" data-action="view" class="btn btn-sm btn-alt-secondary subject-info" data-bs-toggle="modal" data-bs-target="#modal-chapter" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Thêm chương" data-bs-original-title="Chi tiết chương" data-id="${subject.machude}">
                      <i class="fa fa-circle-info"></i>
                  </a>
                  <a data-role="chude" data-action="update" class="btn btn-sm btn-alt-secondary btn-edit-subject" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Sửa chủ đề" data-bs-original-title="Sửa chủ đề" data-id="${subject.machude}">
                      <i class="fa fa-fw fa-pencil"></i>
                  </a>
                  <a data-role="chude" data-action="delete" class="btn btn-sm btn-alt-secondary btn-delete-subject" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Xoá chủ đề" data-bs-original-title="Xoá chủ đề" data-id="${subject.machude}">
                      <i class="fa fa-fw fa-times"></i>
                  </a>
              </td>
          </tr>`;
  });
  $("#list-subject").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
}

$(document).ready(function () {
  $("[data-bs-target='#modal-add-subject']").click(function (e) {
    e.preventDefault();
    $(".update-subject-element").hide();
    $(".add-subject-element").show();
  });

  function checkTonTai(machude) {
    let check = true;
    $.ajax({
      type: "post",
      url: "./chude/checkTopic",
      data: {
        machude: machude,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length !== 0) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `Chủ đề đã tồn tại!`,
          });
          check = false;
        }
      },
    });
    return check;
  }

  $("#add_subject").on("click", function () {
    let machude = $("#machude").val();
    if ($(".form-add-subject").valid() && checkTonTai(machude)) {
      $.ajax({
        type: "post",
        url: "./chude/add",
        data: {
          machude: machude,
          tenchude: $("#tenchude").val(),
        },
        success: function (response) {
          if (response) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Thêm chủ đề thành công!",
            });
            $("#modal-add-subject").modal("hide");
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Thêm chủ đề không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-edit-subject", function () {
    $(".update-subject-element").show();
    $(".add-subject-element").hide();
    let machude = $(this).data("id");
    $.ajax({
      type: "post",
      url: "./chude/getDetail",
      data: {
        machude: machude,
      },
      dataType: "json",
      success: function (response) {
        if (response) {
          $("#machude").val(response.machude),
            $("#tenchude").val(response.tenchude),
            $("#modal-add-subject").modal("show"),
            $("#update_subject").data("id", response.machude);
        }
      },
    });
  });

  $("#modal-add-subject").on("hidden.bs.modal", function () {
    $("#machude").val(""),
      $("#tenchude").val(""),
      $("#update_subject").data("id", "");
  });

  $("#update_subject").click(function (e) {
    e.preventDefault();
    let id = $(this).data("id");
    let machude = $("#machude").val();

    if ($(".form-add-subject").valid()) {
      if (id !== machude && !checkTonTai(machude)) {
        return;
      }
      $.ajax({
        type: "post",
        url: "./chude/update",
        data: {
          id: id,
          machude: machude,
          tenchude: $("#tenchude").val(),
        },
        success: function (response) {
          if (response) {
            $("#modal-add-subject").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Cập nhật chủ đề thành công!",
            });
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật chủ đề không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-delete-subject", function () {
    let trid = $(this).data("id");
    let e = Swal.mixin({
      buttonsStyling: !1,
      target: "#page-container",
      customClass: {
        confirmButton: "btn btn-success m-1",
        cancelButton: "btn btn-danger m-1",
        input: "form-control",
      },
    });

    e.fire({
      title: "Are you sure?",
      text: "Bạn có chắc chắn muốn xoá chủ đề?",
      icon: "warning",
      showCancelButton: !0,
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
      },
      confirmButtonText: "Vâng, tôi chắc chắn!",
      html: !1,
      preConfirm: (e) =>
        new Promise((e) => {
          setTimeout(() => {
            e();
          }, 50);
        }),
    }).then((t) => {
      if (t.value == true) {
        $.ajax({
          type: "post",
          url: "./chude/delete",
          data: {
            machude: trid,
          },
          success: function (response) {
            if (response) {
              e.fire("Deleted!", "Xóa chủ đề thành công!", "success");
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              e.fire("Lỗi !", "Xoá chủ đề không thành công !)", "error");
            }
          },
        });
      }
    });
  });

  //chapter
  $(document).on("click", ".subject-info", function () {
    var id = $(this).data("id");
    $("#mamon_chuong").val(id);
    showChapter(id);
  });

  function resetFormChapter() {
    $("#collapseChapter").collapse("hide");
    $("#name_chapter").val("");
  }

  $("#modal-chapter").on("hidden.bs.modal", function () {
    resetFormChapter();
  });

  function showChapter(machude) {
    $.ajax({
      type: "post",
      url: "./chude/getAllChapter",
      data: {
        machude: machude,
      },
      dataType: "json",
      success: function (response) {
        let html = "";
        if (response.length > 0) {
          response.forEach((chapter, index) => {
            html += `<tr>
                        <td class="text-center fs-sm"><strong>${
                          index + 1
                        }</strong></td>
                        <td>${chapter["tenchuong"]}</td>
                        <td class="text-center col-action">
                            <a data-role="chuong" data-action="update" class="btn btn-sm btn-alt-secondary chapter-edit"
                                data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit" data-id="${
                                  chapter["machuong"]
                                }">
                                <i class="fa fa-fw fa-pencil"></i>
                            </a>
                            <a data-role="chuong" data-action="delete" class="btn btn-sm btn-alt-secondary chapter-delete" href="javascript:void(0)"
                                data-bs-toggle="tooltip" aria-label="Delete"
                                data-bs-original-title="Delete" data-id="${
                                  chapter["machuong"]
                                }">
                                <i class="fa fa-fw fa-times"></i>
                            </a>
                        </td>
                    </tr>`;
          });
        } else {
          html += `<tr><td class="text-center fs-sm" colspan="3">
                    <img style="width:180px" src="./public/media/svg/empty_data.png" alt=""/>
                    <p class="text-center mt-3">Không có dữ liệu</p>
                    </td>
                    </tr>`;
        }
        $("#showChapper").html(html);
      },
    });
  }

  $("#btn-add-chapter").click(function () {
    $("#add-chapter").show();
    $("#edit-chapter").hide();
    $("#name_chapter").val("");
  });

  $("#add-chapter").on("click", function (e) {
    e.preventDefault();
    let machude = $("#mamon_chuong").val();
    if ($("#name_chapter").val() == "") {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Tên chương không để trống!",
      });
    } else {
      $.ajax({
        type: "post",
        url: "./chude/addChapter",
        data: {
          machude: machude,
          tenchuong: $("#name_chapter").val(),
        },
        success: function (response) {
          if (response) {
            resetFormChapter();
            showChapter(machude);
          }
        },
      });
    }
  });

  $(".close-chapter").click(function (e) {
    e.preventDefault();
    $("#collapseChapter").collapse("hide");
  });

  $(document).on("click", ".chapter-delete", function () {
    let machuong = $(this).data("id");
    $.ajax({
      type: "post",
      url: "./chude/chapterDelete",
      data: {
        machuong: machuong,
      },
      success: function (response) {
        if (response) {
          Dashmix.helpers("jq-notify", {
            type: "success",
            icon: "fa fa-check me-1",
            message: "Xoá chương thành công!",
          });
          showChapter($("#mamon_chuong").val());
        } else {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Xoá chương không thành công!",
          });
        }
      },
    });
  });

  $(document).on("click", ".chapter-edit", function () {
    $("#add-chapter").hide();
    $("#edit-chapter").show();
    let id = $(this).data("id");
    $("#machuong").val(id);
    $("#collapseChapter").collapse("show");
    let name = $(this).closest("td").closest("tr").children().eq(1).text();
    $("#name_chapter").val(name);
  });

  $("#edit-chapter").on("click", function (e) {
    e.preventDefault();
    $.ajax({
      type: "post",
      url: "./chude/updateChapter",
      data: {
        machuong: $("#machuong").val(),
        tenchuong: $("#name_chapter").val(),
      },
      success: function (response) {
        if (response) {
          showChapter($("#mamon_chuong").val());
          resetFormChapter();
          Dashmix.helpers("jq-notify", {
            type: "success",
            icon: "fa fa-check me-1",
            message: "Cập nhật chương thành công!",
          });
        } else {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Cập nhật chương không thành công!",
          });
        }
      },
    });
  });
});

// Pagination
const mainPagePagination = new Pagination();
mainPagePagination.option.controller = "chude";
mainPagePagination.option.model = "ChuDeModel";
mainPagePagination.option.limit = 10;
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);