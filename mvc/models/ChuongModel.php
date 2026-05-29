<?php
class ChuongModel extends DB {
    public function getAll($machude)
    {
        $sql = "SELECT * FROM `chuong` WHERE machude = '$machude' AND `trangthai` = 1";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    public function insert($machude,$tenchuong) 
    {
        $valid = true;
        $sql = "INSERT INTO `chuong`(`machude`,`tenchuong`,`trangthai`) VALUES ('$machude','$tenchuong',1)";
        $result = mysqli_query($this->con,$sql);
        if(!$result) $valid = false;
        return $valid;
    }   

    public function delete($machuong)
    {
        $valid = true;
        $sql = "UPDATE `chuong` SET `trangthai`= 0 WHERE `machuong` = '$machuong'";
        $result = mysqli_query($this->con, $sql);
        if(!$result) $valid = false;
        return $valid;
    }

    public function update($machuong,$tenchuong) 
    {
        $valid = true;
        $sql = "UPDATE `chuong` SET `tenchuong`='$tenchuong' WHERE `machuong` = '$machuong'";
        $result = mysqli_query($this->con,$sql);
        if(!$result) $valid = false;
        return $valid;
    } 
}