<?php
class PhanCongModel extends DB{

    public function getGiangVien(){
        $sql = "SELECT ng.id,ng.manhomquyen,ng.hoten FROM nguoidung ng join chitietquyen ctq on ng.manhomquyen = ctq.manhomquyen where ctq.chucnang = 'cauhoi' OR ctq.chucnang = 'chude' OR ctq.chucnang='hocphan' OR ctq.chucnang = 'chuong' GROUP BY ng.id";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function getMonHoc(){
        $sql = "SELECT * FROM `chude`";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function addAssignment($giangvien,$listSubject){
        $check = true;
        $sql = "INSERT INTO `phanquyen`(`machude`, `manguoidung`) VALUES ";
        foreach($listSubject as $key => $machude){
            $sql .= "('$machude','$giangvien')";
            if ($key != count($listSubject) - 1) {
                $sql .= ", ";
            }
        }
        $result = mysqli_query($this->con,$sql);
        if($result){
        } else {
            $check = false;
        }
        return $check;
    }

    public function getAssignment(){
        $sql = "SELECT pc.machude, pc.manguoidung, ng.hoten, mh.tenchude FROM phanquyen as pc JOIN chude as mh on pc.machude=mh.machude JOIN nguoidung as ng on pc.manguoidung=ng.id";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function delete($mamon,$id){
        $sql = "DELETE FROM `phanquyen` WHERE machude = '$mamon' and manguoidung = '$id'";
        $result = mysqli_query($this->con,$sql);
        return $result;
    }

    public function deleteAll($id){
        $sql = "DELETE FROM `phanquyen` WHERE manguoidung = '$id'";
        $result = mysqli_query($this->con,$sql);
        return $result;
    }

    public function getAssignmentByUser($user){
        // $sql = "SELECT * FROM `phanquyen` where manguoidung = '$user'";
        $sql = "SELECT machude FROM `phanquyen` where manguoidung = '$user'";
        $result = mysqli_query($this->con,$sql);
        $num_rows = mysqli_num_rows($result);
        if ($num_rows == 0) {
            return [];
        }
        $row = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function getQuery($filter, $input, $args) {
        if (isset($args["custom"]["function"])) {
            $func = $args["custom"]["function"];
            switch ($func) {
                case "chude":
                    $query = "SELECT * FROM `chude` WHERE trangthai = 1";
                    if ($input) {
                        $query .= " AND (chude.tenchude LIKE N'%${input}%' OR chude.machude LIKE '%${input}%')";
                    }
                    return $query;
                    break;
                default:
            }
        }
        $query = "SELECT pc.machude, pc.manguoidung, ng.hoten, mh.tenchude FROM phanquyen as pc JOIN chude as mh on pc.machude=mh.machude JOIN nguoidung as ng on pc.manguoidung=ng.id";
        if ($input) {
            $query .= " AND (mh.tenchude LIKE N'%${input}%' OR ng.hoten LIKE '%${input}%')";
        }
        return $query;
    }
}
?>