<?php
    include "config.php";
    $pdo = connectDatabase();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user'])) {
        header("Location: auth/dangnhap.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT iduser, trangthai, ngaykh FROM khxoatk WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "<script>alert('Tài khoản của bạn đã bị xóa và sẽ được xóa hoàn toàn sau 30 ngày!'); window.location.href = 'auth/logout.php';</script>";
    }

    define('SESSION_TIMEOUT', 1000);
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        error_log("Session inactive time: $inactive_time seconds");
    }
    $_SESSION['last_activity'] = time();
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    session_write_close();

    $stmt = $pdo->prepare("SELECT `hoten`, `anh`, `email`, `sdt` FROM `user` WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = htmlspecialchars($result['hoten'], ENT_QUOTES, 'UTF-8');
    $admin_email = htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8');
    $admin_phone = htmlspecialchars($result['sdt'], ENT_QUOTES, 'UTF-8');
    $admin_avatar = !empty($result['anh']) ? htmlspecialchars($result['anh'], ENT_QUOTES, 'UTF-8') : "https://i.pravatar.cc/100";

    try {
        $sql = "SELECT idsp, tensp, iddm FROM sanpham";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $products = [];
    }

    try {
        $sql = "DELETE FROM user WHERE iduser IN (SELECT iduser FROM khxoatk WHERE DATEDIFF(CURDATE(), ngaykh) >= 30)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting users: " . $e->getMessage());
    }

    $userId = $_SESSION['user']['iduser'];
    try {
        // Câu truy vấn 1: Kiểm tra xem bản ghi có tồn tại không
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM trangthaidb WHERE iduser = :iduser");
        $stmt->execute(['iduser' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $exists = $result['count'] > 0;

        if (!$exists) {
            // Câu truy vấn 2: Chèn bản ghi mới nếu chưa tồn tại
            $stmt = $pdo->prepare("INSERT INTO trangthaidb (iduser, trangthai) VALUES (:iduser, 1)");
            $stmt->execute(['iduser' => $userId]);
        } else {
            // Câu truy vấn 3: Cập nhật trạng thái nếu bản ghi đã tồn tại
            $stmt = $pdo->prepare("UPDATE trangthaidb SET trangthai = 1 WHERE iduser = :iduser");
            $stmt->execute(['iduser' => $userId]);
        }
    } catch (PDOException $e) {
        error_log("Error deleting users: " . $e->getMessage());
    }
?>

<script>
    function checkUserStatus() {
        fetch('check_status.php', {
            method: 'GET',
            credentials: 'same-origin' // Đảm bảo gửi session cookies
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'inactive') {
                window.location.href = 'auth/logout.php';
                // console.log("Tài khoản: " + data.status);
            } else if (data.status === 'active') {
                // console.log("Tài khoản: " + data.status);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }
// Gọi hàm checkUserStatus mỗi 10 giây
setInterval(checkUserStatus, 10000); // 10000ms = 10 giây
</script>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="sweetalert2/sweetalert2.min.css">
    <link href="tailwind/dist/output.css" rel="stylesheet">
    <script src="sweetalert2/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
    <script src="script.js"></script>
    <link rel="icon" href="picture/logoTD.png" type="image/png">
    <style>
        :root {
            --primary-color: #ff6200;
            --secondary-color: #0288d1;
            --bg-light: #f5f5f5;
            --bg-white: #ffffff;
            --text-dark: #212121;
            --text-light: #757575;
            --sidebar-width: 240px;
            --sidebar-width-mobile: 200px;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        .dropdown-hidden {
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
        }

        .dropdown-active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        iframe::-webkit-scrollbar {
            width: 6px;
        }

        iframe::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        iframe::-webkit-scrollbar-track {
            background-color: var(--bg-light);
        }

        .store-name {
            font-family: 'Abril Fatface', serif;
            font-size: 2.6rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: colorShift 6s infinite ease-in-out, glowEffect 3s infinite ease-in-out;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-storename {
            font-family: 'Abril Fatface', serif;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: colorShift 6s infinite ease-in-out, glowEffect 3s infinite ease-in-out;
            transition: transform 0.3s ease-in-out;
        }

        @keyframes colorShift {
            0%   { color: #0ea5e9; }
            33%  { color: #22c55e; }
            66%  { color: #f97316; }
            100% { color: #0ea5e9; }
        }

        @keyframes glowEffect {
            0% {
                text-shadow: 0 0 6px rgba(14, 165, 233, 0.4), 0 0 12px rgba(14, 165, 233, 0.2);
            }
            33% {
                text-shadow: 0 0 6px rgba(34, 197, 94, 0.4), 0 0 12px rgba(34, 197, 94, 0.2);
            }
            66% {
                text-shadow: 0 0 6px rgba(249, 115, 22, 0.4), 0 0 12px rgba(249, 115, 22, 0.2);
            }
            100% {
                text-shadow: 0 0 6px rgba(14, 165, 233, 0.4), 0 0 12px rgba(14, 165, 233, 0.2);
            }
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            transition: opacity 0.3s ease;
            justify-content: center;
        }

        .logo-container.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-logoTD {
            display: flex;
            height: 80px;
            width: 80px;
            align-items: center;
        }

        .sidebar-logoTD:hover {
            transform: scale(1.1) rotateY(15deg);
            filter: drop-shadow(0 0 14px rgba(0, 255, 255, 0.8));
        }

        .logoTD {
            display: flex;
            height: 80px;
            width: 80px;
            margin-left: 50px;
            align-items: center;
            animation: rotateYSoft 7s linear infinite, glowPulse 2.5s ease-in-out infinite;
            transform-style: preserve-3d;
            backface-visibility: visible;
            transition: transform 0.4s ease-in-out, filter 0.4s ease-in-out;
            filter: drop-shadow(0 0 6px rgba(0, 255, 255, 0.4));
        }

        .logoTD:hover {
            transform: scale(1.1) rotateY(15deg);
            filter: drop-shadow(0 0 14px rgba(0, 255, 255, 0.8));
        }

        @keyframes rotateYSoft {
            0%   { transform: rotateY(0deg); }
            25%  { transform: rotateY(-180deg); }
            50%  { transform: rotateY(0deg); }
            75%  { transform: rotateY(180deg); }
            100% { transform: rotateY(0deg); }
        }

        .logoTD:hover {
            transform: scale(1.1);
        }

        .input-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .input-field {
            width: 100%;
            padding: 12px 40px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--bg-white);
            font-size: 1rem;
            color: var(--text-dark);
        }

        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 8px rgba(255, 98, 0, 0.2);
        }

        .icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 18px;
        }

        .mic-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .mic-icon:hover {
            color: var(--primary-color);
        }

        .mic-icon.listening i {
            color: var(--primary-color);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.6; }
            100% { transform: scale(1); opacity: 1; }
        }

        .mic-status.hidden {
            display: none;
        }

        #sidebar {
            width: 300px;
            background: var(--bg-white);
            color: var(--text-dark);
            border-right: 1px solid #e0e0e0;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 50;
        }

        #sidebar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        #sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) var(--bg-white);
        }

        #sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar-menu::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        #sidebar-menu::-webkit-scrollbar-track {
            background-color: var(--bg-white);
        }

        #home-icon-toggle {
            position: fixed;
            left: 16px;
            top: 25px;
            z-index: 1000;
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, background-color 0.3s ease, left 0.3s ease;
        }

        #home-icon-toggle:hover {
            transform: scale(1.1);
            background: #e55a00;
        }

        #home-icon-toggle.sidebar-expanded {
            left: calc(var(--sidebar-width-mobile) + 120px);
        }

        @media (max-width: 768px) {
            #home-icon-toggle {
                left: 12px;
                top: 40px;
                width: 44px;
                height: 44px;
            }
            #home-icon-toggle.sidebar-expanded {
                left: calc(var(--sidebar-width-mobile) + 80px);
            }
            #sidebar {
                width: 270px;
                z-index: 100; /* Đảm bảo sidebar nằm trên các thành phần khác */
            }
            .store-name {
                font-size: 2rem;
            }
            .logoTD {
                height: 80px;
                width: 80px;
            }
            .input-container {
                max-width: 100%;
            }
            .input-field {
                font-size: 0.9rem;
                padding: 10px 36px;
                margin-bottom: 5px;
                margin-top: 5px;
            }
            .icon, .mic-icon {
                font-size: 16px;
            }
            #sidebar-header h2 {
                font-size: 1.25rem;
            }
            .menu-item {
                padding: 10px 14px;
                font-size: 0.9rem;
                gap: 10px;
            }
            .menu-item i {
                font-size: 1rem;
            }
            .menusidebar h2, .menutaikhoan p {
                font-size: 0.95rem;
            }
            .menutaikhoan .taikhoan {
                padding: 12px;
            }
            #adminDropdown {
                width: 180px;
                right: 8px;
            }
            #adminDropdown img {
                width: 48px;
                height: 48px;
            }
            #adminDropdown p {
                font-size: 0.85rem;
            }
            .btndx {
                padding: 10px;
                font-size: 0.9rem;
            }
        }

        .menu-item {
            padding: 12px 16px;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-dark);
        }

        .menu-item:hover {
            background-color: #f0f0f0;
            transform: translateX(4px);
        }

        .menu-item.active {
            background-color: #fff3e0;
            color: var(--primary-color);
        }

        .menusidebar {
            padding: 16px;
        }

        .menutaikhoan {
            padding: 16px;
            border-top: 1px solid #e0e0e0;
        }

        #adminDropdown {
            position: absolute;
            width: 220px;
            left: 22px;
            bottom: 100%;
            background: var(--bg-white);
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            color: var(--text-dark);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 10;
        }

        #adminDropdown.dropdown-active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .btndx {
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btndx:hover {
            background-color: #f0f0f0;
            color: var(--primary-color);
        }

        .main-content {
            flex: 1 1 auto;
            width: 100%;
            height: 75vh;
            min-width: 300px;
            box-sizing: border-box;
        }

        @media (min-width: 769px) {
            .main-content.expanded {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                width: 100%;
            }
            .main-content.expanded {
                width: 100%; /* Không thay đổi width */
                margin-left: 0; /* Không đẩy nội dung */
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert-success {
            background: #4caf50;
            color: white;
        }

        .alert-error {
            background: #f44336;
            color: white;
        }

        footer {
            margin-top: auto;
        }

        @media (max-width: 768px) {
            footer {
                padding: 12px;
            }
            footer .text-sm {
                font-size: 0.85rem;
            }
            footer .max-w-7xl {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Home Icon Button -->
    <button id="home-icon-toggle" aria-label="Mở menu">
        <i class="fas fa-home"></i>
    </button>

    <!-- Navbar -->
    <nav class="bg-white px-4 py-2 shadow-sm">
        <div class="flex flex-col md:flex-row items-center md:items-center md:justify-between">
            <div id="navbar-logo" class="logo-container">
                <img src="picture/logoshop.png" alt="Logo Cửa Hàng" class="logoTD">
                <span class="store-name">MONKEY STORE</span>
            </div>
            <div class="ml-auto md:mt-0 w-full md:w-auto">
                <div class="input-container">
                    <i class="fas fa-search icon"></i>
                    <input type="text" placeholder="Tìm kiếm sản phẩm...." class="input-field search-bar" onkeyup="handleSearch(this.value)" aria-label="Tìm kiếm sản phẩm, người dùng hoặc đơn hàng">
                    <button id="mic-btn" class="mic-icon mic-btn" aria-label="Tìm kiếm bằng giọng nói">
                        <i class="fas fa-microphone"></i>
                        <span class="mic-status hidden text-sm text-red-600">Đang lắng nghe...</span>
                    </button>
                </div>
            </div>
            <div id="search-results" class="mt-2 w-full md:w-96 bg-white rounded-md shadow-sm hidden"></div>
        </div>
    </nav>

    <!-- Sidebar Menu -->
    <nav id="sidebar" class="fixed top-0 left-0 h-full transition-all duration-300 z-50 -translate-x-full" aria-label="Menu điều hướng">
        <div class="flex flex-col h-full">
            <div id="sidebar-logo" class="logo-container hidden p-4 border-b border-gray-200">
                <img src="picture/logoshop.png" alt="Logo Cửa Hàng" class="sidebar-logoTD">
                <span class="sidebar-storename">MONKEY STORE</span>
            </div>
            <div id="sidebar-header">
                <h2 class="text-lg font-semibold text-gray-800 flex-1 text-center">Menu</h2>
            </div>
            <div id="sidebar-menu">
                <?php if ($_SESSION['user']['quyen'] != 1): ?>
                    <div class="menu-item" id="menu-model" onclick="clearLocalStorage()" aria-label="Xóa mô hình TensorFlow">
                        <i class="fas fa-eraser"></i> Model
                    </div>
                <?php endif; ?>
                <div class="menu-item" id="menu-trangchu" onclick="loadTTTrangChu()" aria-label="Quay về trang chủ">
                    <i class="fas fa-home"></i> Trang chủ
                </div>
                <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM yeucaudonhang WHERE trangthai = 0");
                    $stmt->execute();
                    $thongbaoCount = (int) $stmt->fetchColumn();
                ?>
                <?php if ($_SESSION['user']['quyen'] != 1): ?>
                    <div class="menu-item relative" id="menu-tb" onclick="loadThongBao()" aria-label="Xem thông báo">
                        <i class="fas fa-bell"></i> Thông báo
                        <?php if ($thongbaoCount > 0): ?>
                            <span class="absolute top-0 right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5" aria-label="Số lượng thông báo chưa xem">
                                <?= $thongbaoCount ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="menu-item" id="menu-baocao" onclick="loadBaoCao()" aria-label="Quay về trang chủ">
                        <i class="fas fa-file-invoice"></i> Xuất báo cáo
                    </div>
                <?php endif; ?>
                <div class="menusidebar">
                    <h2 class="text-lg font-semibold text-gray-800">Quản lý</h2>
                </div>
                <?php if ($_SESSION['user']['quyen'] != 1): ?>
                    <div class="menu-item" id="menu-user" onclick="loadDLUser()" aria-label="Quản lý người dùng">
                        <i class="fas fa-users"></i> Người dùng
                    </div>
                <?php else: ?>
                    <div class="menu-item" id="menu-user" onclick="loadDLUser()" aria-label="Quản lý tài khoản cá nhân">
                        <i class="fas fa-users"></i> Tài khoản cá nhân
                    </div>
                <?php endif; ?>
                <?php if ($_SESSION['user']['quyen'] != 1): ?>
                    <div class="menu-item" id="menu-product" onclick="loadDLSanpham()" aria-label="Quản lý sản phẩm">
                        <i class="fas fa-box"></i> Sản phẩm
                    </div>
                <?php else: ?>
                    <div class="menu-item" id="menu-product" onclick="loadDLSanpham()" aria-label="Quản lý mua sắm">
                        <i class="fas fa-box"></i> Mua sắm
                    </div>
                <?php endif; ?>
                <?php if ($_SESSION['user']['quyen'] != 1): ?>
                    <div class="menu-item" id="menu-category" onclick="loadDLDanhmuc()" aria-label="Quản lý danh mục">
                        <i class="fas fa-list"></i> Danh mục
                    </div>
                    <div class="menu-item" id="menu-discount" onclick="loadDLMGG()" aria-label="Quản lý khuyến mãi">
                        <i class="fas fa-tags"></i> Khuyến mãi
                    </div>
                <?php endif; ?>
                <?php if ($_SESSION['user']['quyen'] != 1): ?>
                    <div class="menu-item" id="menu-order" onclick="loadDLDonhang()" aria-label="Quản lý đơn hàng">
                        <i class="fas fa-chart-bar"></i> Đơn hàng
                    </div>
                <?php else: ?>
                    <div class="menu-item" id="menu-order" onclick="loadDLDonhang()" aria-label="Quản lý đơn mua hàng">
                        <i class="fas fa-chart-bar"></i> Đơn mua hàng
                    </div>
                <?php endif; ?>
                <div class="menu-item" id="menu-gh" onclick="loadGH()" aria-label="Giỏ hàng">
                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                </div>
                <div class="menu-item" id="menu-support" onclick="loadPhanHoi()" aria-label="Hỗ trợ khách hàng">
                    <i class="fas fa-headset"></i> Hỗ trợ
                </div>
            </div>
            <div class="menutaikhoan">
                <div class="relative">
                    <button class="flex items-center gap-3 p-3 w-full rounded-md hover:bg-gray-100 taikhoan" onclick="ddadmin()" aria-label="Tài khoản admin" aria-expanded="false">
                        <img src="<?= $admin_avatar ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                        <span class="font-medium text-gray-800"><?= !empty($admin_name) ? $admin_name : "Admin"; ?></span>
                    </button>
                    <div id="adminDropdown" class="dropdowntk hidden">
                        <div class="p-4 border-b text-center">
                            <img src="<?= $admin_avatar ?>" alt="Avatar admin" 
                            class="w-16 h-16 rounded-full mx-auto border-2 border-gray-200" 
                            onclick="taikhoancn()" aria-label="Xem thông tin cá nhân">
                            <p class="font-semibold mt-2 text-gray-800"><?= $admin_name ?></p>
                            <p class="text-sm text-gray-600"><?= $admin_phone ?></p>
                            <p class="text-sm text-gray-600 italic"><?= $admin_email ?></p>
                        </div>
                        <div class="btndx" onclick="logout()" aria-label="Đăng xuất">
                            <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <iframe id="Frame" src="" class="w-full h-full border-none" scrolling="auto" aria-label="Nội dung chính"></iframe>
    </div>

    <footer class="bg-white border-t border-gray-200 text-gray-600 px-4 py-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div class="text-center md:text-left text-sm">
                <span>© 2025 M'K STORE - All rights reserved.</span>
            </div>
            <div class="flex flex-col md:flex-row items-center md:items-start text-sm gap-2 md:gap-4">
                <span onclick="ggmap()"><i class="fas fa-map-marker-alt mr-2"></i>Địa chỉ: 73 Nguyễn Huệ, P.2, TP. Vĩnh Long</span>
                <span><i class="fas fa-phone mr-2"></i>0702 8045 94</span>
            </div>
            <div class="flex gap-4 text-sm">
                <a href="https://zalo.me/0702804594" class="text-blue-600 hover:underline" aria-label="Zalo">
                    <i class="fab fa-facebook-messenger mr-1"></i>Zalo
                </a>
                <a href="https://www.messenger.com/t/29094943980089871/" class="text-blue-600 hover:underline" aria-label="Facebook">
                    <i class="fab fa-facebook mr-1"></i>Facebook
                </a>
            </div>
        </div>
    </footer>
<script>
    function ggmap() {
        window.location.href="https://www.google.com/maps?q=73+Nguyễn+Huệ,+Phường+1,+Vĩnh+Long,+85000,+Việt+Nam";
    }
</script>
    <!-- Alerts -->
    <div id="success-alert" class="alert alert-success"></div>
    <div id="error-alert" class="alert alert-error"></div>
    <?php
        $statusMessages = [
            'cnuserT' => ['Thành Công!', 'Thông tin đã được cập nhật.', 'picture/success.png'],
            'cnuserF' => ['Thất bại!', '', 'picture/error.png'],
            'themuserT' => ['Thành Công!', 'Người dùng đã được thêm vào danh sách!', 'picture/success.png'],
            'themuserF' => ['Thất bại!', '', 'picture/error.png'],
            'cnspT' => ['Thành Công!', 'Thông tin sản phẩm đã được cập nhật thành công!', 'picture/success.png'],
            'cnspF' => ['Thất bại!', '', 'picture/error.png'],
            'themspT' => ['Thành Công!', 'Sản phẩm đã được thêm thành công!', 'picture/success.png'],
            'themspF' => ['Thất bại!', '', 'picture/error.png'],
            'xoaspT' => ['Thành Công!', 'Xóa sản phẩm thành công!', 'picture/success.png'],
            'xoaspF' => ['Thất bại!', '', 'picture/error.png'],
            'cnanhuserT' => ['Thành Công!', 'Cập nhật ảnh thành công!', 'picture/success.png'],
            'cnanhuserF' => ['Thất bại!', '', 'picture/error.png'],
            'laylaispT' => ['Lấy lại tài khoản thành công!', '', 'picture/success.png'],
            'laylaispF' => ['Lỗi khi cấp lại tài!', '', 'picture/error.png'],
            'themdmT' => ['Thêm danh mục thành công', '', 'picture/success.png'],
            'cndmT' => ['Thành Công!', 'Cập nhật ảnh thành công!', 'picture/success.png'],
            'cndmF' => ['Thất bại!', '', 'picture/error.png'],
            'nhanhangT' => ['Bạn đã nhận hàng thành công!','picture/success.png'],
            'nhanhangF' => ['Cập nhật trạng thái nhận hàng thất bại!','picture/error.png'],
            'khoiphucdhT' => ['Bạn đã khôi phục đơn hàng thành công!','picture/success.png'],
            'khoiphucdhF' => ['Bạn không thể khôi phục đơn hàng!','picture/error.png']
        ];
        if (isset($_GET['status']) && isset($statusMessages[$_GET['status']])) {
            $msg = $statusMessages[$_GET['status']];
            echo "<script>showCustomAlert('{$msg[0]}', '{$msg[1]}', '{$msg[2]}'); setTimeout(() => { window.location.href='trangchu.php'; }, 3000);</script>";
        }
    ?>

    <script>
        const rawProducts = <?php echo json_encode($products); ?>;
        const products = rawProducts.map(p => ({
            id: parseInt(p.idsp, 10) || 0,
            name: p.tensp || 'unknown',
            category: String(p.iddm ?? 'unknown')
        })).filter(p => p.id > 0);

        function checkEmptyProducts() {
            if (products.length === 0) {
                document.getElementById('search-results').innerHTML = '<div class="p-2 bg-white rounded-md shadow-sm">Không có sản phẩm để tìm kiếm. Vui lòng thêm sản phẩm.</div>';
                return true;
            }
            return false;
        }

        const keywordMapping = {
            'backpack': 'balo', 'cặp': 'balo', 'túi đeo lưng': 'balo', 'túi đi học': 'balo', 'ba lô': 'balo',
            'bag': 'túi', 'túi xách': 'túi', 'túi đeo': 'túi',
            'wallet': 'ví', 'clutch': 'ví', 'ví tiền': 'ví',
            'watch': 'đồng hồ', 'đồng hồ đeo tay': 'đồng hồ', 'đồng hồ thời trang': 'đồng hồ',
            'leather': 'da', 'đồ da': 'da', 'phụ kiện da': 'da',
            'eye': 'mắt', 'mỹ phẩm mắt': 'mắt', 'phấn mắt': 'mắt',
            'lip': 'môi', 'son môi': 'môi', 'mỹ phẩm môi': 'môi',
            'tool': 'dụng cụ', 'phụ kiện': 'dụng cụ', 'đồ dùng': 'dụng cụ',
            'necklace': 'vòng cổ', 'dây chuyền': 'vòng cổ', 'chuỗi ngọc': 'vòng cổ', 'vongco': 'vòng cổ',
            'bracelet': 'vòng lắc', 'vòng tay': 'vòng lắc', 'lắc': 'vòng lắc',
            'ring': 'nhẫn', 'nhẫn đính hôn': 'nhẫn', 'nhẫn cưới': 'nhẫn', 'nhẫn vàng 18k': 'nhẫn',
            'earring': 'bông tai', 'khuyên tai': 'bông tai', 'hoa tai': 'bông tai',
            'teddy bear': 'gấu bông', 'gấu teddy': 'gấu bông', 'thú nhồi bông': 'gấu bông'
        };

        let trainingData = [];
        let vocabulary = [];
        function prepareTrainingData() {
            trainingData = products.map(product => {
                const categoryKeywords = (product.category && product.category.trim() !== '') 
                    ? product.category.toLowerCase().split(/\s+/) : [];
                const productNameWords = product.name.toLowerCase().split(/\s+/).filter(word => !/^\d+$/.test(word));
                return {
                    keywords: [
                        ...productNameWords, ...categoryKeywords,
                        ...(product.name.toLowerCase().includes("balo") ? ["cặp", "backpack", "túi đeo lưng", "túi đi học"] : []),
                        ...(product.name.toLowerCase().includes("túi") ? ["bag", "túi xách", "túi đeo"] : []),
                        ...(product.name.toLowerCase().includes("ví") ? ["wallet", "clutch", "ví tiền"] : []),
                        ...(product.name.toLowerCase().includes("đồng hồ") ? ["watch", "đồng hồ đeo tay", "đồng hồ thời trang"] : []),
                        ...(product.name.toLowerCase().includes("da") ? ["leather", "đồ da", "phụ kiện da"] : []),
                        ...(product.name.toLowerCase().includes("mắt") ? ["eye", "mỹ phẩm mắt", "phấn mắt"] : []),
                        ...(product.name.toLowerCase().includes("môi") ? ["lip", "son môi", "mỹ phẩm môi"] : []),
                        ...(product.name.toLowerCase().includes("dụng cụ") ? ["tool", "phụ kiện", "đồ dùng"] : []),
                        ...(product.name.toLowerCase().includes("vòng cổ") ? ["dây chuyền", "chuỗi ngọc", "ta2589", "vongco", "necklace"] : []),
                        ...(product.name.toLowerCase().includes("vòng lắc") ? ["vòng tay", "lắc", "bracelet"] : []),
                        ...(product.name.toLowerCase().includes("nhẫn") ? ["nhẫn đính hôn", "nhẫn cưới", "nhẫn vàng 18k", "ring"] : []),
                        ...(product.name.toLowerCase().includes("bông tai") ? ["khuyên tai", "hoa tai", "earring"] : []),
                        ...(product.name.toLowerCase().includes("gấu bông") ? ["gấu teddy", "thú nhồi bông", "teddy bear"] : [])
                    ],
                    productId: product.id
                };
            });
            vocabulary = products.length > 0 ? [...new Set(trainingData.flatMap(item => item.keywords))] : [];
            const currentVocabulary = JSON.stringify(vocabulary);
            const savedVocabulary = localStorage.getItem('search-vocabulary');
            if (savedVocabulary !== currentVocabulary) {
                localStorage.removeItem('search-model');
                localStorage.setItem('search-vocabulary', currentVocabulary);
            }
        }

        function keywordsToVector(keywords) {
            return vocabulary.map(word => keywords.includes(word) ? 1 : 0);
        }

        let xs = null;
        let ys = null;
        function prepareTrainingTensors() {
            xs = products.length > 0 ? tf.tensor2d(trainingData.map(item => keywordsToVector(item.keywords))) : null;
            ys = products.length > 0 ? tf.tensor2d(trainingData.map(item => [item.productId]), [trainingData.length, 1]) : null;
        }

        async function trainModel() {
            if (products.length === 0) {
                return null;
            }
            const model = tf.sequential();
            model.add(tf.layers.dense({ units: 32, activation: 'relu', inputShape: [vocabulary.length] }));
            model.add(tf.layers.dense({ units: 16, activation: 'relu' }));
            model.add(tf.layers.dense({ units: Math.max(...products.map(p => p.id)) + 1 || 1, activation: 'softmax' }));
            model.compile({ optimizer: tf.train.adam(0.005), loss: 'sparseCategoricalCrossentropy', metrics: ['accuracy'] });
            await model.fit(xs, ys, { epochs: 150, verbose: 0 });
            await model.save('localstorage://search-model');
            return model;
        }

        async function loadModel() {
            if (products.length === 0) {
                return null;
            }
            try {
                const model = await tf.loadLayersModel('localstorage://search-model');
                const expectedShape = model.layers[0].input.shape[1];
                if (expectedShape !== vocabulary.length) {
                    localStorage.removeItem('search-model');
                    return await trainModel();
                }
                return model;
            } catch (e) {
                return await trainModel();
            }
        }

        async function predictProduct(searchQuery) {
            if (checkEmptyProducts()) {
                return null;
            }
            let normalizedQuery = searchQuery.toLowerCase();
            for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                if (normalizedQuery.includes(keyword)) {
                    normalizedQuery = normalizedQuery.replace(keyword, mapped);
                }
            }
            const keywords = normalizedQuery.split(/\s+/);
            const inputVector = tf.tensor2d([keywordsToVector(keywords)]);
            const model = await loadModel();
            if (!model) return null;
            const prediction = model.predict(inputVector);
            const predictedId = tf.argMax(prediction, axis=1).dataSync()[0];
            const product = products.find(p => p.id === predictedId) || null;
            if (!product) {
                for (let keyword of keywords) {
                    const matchedProduct = products.find(p => p.name.toLowerCase().includes(keyword));
                    if (matchedProduct) {
                        return matchedProduct;
                    }
                }
            }
            return product;
        }

        let searchTimeout;
        async function handleSearch(query) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                query = query.trim();
                const resultsDiv = document.getElementById('search-results');
                resultsDiv.innerHTML = '';
                const activeMenu = getActiveMenu();
                if (activeMenu === "menu-product") {
                    prepareTrainingData();
                    prepareTrainingTensors();
                    if (query) {
                        if (checkEmptyProducts()) {
                            searchProducts(query);
                            return;
                        }
                        try {
                            const predictedProduct = await predictProduct(query);
                            if (predictedProduct) {
                                resultsDiv.innerHTML = `
                                    <div class="search-result p-2 bg-white rounded-md shadow-sm">
                                        <span>${predictedProduct.name}</span>
                                    </div>
                                `;
                                let normalizedQuery = query.toLowerCase();
                                for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                                    if (normalizedQuery.includes(keyword)) {
                                        normalizedQuery = normalizedQuery.replace(keyword, mapped);
                                        break;
                                    }
                                }
                                searchProducts(normalizedQuery);
                            } else {
                                resultsDiv.innerHTML = '<div class="p-2 bg-white rounded-md shadow-sm">Không tìm thấy sản phẩm</div>';
                                searchProducts(query);
                            }
                        } catch (e) {
                            resultsDiv.innerHTML = '<div class="p-2 bg-white rounded-md shadow-sm">Lỗi khi tìm kiếm. Vui lòng thử lại.</div>';
                            searchProducts(query);
                        }
                    } else {
                        searchProducts(query);
                    }
                } 
            }, 300);
        }

        function searchProducts(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "sanpham/hienthisanpham.php?query=" + encodeURIComponent(query);
                    iframe.onload = function() {
                        if (iframe.contentDocument && iframe.contentDocument.body.innerHTML.trim() === "") {
                            iframe.contentDocument.body.innerHTML = '<div class="p-4 text-gray-500">Không có sản phẩm nào để hiển thị.</div>';
                        }
                    };
                }
            }, 100);
        }

        document.addEventListener("DOMContentLoaded", () => {
            const micButton = document.getElementById("mic-btn");
            const micStatus = micButton.querySelector(".mic-status");
            const searchBar = document.querySelector(".search-bar");
            if (!('webkitSpeechRecognition' in window)) {
                alert("Trình duyệt không hỗ trợ tìm kiếm bằng giọng nói.");
                micButton.disabled = true;
                return;
            }
            const recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = "vi-VN";
            micButton.addEventListener("click", () => {
                micButton.classList.add("listening");
                micStatus.classList.remove("hidden");
                recognition.start();
            });
            recognition.onresult = (event) => {
                const speechResult = event.results[0][0].transcript;
                searchBar.value = speechResult;
                handleSearch(speechResult);
            };
            recognition.onerror = (event) => {
                console.error("Lỗi nhận dạng giọng nói:", event.error);
            };
            recognition.onend = () => {
                micButton.classList.remove("listening");
                micStatus.classList.add("hidden");
            };
        });

        function ddadmin() {
            const dropdown = document.getElementById("adminDropdown");
            const taikhoanBtn = document.querySelector(".taikhoan");
            if (!dropdown || !taikhoanBtn) return;
            const isHidden = !dropdown.classList.contains("dropdown-active");
            dropdown.classList.toggle("dropdown-active", isHidden);
            dropdown.classList.toggle("hidden", !isHidden);
            taikhoanBtn.setAttribute("aria-expanded", isHidden);
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById("adminDropdown");
            const trigger = document.querySelector(".taikhoan");
            if (dropdown && trigger && !dropdown.contains(event.target) && !trigger.contains(event.target)) {
                dropdown.classList.remove("dropdown-active");
                trigger.setAttribute("aria-expanded", "false");
            }
        });

        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const homeIconToggle = document.getElementById('home-icon-toggle');
            const navbarLogo = document.getElementById('navbar-logo');
            const sidebarLogo = document.getElementById('sidebar-logo');
            const mainContent = document.getElementById('main-content');
            const isExpanded = sidebar.classList.contains('translate-x-0');

            if (isExpanded) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                navbarLogo.classList.remove('hidden');
                sidebarLogo.classList.add('hidden');
                homeIconToggle.classList.remove('sidebar-expanded');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                navbarLogo.classList.add('hidden');
                sidebarLogo.classList.remove('hidden');
                homeIconToggle.classList.add('sidebar-expanded');
            }
            homeIconToggle.setAttribute('aria-expanded', !isExpanded);
            localStorage.setItem('sidebarStateadmin', isExpanded ? 'collapsed' : 'expanded');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const homeIconToggle = document.getElementById('home-icon-toggle');
            const navbarLogo = document.getElementById('navbar-logo');
            const sidebarLogo = document.getElementById('sidebar-logo');
            const mainContent = document.getElementById('main-content');
            const searchBox = document.querySelector('.input-container');
            const savedState = localStorage.getItem('sidebarStateadmin');

            let firstVisitAfterLogin = localStorage.getItem('firstVisitAfterLogin');
            if (firstVisitAfterLogin === null) {
                firstVisitAfterLogin = 'true';
                localStorage.setItem('firstVisitAfterLogin', 'true');
            }

            // Sidebar logic
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                navbarLogo.classList.remove('hidden');
                sidebarLogo.classList.add('hidden');
                mainContent.classList.remove('expanded');
                homeIconToggle.classList.remove('sidebar-expanded');
                homeIconToggle.setAttribute('aria-expanded', 'false');
            } else if (savedState === 'expanded') {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                navbarLogo.classList.add('hidden');
                sidebarLogo.classList.remove('hidden');
                mainContent.classList.add('expanded');
                homeIconToggle.classList.add('sidebar-expanded');
                homeIconToggle.setAttribute('aria-expanded', 'true');
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                navbarLogo.classList.remove('hidden');
                sidebarLogo.classList.add('hidden');
                mainContent.classList.remove('expanded');
                homeIconToggle.classList.remove('sidebar-expanded');
                homeIconToggle.setAttribute('aria-expanded', 'false');
            }

            homeIconToggle.addEventListener('click', toggleMenu);
            activateMenu();

            const handleMenuClick = (menuId, loadFunction, showSearch = false) => {
                document.getElementById(menuId)?.addEventListener('click', () => {
                    if (showSearch) {
                        searchBox.style.display = 'block';
                    } else {
                        searchBox.style.display = 'none';
                    }
                    loadFunction();
                    localStorage.removeItem('profileMenuClicked');
                });
            };

            // Thiết lập các menu và hành vi tương ứng
            handleMenuClick("menu-user", loadDLUser, false);
            handleMenuClick("menu-product", loadDLSanpham, true);
            handleMenuClick("menu-category", loadDLDanhmuc, false);
            handleMenuClick("menu-order", loadDLDonhang, false);
            handleMenuClick("menu-discount", loadDLMGG, false);
            handleMenuClick("menu-support", loadPhanHoi, false);
            handleMenuClick("menu-gh", loadGH, false);
            handleMenuClick("menu-tb", loadThongBao, false);
            handleMenuClick("menu-trangchu", loadTTTrangChu, false);
            handleMenuClick("menu-baocao", loadBaoCao, false);

            // Lần load đầu tiên sau đăng nhập
            setTimeout(() => {
                let id = getActiveMenu();
                localStorage.removeItem('qltaikhoancanhan');
                if (id === "menu-user") {
                    searchBox.style.display = 'none';
                    loadDLUser();
                } else if (id === "menu-product") {
                    searchBox.style.display = 'block';
                    loadDLSanpham();
                } else if (id === "menu-category") {
                    searchBox.style.display = 'none';
                    loadDLDanhmuc();
                } else if (id === "menu-order") {
                    searchBox.style.display = 'none';
                    loadDLDonhang();
                } else if (id === "menu-discount") {
                    searchBox.style.display = 'none';
                    loadDLMGG();
                } else if (id === "menu-support") {
                    searchBox.style.display = 'none';
                    loadPhanHoi();
                } else if (id === "menu-gh") {
                    searchBox.style.display = 'none';
                    loadGH();
                } else if (id === "menu-tb") {
                    searchBox.style.display = 'none';
                    loadThongBao();
                } else if (id === "menu-trangchu") {
                    searchBox.style.display = 'none';
                    loadTTTrangChu();
                } else if (id == "menu-baocao") {
                    searchBox.style.display = 'none';
                    loadBaoCao();
                } else {
                    searchBox.style.display = 'none';
                    loadTTTrangChu();
                }
            }, 100);
        });                 

        function taikhoancn() {
            loadTaiKhoanCN();
            localStorage.setItem('qltaikhoancanhan', 'true');
            document.querySelectorAll(".menu-item").forEach(item => item.classList.remove("active"));
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            if (localStorage.getItem('profileMenuClicked') === 'true') {
                loadTaiKhoanCN();
            }
        });

        handleSessionTimeout(<?= SESSION_TIMEOUT ?>);
    </script>
</body>
</html>
<?php
