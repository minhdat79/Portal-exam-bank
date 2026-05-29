<?php
require_once 'vendor/autoload.php';
require_once 'vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

class Phong extends Controller
{
    public $phongModel;

    function __construct()
    {
        $this->phongModel = $this->model("PhongModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("phong", "view")) {
            $this->view("main_layout", [
                "Page" => "phong", // Đã đổi Page
                "Title" => "Quản lý phòng ban", // Đã đổi Title
                "Script" => "phong", // Đã đổi Script
                "Plugin" => [
                    "sweetalert2" => 1,
                    "select" => 1,
                    "jquery-validate" => 1,
                    "notify" => 1
                ]
            ]);
        } else
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

    public function detail($maphong)
    {
        $chitietphong = $this->phongModel->getDetailGroup($maphong);
        // Đổi giangvien thành truongphong
        if (AuthCore::checkPermission("phong", "view") && $_SESSION['user_id'] == $chitietphong['truongphong']) {
            $this->view("main_layout", [
                "Page" => "class_detail",
                "Title" => "Quản lý chi tiết phòng",
                "Plugin" => [
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "sweetalert2" => 1,
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "pagination" => [],
                ],
                "Script" => "class_detail",
                "Detail" => $chitietphong
            ]);
        } else
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

    public function loadData()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $hienthi = $_POST['hienthi'];
            $user_id = $_SESSION['user_id'];
            $result = $this->phongModel->getBySubject($user_id, $hienthi);
            echo json_encode($result);
        } else
            echo json_encode(false);
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("phong", "create")) {
            $tenphong = $_POST['tenphong'];
            $ghichu = $_POST['ghichu'];
            $chude = $_POST['chude'];
            $truongphong = $_SESSION['user_id']; // Đã đổi từ giangvien
            $result = $this->phongModel->create($tenphong, $ghichu, $truongphong, $chude);
            echo $result;
        } else
            echo json_encode(false);
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("phong", "delete")) {
            $maphong = $_POST['maphong'];
            $result = $this->phongModel->delete($maphong);
            echo $result;
        } else
            echo json_encode(false);
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("phong", "update")) {
            $maphong = $_POST['maphong'];
            $tenphong = $_POST['tenphong'];
            $ghichu = $_POST['ghichu'];
            $chude = $_POST['chude'];
            $result = $this->phongModel->update($maphong, $tenphong, $ghichu, $chude);
            echo json_encode($result);
        } else
            echo json_encode(false);
    }

    public function hide()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("phong", "create")) {
            $maphong = $_POST['maphong'];
            $giatri = $_POST['giatri'];
            $result = $this->phongModel->hide($maphong, $giatri);
            echo $result;
        } else
            echo json_encode(false);
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("phong", "create")) {
            $maphong = $_POST['maphong'];
            $result = $this->phongModel->getById($maphong);
            echo json_encode($result);
        } else
            echo json_encode(false);
    }

    // Đã loại bỏ updateInvitedCode và getInvitedCode vì hệ thống SDTC không dùng mã mời (mamoi) nữa

    public function getUserList() 
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $result = $this->phongModel->getSvList($maphong); // Tạm giữ tên hàm model cũ để đỡ lỗi
            echo json_encode($result);
        }
    }


    public function addUser()
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $manguoidung = $_POST['manguoidung']; // Đổi từ mssv
            $hoten = $_POST['hoten'];
            $password = $_POST['password'];
            $result = $this->phongModel->addSV($manguoidung,$hoten,$password);
            $joinGroup = $this->phongModel->join($maphong,$manguoidung);
            echo $joinGroup;
        }
    }

    public function addUserGroup(){
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $manguoidung = $_POST['manguoidung']; // Đổi từ mssv
            $joinGroup = $this->phongModel->join($maphong,$manguoidung);
            echo ($joinGroup);
        }
    }

    public function checkAcc()
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $manguoidung = $_POST['manguoidung'];
            $result = $this->phongModel->checkAcc($manguoidung,$maphong);
            echo $result;
        }
    }
    
    public function exportExcelUsers()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $result = $this->phongModel->getStudentByGroup($maphong);
            
            $excel = new PHPExcel();
            $excel->setActiveSheetIndex(0);
            $excel->getActiveSheet()->setTitle("Danh sách kết quả");

            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

            $phpColor = new PHPExcel_Style_Color();
            $phpColor->setRGB('FFFFFF');
            $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
            $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setColor($phpColor);
            $excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '33FF33')
                    )
                )
            );
            $excel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->applyFromArray(
                array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            );

            // Đổi tiêu đề MSSV thành Mã NV
            $excel->getActiveSheet()->setCellValue('A1', 'Mã NV');
            $excel->getActiveSheet()->setCellValue('B1', 'Họ và tên');
            $excel->getActiveSheet()->setCellValue('C1', 'Email');
            $excel->getActiveSheet()->setCellValue('D1', 'Ngày tham gia');
            $excel->getActiveSheet()->setCellValue('E1', 'Ngày Sinh');
            $excel->getActiveSheet()->setCellValue('F1', 'Giới tính');
            
            $numRow = 2;
            foreach ($result as $row) {
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row["id"]);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row["hoten"]);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row["email"]);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row["ngaythamgia"]);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row["ngaysinh"]);
                if ($row["gioitinh"] == 0) {
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, "Nữ");
                } else if ($row["gioitinh"] == 1) {
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, "Nam");
                } else {
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, "Null");
                }

                $excel->getActiveSheet()->getStyle("A" . $numRow . ":F" . "$numRow")->getAlignment()->applyFromArray(
                    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                );
                $numRow++;
            }
            ob_start();
            $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $write->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'status' => TRUE,
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );

            die(json_encode($response));
        }
    }

    public function getGroupSize() {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $result = $this->phongModel->getGroupSize($maphong);
            echo $result;
        }
    }

    public function kickUser()
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $manguoidung = $_POST['manguoidung'];
            $result = $this->phongModel->kickUser($maphong,$manguoidung);
            echo $result;
        }
    }
}
?>