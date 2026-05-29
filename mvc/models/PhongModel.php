<?php
class PhongModel extends DB
{
    public function create($tenphong, $ghichu, $giangvien, $machude)
    {
        $valid = true;
        $sql = "INSERT INTO `phong`(`tenphong`, `ghichu`, `giangvien`, `machude`) VALUES ('$tenphong','$ghichu','$giangvien','$machude')";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }

    public function update($maphong, $tenphong, $ghichu, $machude)
    {
        $valid = true;
        $sql = "UPDATE `phong` SET `tenphong`='$tenphong',`ghichu`='$ghichu',`machude`='$machude' WHERE `maphong`='$maphong'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }

    public function delete($maphong)
    {
        $valid = true;
        $sql = "UPDATE `phong` SET `trangthai`='0' WHERE `maphong`='$maphong'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }

    // Ẩn || Hiện nhóm
    public function hide($maphong, $giatri)
    {
        $valid = true;
        $sql = "UPDATE `phong` SET `hienthi`=' $giatri' WHERE `maphong`='$maphong'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }

    public function sv_hide($maphong, $masv, $giatri)
    {
        $valid = true;
        $sql = "UPDATE `chitietphong` SET `hienthi`= '$giatri' WHERE `maphong`='$maphong' AND `manguoidung`='$masv'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getById($maphong)
    {
        $sql = "SELECT * FROM `phong` WHERE `maphong` = $maphong";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Lấy tất cả nhóm của người tạo và gom lại theo mã môn học, năm học, học kỳ
    public function getBySubject($nguoitao, $hienthi)
    {
        $sht = $hienthi == 2 ? "" : "AND phong.hienthi = $hienthi";
        $sql = "SELECT chude.machude, chude.tenchude, phong.maphong, phong.tenphong, phong.ghichu, phong.siso, phong.hienthi
        FROM phong, chude
        WHERE phong.machude = chude.machude AND phong.giangvien = '$nguoitao' AND phong.trangthai = 1 $sht";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        $newArray = [];
        foreach ($rows as $item) {
            $foundIndex = -1;
            foreach ($newArray as $key => $newItem) {
                if ($newItem["machude"] == $item["machude"]) {
                    $foundIndex = $key;
                    break;
                }
            }
            $detail_group = [
                "maphong" => $item["maphong"],
                "tenphong" => $item["tenphong"],
                "ghichu" => $item["ghichu"],
                "siso" => $item["siso"],
                "hienthi" => $item["hienthi"]
            ];
            if ($foundIndex == -1) {
                $newArray[] = [
                    "machude" => $item["machude"],
                    "tenchude" => $item["tenchude"],
                    "phong" => [$detail_group],
                ];
            } else {
                $newArray[$foundIndex]['phong'][] = $detail_group;
            }
        }
        return $newArray;
    }

    // Cập nhật mã mời
    public function updateInvitedCode($maphong)
    {
        $valid = true;
        $sql = "UPDATE `phong` SET `trangthai`='1' WHERE `maphong` = '$maphong'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    // Lấy mã mời 
    public function getInvitedCode($maphong)
    {
        $sql = "SELECT * FROM phong WHERE maphong = '$maphong'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Lấy mã nhóm từ mã mời
    public function getIdFromInvitedCode($maphong)
    {
        $sql = "SELECT `maphong` FROM `phong` WHERE `maphong` = '$maphong'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Thêm sinh viên vào nhóm
    public function join($maphong, $manguoidung)
    {
        $valid = true;
        $checkSql = "SELECT * FROM `chitietphong` WHERE `maphong` = '$maphong' AND `manguoidung` = '$manguoidung'";
        $checkResult = mysqli_query($this->con, $checkSql);

        if (mysqli_num_rows($checkResult) == 0) {
            $insertSql = "INSERT INTO `chitietphong`(`maphong`, `manguoidung`) VALUES ('$maphong','$manguoidung')";
            $insertResult = mysqli_query($this->con, $insertSql);

            if (!$insertResult) $valid = false;
        } else {
            $valid = false;
        }
        return $valid;
    }


    // Thoát khỏi nhóm 
    public function SVDelete($maphong, $manguoidung)
    {
        $valid = true;
        $sql = "DELETE FROM `chitietphong` WHERE `maphong` = '$maphong' AND `manguoidung` = '$manguoidung'";
        $result = mysqli_query($this->con, $sql);
        // $this->updateSiso($maphong);
        if (!$result) $valid = false;
        return $valid;
    }

    // Lấy các nhóm mà sinh viên tham gia
    public function getAllGroup_User($user_id, $hienthi)
    {
        $sql = "SELECT chude.machude,chude.tenchude,phong.maphong, phong.tenphong ,nguoidung.hoten, nguoidung.avatar,chitietphong.hienthi
        FROM chitietphong, phong, nguoidung, chude
        WHERE chitietphong.maphong = phong.maphong AND nguoidung.id = phong.giangvien AND chude.machude = phong.machude AND chitietphong.manguoidung = $user_id
        AND chitietphong.hienthi = $hienthi AND phong.trangthai != 0";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy chi tiết một nhóm mà sinh viên tham gia
    public function getDetailGroup($maphong)
    {
        $sql = "SELECT chude.machude,chude.tenchude,phong.maphong, phong.tenphong, phong.giangvien, nguoidung.hoten, nguoidung.avatar
        FROM phong, nguoidung, chude
        WHERE nguoidung.id = phong.giangvien AND chude.machude = phong.machude AND phong.maphong = $maphong";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Lấy danh sách bạn học chung nhóm
    public function getSvList($maphong)
    {
        $sql = "SELECT id, avatar, hoten, email, gioitinh, ngaysinh FROM chitietphong, nguoidung WHERE manguoidung = id AND chitietphong.maphong = $maphong";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // hàm update sỉ số sinh viên trong nhóm
    public function updateSiso($maphong)
    {
        $valid = true;
        $sql = "UPDATE `phong` SET `siso`= (SELECT count(*) FROM `chitietphong` where maphong = $maphong ) WHERE `maphong` = $maphong";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }

    // Hàm cập nhật sỉ số khi sv tham gia bằng mã mời
    public function updateSiso1($maphong)
    {
        $valid = $this->updateSiso($maphong);
        return $valid;
    }

    // Hàm lấy sinh viên ra từ nhóm
    public function getStudentByGroup($group)
    {
        $sql = "SELECT ng.id,ng.hoten,ng.email,ng.ngaythamgia,ng.ngaysinh,ng.gioitinh FROM chitietphong ctn JOIN nguoidung ng ON ctn.manguoidung=ng.id WHERE ctn.maphong = $group";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function kickUser($maphong, $mssv)
    {
        $valid = true;
        $sql = "DELETE FROM `chitietphong` WHERE `manguoidung` = $mssv AND `maphong` = $maphong";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function addSV($mssv, $hoten, $password)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `nguoidung`(`id`,`hoten`,`matkhau`,`trangthai`, `manhomquyen`) VALUES ('$mssv','$hoten','$password','1', '11')";
        $check = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $check = false;
        }
        return $check;
    }

    public function checkAcc($mssv, $maphong)
    {
        $sql_checkGroup = "SELECT * FROM chitietphong where maphong='$maphong' AND manguoidung='$mssv'";
        $result_checkGroup = mysqli_query($this->con, $sql_checkGroup);
        if ($result_checkGroup->num_rows > 0) {
            return "0";
        }

        $sql_checkNguoiDung = "SELECT * FROM nguoidung where id='$mssv'";
        $result_checkNguoiDung = mysqli_query($this->con, $sql_checkNguoiDung);
        if ($result_checkNguoiDung->num_rows > 0) {
            return "-1";
        }
        return "1";
    }

    public function getGroupSize($id)
    {
        // $sql = "SELECT count(*) FROM chitietphong WHERE maphong = $id";
        $sql = "SELECT siso from phong where maphong = $id";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        // return $row['count(*)'];
        return $row['siso'];
    }

    public function getQuerySortByName($filter, $input, $args, $order)
    {
        $query = "SELECT ND.id, avatar, hoten, email, gioitinh, ngaysinh, SUBSTRING_INDEX(hoten, ' ', -1) AS firstname FROM chitietphong CTN, nguoidung ND WHERE CTN.manguoidung = ND.id AND CTN.maphong = " . $args['maphong'];
        if ($input) {
            $query .= " AND (ND.hoten LIKE N'%${input}%' OR CTN.manguoidung LIKE N'%${input}%')";
        }
        $query .= " ORDER BY firstname $order";
        return $query;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT ND.id, avatar, hoten, email, gioitinh, ngaysinh FROM chitietphong CTN, nguoidung ND WHERE CTN.manguoidung = ND.id AND CTN.maphong = " . $args['maphong'];
        if ($input) {
            $query .= " AND (ND.hoten LIKE N'%${input}%' OR CTN.manguoidung LIKE N'%${input}%')";
        }
        if (isset($args["custom"]["function"])) {
            $function = $args["custom"]["function"];
            switch ($function) {
                case "sort":
                    $column = $args["custom"]["column"];
                    $order = $args["custom"]["order"];
                    switch ($column) {
                        case "id":
                            $query .= " ORDER BY $column $order";
                            break;
                        case "hoten":
                            $query = $this->getQuerySortByName($filter, $input, $args, $order);
                            break;
                        default:
                    }
                    break;
                default:
            }
        }
        return $query;
    }
}
