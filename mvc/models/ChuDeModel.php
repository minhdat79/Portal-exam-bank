<?php
class ChuDeModel extends DB
{
    public function create($mamon, $tenmon, $sotinchi, $sotietlythuyet, $sotietthuchanh)
    {
        $valid = true;
        $sql = "INSERT INTO `chude`(`machude`, `tenchude`, `sotinchi`, `sotietlythuyet`, `sotietthuchanh`, `trangthai`) VALUES ('$mamon','$tenmon','$sotinchi','$sotietlythuyet','$sotietthuchanh', 1)";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function update($id, $mamon, $tenmon, $sotinchi, $sotietlythuyet, $sotietthuchanh)
    {
        $valid = true;
        $sql = "UPDATE `chude` SET `machude`='$mamon',`tenchude`='$tenmon',`sotinchi`='$sotinchi',`sotietlythuyet`='$sotietlythuyet',`sotietthuchanh`='$sotietthuchanh' WHERE `machude`='$id'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function delete($mamon)
    {
        $valid = true;
        $sql = "UPDATE `chude` SET `trangthai`= 0 WHERE `machude`='$mamon'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM `chude` WHERE `trangthai` = 1";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `chude` WHERE `machude` = '$id'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function search($input)
    {
        $sql = "SELECT * FROM `chude` WHERE `machude` LIKE '%$input%' OR `tenchude` LIKE N'%$input%';";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getAllSubjectAssignment($userid)
    {
        $sql = "SELECT chude.* FROM phanquyen, chude WHERE manguoidung = '$userid' AND chude.machude = phanquyen.machude AND chude.trangthai = 1";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT * FROM `chude` WHERE `trangthai` = 1";
        if ($input) {
            $query = $query . " AND (`chude`.`tenchude` LIKE N'%${input}%' OR `chude`.`machude` LIKE '%${input}%')";
        }
        return $query;
    }

    public function checkSubject($mamon)
    {
        $sql = "SELECT * FROM `chude` WHERE `machude` = $mamon";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
