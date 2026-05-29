<?php
// include "./mvc/models/CauTraLoiModel.php";
require_once "./mvc/models/CauTraLoiModel.php";
class DeThiModel extends DB
{
    public function create($machude, $nguoitao, $tende, $thoigianthi, $thoigianbatdau, $thoigianketthuc, $hienthibailam, $xemdiemthi, $xemdapan, $troncauhoi, $trondapan, $nopbaichuyentab, $loaide, $socaude, $socautb, $socaukho, $chuong, $phong)
    {
        $sql = "INSERT INTO `dethi`(`machude`, `nguoitao`, `tende`, `thoigianthi`, `thoigianbatdau`, `thoigianketthuc`, `hienthibailam`, `xemdiemthi`, `xemdapan`, `troncauhoi`, `trondapan`, `nopbaichuyentab`, `loaide`, `socaude`, `socautb`, `socaukho`) VALUES ('$machude','$nguoitao','$tende','$thoigianthi','$thoigianbatdau','$thoigianketthuc','$hienthibailam','$xemdiemthi','$xemdapan','$troncauhoi','$trondapan','$nopbaichuyentab','$loaide','$socaude','$socautb','$socaukho')";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $madethi = mysqli_insert_id($this->con);
            // Một đề thi giao cho nhiều nhóm
            $result = $this->create_giaodethi($madethi, $phong);
            // Một đề thi thì có nhiều chương
            $result = $this->create_chuongdethi($madethi, $chuong);
            return $madethi;
        } else return false;
    }

    // 
    public function create_dethi_auto($made, $chude, $chuong, $socaude, $socautb, $socaukho)
    {
        $valid = true;
        $sql_caude = "SELECT * FROM cauhoi ch join chude mh on ch.machude = mh.machude where ch.machude = $chude and ch.dokho = 1 and ";
        $sql_cautb = "SELECT * FROM cauhoi ch join chude mh on ch.machude = mh.machude where ch.machude = $chude and ch.dokho = 2 and ";
        $sql_caukho = "SELECT * FROM cauhoi ch join chude mh on ch.machude = mh.machude where ch.machude = $chude and ch.dokho = 3 and ";
        $countChuong = count($chuong) - 1;
        $detailChuong = "(";
        $i = 0;
        while ($i < $countChuong) {
            $detailChuong .= "ch.machuong='$chuong[$i]' or ";
            $i++;
        }
        $detailChuong .= "ch.machuong=$chuong[$countChuong])";

        $sql_caude = $sql_caude . $detailChuong . " order by rand() limit $socaude";
        $sql_cautb = $sql_cautb . $detailChuong . " order by rand() limit $socautb";
        $sql_caukho = $sql_caukho . $detailChuong . " order by rand() limit $socaukho";

        $result_cd = mysqli_query($this->con, $sql_caude);
        $result_tb = mysqli_query($this->con, $sql_cautb);
        $result_ck = mysqli_query($this->con, $sql_caukho);

        $data_cd = array();

        while ($row = mysqli_fetch_assoc($result_cd)) {
            $data_cd[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_tb)) {
            $data_cd[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_ck)) {
            $data_cd[] = $row;
        }
        shuffle($data_cd);
        return $data_cd;
    }

    public function create_chuongdethi($made, $chuong)
    {
        $valid = true;
        foreach ($chuong as $machuong) {
            $sql = "INSERT INTO `dethitudong`(`made`, `machuong`) VALUES ('$made','$machuong')";
            $result = mysqli_query($this->con, $sql);
            if (!$result) $valid = false;
        }
        return $valid;
    }

    public function update_chuongdethi($made, $chuong)
    {
        $valid = true;
        $sql = "DELETE FROM `dethitudong` WHERE `made`='$made'";
        $result_del = mysqli_query($this->con, $sql);
        if ($result_del) $result_update = $this->create_chuongdethi($made, $chuong);
        else $valid = false;
        return $valid;
    }

    public function create_giaodethi($made, $phong)
    {
        $valid = true;
        foreach ($phong as $maphong) {
            $sql = "INSERT INTO `giaodethi`(`made`, `maphong`) VALUES ('$made','$maphong')";
            $result = mysqli_query($this->con, $sql);
            if (!$result) $valid = false;
        }
        return $valid;
    }

    public function update_giaodethi($made, $phong)
    {
        $valid = true;
        $sql = "DELETE FROM `giaodethi` WHERE `made`='$made'";
        $result_del = mysqli_query($this->con, $sql);
        if ($result_del) $result_update = $this->create_giaodethi($made, $phong);
        else $valid = false;
        return $valid;
    }

    public function update($made, $machude, $tende, $thoigianthi, $thoigianbatdau, $thoigianketthuc, $hienthibailam, $xemdiemthi, $xemdapan, $troncauhoi, $trondapan, $nopbaichuyentab, $loaide, $socaude, $socautb, $socaukho, $chuong, $phong)
    {
        $valid = true;
        $sql = "UPDATE `dethi` SET `machude`='$machude',`tende`='$tende',`thoigianthi`='$thoigianthi',`thoigianbatdau`='$thoigianbatdau',`thoigianketthuc`='$thoigianketthuc',`hienthibailam`='$hienthibailam',`xemdiemthi`='$xemdiemthi',`xemdapan`='$xemdapan',`troncauhoi`='$troncauhoi',`trondapan`='$trondapan',`nopbaichuyentab`='$nopbaichuyentab',`loaide`='$loaide',`socaude`='$socaude',`socautb`='$socautb',`socaukho`='$socaukho' WHERE `made`='$made'";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            // Một đề thi giao cho nhiều nhóm
            $result = $this->update_giaodethi($made, $phong);
            // Một đề thi thì có nhiều chương
            $result = $this->update_chuongdethi($made, $chuong);
        } else $valid = false;
        return $valid;
    }

    public function delete($madethi)
    {
        $valid = true;
        $sql = "UPDATE `dethi` SET `trangthai`= 0 WHERE `made` = $madethi";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    // Lấy đề thi mà người dùng tạo
    public function getAll($nguoitao)
    {
        $sql = "SELECT dethi.made, tende, chude.tenchude, thoigianbatdau, thoigianketthuc, phong.tenphong
        FROM dethi, chude, giaodethi, phong
        WHERE dethi.machude = chude.machude AND dethi.made = giaodethi.made AND phong.maphong = giaodethi.maphong AND nguoitao = $nguoitao AND dethi.trangthai = 1
        ORDER BY dethi.made DESC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $made = $row['made'];
            $index = array_search($made, array_column($rows, 'made'));
            if ($index === false) {
                $item = [
                    "made" => $made,
                    "tende" => $row['tende'],
                    "thoigianbatdau" => date_format(date_create($row['thoigianbatdau']), "H:i d/m/Y"),
                    "thoigianketthuc" => date_format(date_create($row['thoigianketthuc']), "H:i d/m/Y"),
                    "tenchude" => $row['tenchude'],
                    "phong" => [$row['tenphong']]
                ];
                array_push($rows, $item);
            } else {
                array_push($rows[$index]["phong"], $row['tenphong']);
            }
        }
        return $rows;
    }

    // Lấy chi tiết đề thi
    public function getById($made)
    {
        $sql_dethi = "SELECT dethi.*, chude.tenchude FROM dethi, chude WHERE made = $made AND dethi.machude = chude.machude";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $dethi = mysqli_fetch_assoc($result_dethi);
        if ($dethi != null) {
            $sql_giaodethi = "SELECT maphong FROM giaodethi WHERE made = $made";
            $sql_dethitudong = "SELECT machuong FROM dethitudong WHERE made = $made";
            $result_giaodethi = mysqli_query($this->con, $sql_giaodethi);
            $result_dethitudong = mysqli_query($this->con, $sql_dethitudong);
            $dethi['chuong'] = array();
            while ($row = mysqli_fetch_assoc($result_dethitudong)) {
                $dethi['chuong'][] = $row['machuong'];
            }
            $dethi['phong'] = array();
            while ($row = mysqli_fetch_assoc($result_giaodethi)) {
                $dethi['phong'][] = $row['maphong'];
            }
        }
        return $dethi;
    }

    // Lấy thông tin cơ bản của đề thi ()
    public function getInfoTestBasic($made)
    {
        $sql_dethi = "SELECT dethi.made, dethi.tende, dethi.thoigiantao,dethi.loaide,dethi.nguoitao,chude.machude, chude.tenchude FROM dethi, chude WHERE made = $made AND dethi.machude = chude.machude";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $dethi = mysqli_fetch_assoc($result_dethi);
        if ($dethi != null) {
            $sql_giaodethi = "SELECT giaodethi.maphong, phong.tenphong FROM giaodethi, phong WHERE made = $made AND giaodethi.maphong = phong.maphong";
            $result_giaodethi = mysqli_query($this->con, $sql_giaodethi);
            $dethi['phong'] = array();
            while ($row = mysqli_fetch_assoc($result_giaodethi)) {
                $dethi['phong'][] = $row;
            }
        }
        return $dethi;
    }

    // Lấy đề thi của nhóm học phần
    public function getListTestGroup($maphong)
    {
        $sql = "SELECT dethi.made, dethi.tende, dethi.thoigianbatdau, dethi.thoigianketthuc
        FROM giaodethi, dethi
        WHERE maphong = '$maphong' AND giaodethi.made = dethi.made ORDER BY dethi.made DESC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['thoigianbatdau'] = date_format(date_create($row['thoigianbatdau']), "H:i d/m/Y");
            $row['thoigianketthuc'] = date_format(date_create($row['thoigianketthuc']), "H:i d/m/Y");
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy câu hỏi của đề thi
    public function getQuestionOfTest($made)
    {
        $sql_dethi = "select * from dethi where made = '$made'";
        $data_dethi = mysqli_fetch_assoc(mysqli_query($this->con, $sql_dethi));
        $question = array();
        if ($data_dethi['loaide'] == 0) {
            $question = $this->getQuestionOfTestManual($made);
        } else {
            $question = $this->getQuestionTestAuto($made);
        }
        $makq = $this->getMaDe($made,$_SESSION['user_id']);
        foreach ($question as $data) {
            $macauhoi = $data['macauhoi'];
            $sql = "INSERT INTO `chitietketqua`(`makq`, `macauhoi`) VALUES ('$makq','$macauhoi')";
            $addCtKq = mysqli_query($this->con,$sql);
        }

        return $question;
    }

    public function getQuestionByUser($made,$user){
        $sql_ketqua = "SELECT * FROM ketqua where made = '$made' and manguoidung = '$user'";
        $result_ketqua = mysqli_query($this->con,$sql_ketqua);
        $data_ketqua = mysqli_fetch_assoc($result_ketqua);
        $ketqua = $data_ketqua['makq'];
        $sql_question = "SELECT * FROM chitietketqua ctkq JOIN cauhoi ch on ctkq.macauhoi = ch.macauhoi WHERE makq = '$ketqua'";
        $data_question = mysqli_query($this->con,$sql_question);
        $ctlmodel = new CauTraLoiModel();
        $sql_dethi = "SELECT * FROM dethi where made='$made'";
        $result_dethi = mysqli_query($this->con,$sql_dethi);
        $data_dethi = mysqli_fetch_assoc($result_dethi);
        $trondapan = $data_dethi['trondapan'];
        $rows = array();
        foreach ($data_question as $row) {
            if($trondapan==1){
                $arrDapAn = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
                shuffle($arrDapAn);
                $row['cautraloi']= $arrDapAn;
                
            } else {
                $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            }
            $rows[] = $row;
        }
        $troncauhoi = $data_dethi['troncauhoi'];
        if($troncauhoi==1){
            shuffle($rows);
        }
        return $rows;
    }

    public function getMaDe($made, $user){
        $sql = "SELECT * FROM `ketqua` WHERE made = '$made' and manguoidung = '$user'";
        $result = mysqli_query($this->con,$sql);
        $data = mysqli_fetch_assoc($result);
        return $data['makq'];
    }


    public function getQuestionTestAuto($made)
    {
        $sql_dethi = "select * from dethi where made = '$made'";
        $data_dethi = mysqli_fetch_assoc(mysqli_query($this->con, $sql_dethi));
        $socaude = $data_dethi['socaude'];
        $socautb = $data_dethi['socautb'];
        $socaukho = $data_dethi['socaukho'];
        $sql_cd = "select ch.macauhoi,ch.noidung,ch.dokho from dethitudong dttd join cauhoi ch on dttd.machuong=ch.machuong where ch.dokho = 1 and dttd.made = '$made' order by rand() limit $socaude";
        $sql_ctb = "select ch.macauhoi,ch.noidung,ch.dokho from dethitudong dttd join cauhoi ch on dttd.machuong=ch.machuong where ch.dokho = 2 and dttd.made = '$made' order by rand() limit $socautb";
        $sql_ck = "select ch.macauhoi,ch.noidung,ch.dokho from dethitudong dttd join cauhoi ch on dttd.machuong=ch.machuong where ch.dokho = 3 and dttd.made = '$made' order by rand() limit $socaukho";
        $result_cd = mysqli_query($this->con, $sql_cd);
        $result_tb = mysqli_query($this->con, $sql_ctb);
        $result_ck = mysqli_query($this->con, $sql_ck);
        $result = array();
        while ($row = mysqli_fetch_assoc($result_cd)) {
            $result[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_tb)) {
            $result[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_ck)) {
            $result[] = $row;
        }
        shuffle($result);
        $rows = array();

        $ctlmodel = new CauTraLoiModel();

        foreach ($result as $row) {
            $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function getNameGroup($maphong){
        $sql = "SELECT * FROM `phong` WHERE maphong=$maphong";
        $result = mysqli_query($this->con,$sql);
        $nameGroup = mysqli_fetch_assoc($result)['tenphong'];
        return $nameGroup;
    }

    // Tạo đề thủ công
    public function getQuestionOfTestManual($made)
    {
        $sql = "SELECT CTDT.macauhoi, noidung, dokho, thutu FROM chitietdethi CTDT, cauhoi CH WHERE CTDT.macauhoi = CH.macauhoi AND CTDT.made = $made ORDER BY thutu ASC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        $ctlmodel = new CauTraLoiModel();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy chi tiết đề thi của sinh viên
    public function getResultDetail($makq)
    {
        $sql = "SELECT cauhoi.macauhoi,cauhoi.noidung,cauhoi.dokho,chitietketqua.dapanchon FROM chitietketqua, cauhoi WHERE makq= '$makq' AND chitietketqua.macauhoi = cauhoi.macauhoi";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        $ctlmodel = new CauTraLoiModel();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['cautraloi'] = $ctlmodel->getAll($row['macauhoi']);
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy thời gian kết thúc đề thi
    public function getTimeTest($dethi, $nguoidung)
    {
        $sql = "Select * from ketqua where made = '$dethi' and manguoidung = '$nguoidung'";
        $sql_dethi = "select * from dethi where made = '$dethi'";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            $data_dethi = mysqli_fetch_assoc($result_dethi);
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $thoigianketthuc = date("Y-m-d H:i:s", strtotime($data['thoigianvaothi']) + ($data_dethi['thoigianthi'] * 60));
            return $thoigianketthuc;
        }
        return false;
    }

    public function getTimeEndTest($dethi){
        $sql_dethi = "select * from dethi where made = '$dethi'";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $data_dethi = mysqli_fetch_assoc($result_dethi);
        $thoigianketthuc = date("Y-m-d H:i:s", strtotime($data_dethi['thoigianketthuc']));
        return $thoigianketthuc;
    }

    public function getGroupsTakeTests($tests) {
        $string = implode(', ', $tests);
            $sql = "SELECT GDT.*, tenphong FROM giaodethi GDT, phong N WHERE GDT.maphong = N.maphong AND made IN ($string)";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkStudentAllowed($manguoidung, $madethi)
    {
        $valid = true;
        $sql = "SELECT *
        FROM giaodethi, chitietphong
        WHERE giaodethi.made = '$madethi' AND giaodethi.maphong = chitietphong.maphong AND chitietphong.manguoidung = '$manguoidung'";
        $result = mysqli_query($this->con,$sql);
        if(!mysqli_fetch_assoc($result)) $valid = false;
        return $valid;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "";
        if (isset($args["custom"]["function"])) {
            $func = $args["custom"]["function"];
            switch ($func) {
                case "getUserTestSchedule":
                    // Lấy danh sách lịch thi đã được giao của người dùng
                        $query = "SELECT T1.*, diemthi FROM (SELECT DT.made, tende, thoigianbatdau, thoigianketthuc, CTN.maphong, tenphong, tenchude FROM chitietphong CTN, giaodethi GDT, dethi DT, chude MH, phong N WHERE N.trangthai != 0 AND N.maphong = CTN.maphong AND CTN.maphong = GDT.maphong AND DT.made = GDT.made AND MH.machude = DT.machude AND DT.trangthai = 1 AND manguoidung = '" . $args['manguoidung'] . "') T1 LEFT JOIN (SELECT DISTINCT DT.made, diemthi FROM chitietphong CTN, giaodethi GDT, dethi DT, chude MH, phong N, ketqua KQ WHERE N.maphong = CTN.maphong AND CTN.maphong = GDT.maphong AND DT.made = GDT.made AND MH.machude = DT.machude AND KQ.made = DT.made AND DT.trangthai = 1 AND KQ.manguoidung = '" . $args['manguoidung'] . "') T2 ON T1.made = T2.made WHERE 1";
                    if (isset($filter)) {
                        switch ($filter) {
                            case "0";
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc AND diemthi IS NULL";
                                break;
                            case "1";
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc AND diemthi IS NULL";
                                break;
                            case "2";
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "3";
                                $query .= " AND diemthi IS NOT NULL";
                                break;
                            default:
                        }
                    }
                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenchude LIKE N'%$input%')";
                    }
                    $query .= " ORDER BY made DESC";
                    break;
                case "getAllCreatedTest":
                    // Lấy danh sách các đề thi đã tạo của giảng viên
                    $query = "SELECT DT.made, tende, tenchude, thoigianbatdau, thoigianketthuc, GROUP_CONCAT(N.tenphong SEPARATOR ', ') AS phong FROM dethi DT, chude MH, giaodethi GDT, phong N WHERE DT.machude = MH.machude AND DT.made = GDT.made AND N.maphong = GDT.maphong AND nguoitao = '".$args['id']."' AND DT.trangthai = 1";
                    if (isset($filter)) {
                        switch ($filter) {
                            case "0";
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "1";
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc";
                                break;
                            case "2";
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc";
                                break;
                            default:
                        }
                    }
                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenchude LIKE N'%$input%')";
                    }
                    $query .= " GROUP BY DT.made ORDER BY DT.made DESC";
                    break;
                case "getQuestionsForTest":
                    $query = "SELECT cauhoi.*, fnStripTags(noidung) AS noidungplaintext FROM cauhoi,phanquyen WHERE cauhoi.machude = phanquyen.machude AND trangthai = 1 AND phanquyen.manguoidung = ".$args['id']." AND cauhoi.machude = ".$args['machude'];
                    if (isset($filter['machuong'])) {
                        $query .= " AND machuong = ".$filter['machuong'];
                    }
                    if (isset($filter['dokho'])) {
                        $query .= " AND dokho = ".$filter['dokho'];
                    }
                    if ($input) {
                        $input_entity_encode = htmlentities($input);
                        $query .= " AND (noidung LIKE N'%${input}%' OR fnStripTags(noidung) LIKE N'%${input_entity_encode}%')";
                    }
                    break;
                default:
            }
        }
        return $query;
    }

    public function getTestsGroupWithUserResult($maphong, $manguoidung)
    {
        $sql = "SELECT T1.*, diemthi FROM (SELECT DT.made, tende, thoigianbatdau, thoigianketthuc FROM dethi DT, giaodethi GDT WHERE DT.made = GDT.made AND DT.trangthai = 1 AND maphong = $maphong) T1 LEFT JOIN (SELECT KQ.made, diemthi FROM ketqua KQ, giaodethi GDT WHERE KQ.made = GDT.made AND manguoidung = '$manguoidung' AND GDT.maphong = $maphong) T2 ON T1.made = T2.made ORDER BY made DESC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}