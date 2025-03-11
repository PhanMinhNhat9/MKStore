<?php
include "config.php";

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem admin đã đăng nhập chưa
if (!isset($_SESSION['user']) || $_SESSION['user']['quyen'] != 0) {
    header("Location: GUI&dangnhap.php");
    exit();
}

define('SESSION_TIMEOUT', 1800);

if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    error_log("Session inactive time: $inactive_time seconds"); // Debug log
    
    if ($inactive_time > SESSION_TIMEOUT) {
        // Hủy session và chuyển hướng đến trang đăng nhập
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/'); // Xóa cookie session
        echo "<script>window.location.href = 'GUI&dangnhap.php?timeout=1';</script>";
        exit();
    }
}
// Cập nhật lại thời gian hoạt động cuối cùng
$_SESSION['last_activity'] = time();
// Làm mới session ID để tăng cường bảo mật (kiểm tra session trước khi gọi)
if (session_status() == PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

session_write_close(); // Đảm bảo session được ghi lại ngay lập tức

// Lấy thông tin admin từ session
$admin_name = htmlspecialchars($_SESSION['user']['hoten'], ENT_QUOTES, 'UTF-8');
$admin_email = htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8');
$admin_phone = htmlspecialchars($_SESSION['user']['sdt'], ENT_QUOTES, 'UTF-8');
$admin_avatar = !empty($_SESSION['user']['anh']) ? htmlspecialchars($_SESSION['user']['anh'], ENT_QUOTES, 'UTF-8') : "https://i.pravatar.cc/100";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="trangchuadmin.css">
    <script src="trangchuadmin.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            width: 200px;
            border-radius: 8px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            padding: 10px;
            z-index: 10;
            margin-top: 10px ;
        }
        .dropdown.active {
            display: flex;
        }
        .dropdown .profile {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .dropdown .profile img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 5px;
        }
        .dropdown .profile p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
        }
        .dropdown .logout {
            background:rgb(40, 73, 218);
            color: white;
            padding: 8px;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .dropdown .logout:hover {
            background:rgb(19, 10, 146);
        }
    </style>
</head>
<body>
    <!-- Thanh navbar -->
    <nav class="navbar">
        <div class="logo-container">
            <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="logo">
            <span class="store-name"> M'K STORE</span>
        </div>
        
        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Tìm kiếm...">
            <button class="mic-btn"><i class="fas fa-microphone"></i></button>
            <button class="search-btn"> Tìm kiếm</button>
        </div>
        
        <div class="nav-buttons">
            <button class="btn trangchu" onclick="goBackHome()"><i class="fas fa-home"></i> Trang chủ</button>
            <button class="btn thongbao"><i class="fas fa-bell"></i> Thông báo</button>
            <!-- Nút Admin với dropdown -->
            <div style="position: relative;">
                <button class="btn taikhoan" onclick="ddadmin()">
                    <i class="fas fa-user"></i> <?= !empty($admin_name) ? $admin_name : "Admin"; ?>
                </button>
                <div class="dropdown" id="adminDropdown">
                    <div class="profile">
                        <img src="<?= $admin_avatar ?>" alt="Avatar">
                        <p><strong><?= $admin_name ?></strong></p>
                        <p><?= $admin_phone ?></p>
                        <p><?= $admin_email ?></p>
                    </div>
                    <div class="logout" onclick="logout()">Đăng xuất</div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Thanh menu -->
    <nav class="menu">
        <div class="menu-item" id="menu-user" onclick="loadDLUser()"><i class="fas fa-users"></i> Quản lý người dùng</div>
        <div class="menu-item" id="menu-product" onclick="loadDLSanpham()"><i class="fas fa-box"></i> Quản lý sản phẩm</div>
        <div class="menu-item" id="menu-category" onclick="loadDLDanhmuc()"><i class="fas fa-list"></i> Quản lý danh mục</div>
        <div class="menu-item" id="menu-order" onclick="loadDLDonhang()"><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</div>
        <div class="menu-item" id="menu-discount" onclick="loadDLMGG()"><i class="fas fa-tags"></i> Quản lý khuyến mãi</div>
        <div class="menu-item" id="menu-report"><i class="fas fa-chart-bar"></i> Thống kê & Báo cáo</div>
        <div class="menu-item" id="menu-support" onclick="loadPhanHoi()"><i class="fas fa-headset"></i> Hỗ trợ khách hàng</div>
    </nav>
    
    <script>
        activateMenu();
        let id = getActiveMenu();
        if (id === "menu-user") loadDLUser();
        if (id === "menu-product") loadDLSanpham();
        if (id === "menu-category") loadDLDanhmuc();
        if (id === "menu-order") loadDLDonhang();
        if (id === "menu-discount") loadDLMGG();
        if (id === "menu-support") loadPhanHoi();
        function ddadmin() {
            document.getElementById("adminDropdown").classList.toggle("active");
        }
        document.addEventListener("click", function(event) {
            var dropdown = document.getElementById("adminDropdown");
            var button = document.querySelector(".taikhoan");

            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });
        handleSessionTimeout(<?= SESSION_TIMEOUT ?>);
    </script>
    <div class="main-content" id="main-content">
        <!-- Nội dung trang web --> 
    </div>
    <!-- Chân web -->
    <footer class="footer">
        <div class="footer-content">
            <p class="copyright">© 2025 M'K STORE - All rights reserved.</p>
            <div class="contact-info">
                <span><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, TP.HCM</span>
                <span><i class="fas fa-phone"></i> 0123 456 789</span>
            </div>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-facebook-messenger"></i> Zalo</a>
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
            </div>
        </div>
    </footer>    
</body>
</html>
