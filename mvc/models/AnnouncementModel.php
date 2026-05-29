<?php
class AnnouncementModel extends DB 
{
    
    public function create($machude,$thoigiantao,$nguoitao,$phong,$content)
    {
        $sql = "INSERT INTO `thongbao`(`noidung`,`thoigiantao`,`nguoitao`) VALUES ('$content','$thoigiantao','$nguoitao')";
        $result = mysqli_query($this->con, $sql);
        if($result) {
            $matb = mysqli_insert_id($this->con);
            // Một thông báo gửi cho nhiều nhóm 
            $result = $this->sendAnnouncement($matb, $phong);
            return $matb;
        } else return false;
    }

    public function getById($matb) {
        $sql = "SELECT * FROM thongbao WHERE matb = '$matb'";
        $result = mysqli_query($this->con,$sql);
        return mysqli_fetch_assoc($result);
    }

    public function sendAnnouncement($matb,$phong)
    {
        $valid = true;
        foreach ($phong as $maphong) {
            $sql = "INSERT INTO `chitietthongbao`(`matb`, `maphong`) VALUES ('$matb','$maphong')";
            $result = mysqli_query($this->con, $sql);
            if (!$result) $valid = false;
        }
        return $valid;
    }

    public function getAnnounce($maphong) {
        $sql = "SELECT DISTINCT `thongbao`.`matb`, `noidung`, `avatar` ,`thoigiantao`
        FROM `thongbao`,`chitietthongbao`,`chitietphong`,`nguoidung` 
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`maphong` = `chitietphong`.`maphong` AND `nguoitao` = `id`
        AND `chitietthongbao`.`maphong` = $maphong ORDER BY thoigiantao DESC";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function getAll($user_id) 
    {
        $sql = "SELECT `chitietthongbao`.`matb`,`tenphong`,`noidung`, `tenchude`, `thoigiantao`
        FROM `thongbao`, `chitietthongbao`,`phong`,`chude` 
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`maphong` = `phong`.`maphong` AND `phong`.`machude` = `chude`.`machude`
        AND `thongbao`.`nguoitao` = $user_id ORDER BY thoigiantao DESC";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $matb = $row['matb'];
            $index = array_search($matb, array_column($rows, 'matb'));
            if ($index === false) {
                $item = [
                    "matb" => $matb,
                    "noidung" => $row['noidung'],
                    "tenchude" => $row['tenchude'],
                    "thoigiantao" => $row['thoigiantao'],
                    "phong" => [$row['tenphong']]
                ];
                array_push($rows, $item);
            } else {
                array_push($rows[$index]["phong"], $row['tenphong']);
            }
        }
        return $rows;
    }

    public function deleteAnnounce($matb)
    {  
        $result = $this->deleteDetailAnnounce($matb);
        if ($result) {
            $sql = "DELETE FROM `thongbao` WHERE `matb` = $matb";
            $result = mysqli_query($this->con,$sql);
            return true;
        } else return false;
    }


    // Xóa thông báo trong bảng thongbao
    public function deleteDetailAnnounce($matb)
    {
        $valid = true;
        $sql = "DELETE FROM `chitietthongbao` WHERE `matb` = $matb";
        $result = mysqli_query($this->con,$sql);
        if (!$result) $valid = false;
        return $valid; 
    }

    public function getDetail($matb)
    {
        $sql_announce = "SELECT `thongbao`.`matb`,`noidung`, `tenchude` 
        FROM `thongbao`, `chitietthongbao`,`phong`,`chude` 
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`maphong` = `phong`.`maphong` AND `phong`.`machude` = `chude`.`machude`
        AND `thongbao`.`matb` = $matb";
        $result_announce = mysqli_query($this->con,$sql_announce);
        $thongbao = mysqli_fetch_assoc($result_announce);
        if($thongbao != null) {
            $sql_sendAnnounce = "SELECT `maphong` FROM `chitietthongbao` WHERE `matb` = $matb";
            $result_sendAnnounce = mysqli_query($this->con,$sql_sendAnnounce);
            $thongbao['phong'] = array();
            while ($row = mysqli_fetch_assoc($result_sendAnnounce)) {
                $thongbao['phong'][] = $row['maphong'];
            } 
        }
        return $thongbao;
    }

    public function updateAnnounce($matb,$noidung,$phong)
    {
        $valid = true;
        $sql = "UPDATE `thongbao` SET `noidung`='$noidung' WHERE `matb` = $matb" ;
        $result = mysqli_query($this->con, $sql);
        if($result) {
            $this->deleteDetailAnnounce($matb);
            $this->sendAnnouncement($matb, $phong);
        } else $valid = false;
        return $valid; 
    }

    public function getNotifications($id)
    {
        $sql = "SELECT `tenphong`,`avatar`,`hoten`,`noidung`, `thoigiantao` ,`chitietphong`.`maphong` , chude.machude, chude.tenchude
        FROM `thongbao`,`chitietthongbao`,`chitietphong`, `nguoidung`,`phong` ,`chude`
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`maphong` = `chitietphong`.`maphong` 
        AND `thongbao`.`nguoitao` = `nguoidung`.`id` 
        AND `chitietphong`.`maphong` = `phong`.`maphong`
        AND `chude`.`machude` = `phong`.`machude`
        AND `chitietphong`.`manguoidung` = $id
        ORDER BY thoigiantao DESC LIMIT 0, 5";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function getQuery($filter, $input, $args) {
        $query = "SELECT TB.*, tenchude, GROUP_CONCAT(N.tenphong SEPARATOR ', ') AS phong FROM thongbao TB, chitietthongbao CTTB, phong N, chude MH WHERE TB.matb = CTTB.matb AND CTTB.maphong = N.maphong AND N.machude = MH.machude AND TB.nguoitao = ".$args['id'];
        if ($input) {
            $query .= " AND noidung LIKE N'%${input}%'";
        }
        $query .= " GROUP BY TB.matb ORDER BY thoigiantao DESC";
        return $query;
    }
}
?>