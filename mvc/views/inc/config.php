<?php
$GLOBALS['navbar'] = [
    array(
        'name'  => 'Dashboard',
        'icon'  => 'fa fa-rocket',
        'url'   => 'dashboard'
    ),
    array(
        'name'  => 'Nhân viên',
        'type'  => 'heading',
        'navbarItem' => [
            array(
                'name'  => 'Phòng ban',
                'icon'  => 'fa fa-users-line',
                'url'   => 'client/group',
                'role' => 'tghocphan' // Giữ nguyên role cũ để không vỡ JS, chỉ đổi tên hiển thị
            ),
            array(
                'name'  => 'Bài kiểm tra',
                'icon'  => 'fa fa-graduation-cap',
                'url'   => 'client/test',
                'role' => 'tgthi'
            ),
        ]
            ),
    array(
        'name'  => 'Quản lý',
        'type'  => 'heading',
        'navbarItem' => [
            array(
                'name'  => 'Phòng ban',
                'icon'  => 'fa fa-layer-group',
                'url'   => 'phong', // Đã đổi url
                'role' => 'phong'   // Đã đổi role
            ),
            array(
                'name'  => 'Câu hỏi',
                'icon'  => 'fa fa-circle-question',
                'url'   => 'question',
                'role' => 'cauhoi'
            ),
            array(
                'name'  => 'Người dùng',
                'icon'  => 'fa fa-user-friends',
                'url'   => 'user',
                'role' => 'nguoidung'
            ),
            array(
                'name'  => 'Chủ đề',
                'icon'  => 'fa fa-folder',
                'url'   => 'chude', // Đã đổi url
                'role' => 'chude'   // Đã đổi role
            ),
            array(
                'name'  => 'Phân quyền',
                'icon'  => 'fa fa-person-harassing',
                'url'   => 'assignment',
                'role' => 'phanquyen' // Đã đổi role
            ),
            array(
                'name'  => 'Đề kiểm tra',
                'icon'  => 'fa fa-file',
                'url'   => 'test',
                'role' => 'dethi'
            ),
            array(
                'name'  => 'Thông báo',
                'icon'  => 'fa fa-comment',
                'url'   => 'teacher_announcement',
                'role' => 'thongbao'
            ),
        ]
    ),
    array(
        'name'  => 'Quản trị',
        'type'  => 'heading',
        'navbarItem' => [
            array(
                'name'  => 'Nhóm quyền',
                'icon'  => 'fa fa-users-gear',
                'url'   => 'roles',
                'role' => 'nhomquyen'
            )
        ]
    )
];

// Xử lý url để active navbar
function getActiveNav() {
    $directoryURI = $_SERVER['REQUEST_URI'];
    $path = parse_url($directoryURI, PHP_URL_PATH);
    $components = explode('/',$path);
    return $components[2];
}

function build_navbar() {
    // Loại bỏ các navbar item không có trong session nhóm quyền
    foreach($GLOBALS['navbar'] as $key => $nav) {
        if(isset($nav['navbarItem'])) {
            foreach ($nav['navbarItem'] as $key1 => $navItem) {
                if(!array_key_exists($navItem['role'],$_SESSION['user_role'])) {
                    unset($GLOBALS['navbar'][$key]['navbarItem'][$key1]);
                }
            }
        }
    }
    
    // Sau khi xoá các navbar item không có trong session nhóm quyền thì duyệt mảng tạo navbar
    $html = '';
    $current_page = getActiveNav();
    foreach($GLOBALS['navbar'] as $nav) {
        if(isset($nav['navbarItem']) && isset($nav['type']) && count($nav['navbarItem']) > 0) {
            $html .= "<li class=\"nav-main-heading\">".$nav['name']."</li>";
            foreach ($nav['navbarItem'] as $navItem) {
                $link_name = '<span class="nav-main-link-name">' . $navItem['name'] . '</span>' . "\n";
                $link_icon = '<i class="nav-main-link-icon ' . $navItem['icon'] . '"></i>' . "\n";
                $html .= "<li class=\"nav-main-item\">"."\n";
                $html .= "<a class=\"nav-main-link".($current_page == $navItem['url'] ? " active" : "")."\" href=\"./".$navItem['url']."\">";
                $html .= $link_icon;
                $html .= $link_name;
                $html .= "</a></li>\n";
            }
        }
    }
    echo $html;
}
?>