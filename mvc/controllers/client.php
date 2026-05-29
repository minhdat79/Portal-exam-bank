<?php
class Client extends Controller{

    public $nhommodel;
    public $dethimodel;
    public $nguoidungmodel;

    public function __construct()
    {
        $this->nhommodel = $this->model("PhongModel");
        $this->dethimodel = $this->model("DeThiModel");
        $this->nguoidungmodel = $this->model("NguoiDungModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function group()
    {
        if (AuthCore::checkPermission("tghocphan", "join")) {
            $this->view("main_layout", [
                "Page" => "client_group",
                "Title" => "Nhóm",
                "Script" => "client_group",
                "Plugin" => [
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "sweetalert2" => 1,
                    "select" => 1,
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }

    public function test() {
        if (AuthCore::checkPermission("tgthi", "join")) {
            $this->view("main_layout", [
                "Page" => "test_schedule",
                "Title" => "Lịch kiểm tra",
                "Script" => "test_schedule",
                "user_id" => $_SESSION['user_id'],
                "Plugin" => [
                    "pagination" => [],
                ],
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }

    // /client/test pagination
    public function getQuery($filter, $input, $args) {
        AuthCore::checkAuthentication();
        $query = $this->dethimodel->getQuery($filter, $input, $args);
        return $query;
    }

    public function joinGroup()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("tghocphan", "join")) {
            $manguoidung = $_SESSION['user_id'];
            if (isset($_POST['maphong'])) {
                $maphong = $_POST['maphong'];
                $result = $this->nhommodel->join($maphong,$manguoidung);
                if($result) {
                    echo json_encode($this->nhommodel->getDetailGroup($maphong));
                } else echo json_encode(1);
            } else {
                echo json_encode(0);
            }
        }
    }
    
    public function loadDataGroups() {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $manguoidung = $_SESSION['user_id'];
            $hienthi = $_POST['hienthi'];
            $result = $this->nhommodel->getAllGroup_User($manguoidung,$hienthi);
            echo json_encode($result);
        }
    }

    public function getFriendList()
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $manguoidung = $_SESSION['user_id'];
            $maphong = $_POST['maphong'];
            $result = $this->nhommodel->getSvList($maphong);
            $index = -1;
            $i = 0;
            while($i <= count($result) && $index == -1) {
                if($result[$i]['id'] == $manguoidung) {
                    $index = $i;
                } else $i++;
            }
            array_splice($result, $index, 1);
            echo json_encode($result);
        }
    }

    public function hide()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("tghocphan", "join")) {
            $maphong = $_POST['maphong'];
            $giatri =$_POST['giatri'];
            $manguoidung = $_SESSION['user_id'];
            $result = $this->nhommodel->sv_hide($maphong,$manguoidung,$giatri);
            echo $result;
        } else echo json_encode(false);
    }

    public function delete()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("tghocphan", "join")) {
            $maphong = $_POST['maphong'];
            $manguoidung = $_SESSION['user_id'];
            $result = $this->nhommodel->SVDelete($maphong,$manguoidung);
            echo $result;
        } else echo json_encode(false);
    }    
}
?>