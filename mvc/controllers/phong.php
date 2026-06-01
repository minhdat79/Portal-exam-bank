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
                "Page" => "phong", 
                "Title" => "Quản lý phòng ban", 
                "Script" => "phong", 
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
            // Thay vì getBySubject, ta lấy toàn bộ phòng ban của trưởng phòng này
            $user_id = $_SESSION['user_id'];
            
            // XÂY LẠI CẤU TRÚC PHẲNG: Không cần nhóm theo Chủ đề nữa
            // Chúng ta giả lập trả về một cấu trúc mảng để không làm sập code JS cũ
            $raw_phong = $this->phongModel->getAllByAdmin($user_id); 
            
            // Nếu Model chưa có hàm getAllByAdmin, bạn tạm dùng getBySubject nhưng truyền null hoặc bypass
            // Tạm thời bọc toàn bộ phòng ban vào một mảng giả "Chung" để JS không bị lỗi undefined
            $result = [
                [
                    "machude" => "ALL",
                    "tenchude" => "Tất cả phòng ban",
                    "phong" => $raw_phong ? $raw_phong : []
                ]
            ];
            echo json_encode($result);
        } else
            echo json_encode(false);
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("phong", "create")) {
            $tenphong = $_POST['tenphong'];
            $ghichu = $_POST['ghichu'];
            $truongphong = $_SESSION['user_id']; 
            
            // Truyền 0 hoặc null vào chỗ của Chủ đề để Model không báo lỗi thiếu tham số
            $result = $this->phongModel->create($tenphong, $ghichu, $truongphong, 0);
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
            
            // Tương tự hàm Add, nhét số 0 vào vị trí của Chủ đề
            $result = $this->phongModel->update($maphong, $tenphong, $ghichu, 0);
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

    public function getUserList() 
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $result = $this->phongModel->getSvList($maphong); 
            echo json_encode($result);
        }
    }


    public function addUser()
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $maphong = $_POST['maphong'];
            $manguoidung = $_POST['manguoidung']; 
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
            $manguoidung = $_POST['manguoidung']; 
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
        // ... (Giữ nguyên code xuất excel của bạn) ...
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