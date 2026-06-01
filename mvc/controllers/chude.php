<?php
require_once "./mvc/core/AuthCore.php";

class ChuDe extends Controller
{
    public $chuDeModel;
    public $chuongModel;

    public function __construct()
    {
        $this->chuDeModel = $this->model("ChuDeModel");
        $this->chuongModel = $this->model("ChuongModel");
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("chude", "view")) {
            $this->view("main_layout", [
                "Page" => "chude",
                "Title" => "Quản lý chủ đề",
                "Script" => "chude",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "pagination" => [],
                ]
            ]);
        } else $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

public function add()
    {
        $machude = $_POST['machude'];
        $tenchude = $_POST['tenchude'];
        
        // Mẹo: Truyền số 0 vào 3 vị trí của tín chỉ, tiết LT, tiết TH để Model không bị lỗi thiếu tham số
        $result = $this->chuDeModel->create($machude, $tenchude, 0, 0, 0);
        echo $result;
    }

    public function checkTopic()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $machude = $_POST['machude'];
            $result = $this->chuDeModel->checkSubject($machude);
            echo json_encode($result);
        }
    }

   public function update()
    {
        $id = $_POST['id'];
        $machude = $_POST['machude'];
        $tenchude = $_POST['tenchude'];
        
        // Tương tự, nhét số 0 vào để lấp đầy tham số cũ
        $result = $this->chuDeModel->update($id, $machude, $tenchude, 0, 0, 0);
        echo $result;
    }

    public function delete()
    {
        $machude = $_POST['machude'];
        $result = $this->chuDeModel->delete($machude);
        echo $result;
    }

   public function getTopicAssignment()
    {
        $data = $this->chuDeModel->search("");
        
       
        $result = [];
        if ($data) {
            foreach ($data as $row) {
                $result[] = [
                    'machude' => isset($row['machude']) ? $row['machude'] : (isset($row['mamon']) ? $row['mamon'] : ''),
                    'tenchude' => isset($row['tenchude']) ? $row['tenchude'] : (isset($row['tenmon']) ? $row['tenmon'] : '')
                ];
            }
        }
        echo json_encode($result);
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = $this->chuDeModel->getById($_POST['machude']);
            echo json_encode($data);
        }
        echo false;
    }


    public function getAllChapter()
    {
        $result = $this->chuongModel->getAll($_POST['machude']);
        echo json_encode($result);
    }

    public function chapterDelete()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $result = $this->chuongModel->delete($_POST['machuong']);
            echo $result;
        }
    }

    public function addChapter()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $result = $this->chuongModel->insert($_POST['machude'], $_POST['tenchuong']);
            echo $result;
        }
    }

    public function updateChapter()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $result = $this->chuongModel->update($_POST['machuong'], $_POST['tenchuong']);
            echo $result;
        }
    }

    public function search()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $result = $this->chuDeModel->search($_POST['input']);
            echo json_encode($result);
        }
    }

    public function getQuery($filter, $input, $args)
    {
        $result = $this->chuDeModel->getQuery($filter, $input, $args);
        return $result;
    }
}
?>