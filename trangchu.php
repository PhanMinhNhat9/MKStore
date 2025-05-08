<?php
    include "config.php";
    $pdo = connectDatabase();
    // Bắt đầu session nếu chưa có
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Kiểm tra xem admin đã đăng nhập chưa
    if (isset($_SESSION['user'])) {
    } else {
        header("Location: auth/dangnhap.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT iduser, trangthai, ngaykh FROM khxoatk WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "
        <script>
            alert('Tài khoản của bạn đã bị xóa và sẽ được xóa hoàn toàn sau 30 ngày!');
            window.location.href = 'logout.php';
        </script>";
    }

    define('SESSION_TIMEOUT', 1800);

    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        error_log("Session inactive time: $inactive_time seconds"); // Debug log
    }
    // Cập nhật lại thời gian hoạt động cuối cùng
    $_SESSION['last_activity'] = time();
    // Làm mới session ID để tăng cường bảo mật (kiểm tra session trước khi gọi)
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    session_write_close(); // Đảm bảo session được ghi lại ngay lập tức

    // Lấy thông tin admin từ session
    $stmt = $pdo->prepare("SELECT `hoten`, `anh`, `email`, `sdt` FROM `user` WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $admin_name = htmlspecialchars($result['hoten'], ENT_QUOTES, 'UTF-8');
    $admin_email = htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8');
    $admin_phone = htmlspecialchars($result['sdt'], ENT_QUOTES, 'UTF-8');
    $admin_avatar = !empty($result['anh']) ? htmlspecialchars($result['anh'], ENT_QUOTES, 'UTF-8') : "https://i.pravatar.cc/100";
    
    // Lấy danh sách sản phẩm từ cơ sở dữ liệu
    try {
        $sql = "SELECT idsp, tensp, iddm FROM sanpham";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $products = [];
    }

    // Xóa tài khoản người dùng bị đánh dấu xóa sau 30 ngày
    try {
        $sql = "DELETE FROM user
                WHERE iduser IN (
                    SELECT iduser
                    FROM khxoatk
                    WHERE DATEDIFF(CURDATE(), ngaykh) >= 30
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting users: " . $e->getMessage());
    }
?>

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
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <script src="script.js"></script>
    <link rel="icon" href="picture/logoTD.png" type="image/png">
    <style>
        /* Hamburger chung */
        .hamburger {
            width: 26px;
            height: 24px;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Các thanh */
        .hamburger span {
            display: block;
            position: absolute;
            height: 3px;
            width: 100%;
            background:rgb(255, 255, 255);
            border-radius: 3px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: all 0.3s ease;
        }

        /* Vị trí từng thanh */
        .hamburger span:nth-child(1) {
            top: 0px;
        }

        .hamburger span:nth-child(2) {
            top: 8px;
        }

        .hamburger span:nth-child(3) {
            top: 16px;
        }

        /* Trạng thái active - chuyển sang dấu X */
        .hamburger.active span:nth-child(1) {
            top: 8px;
            transform: rotate(45deg);
            background: #ffffff;
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
            left: -60px;
        }

        .hamburger.active span:nth-child(3) {
            top: 8px;
            transform: rotate(-45deg);
            background: #ffffff;
        }

        /* Dropdown animation */
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

        /* Scrollbar styling */
        iframe::-webkit-scrollbar {
            width: 8px;
        }

        iframe::-webkit-scrollbar-thumb {
            background-color: #4b5563;
            border-radius: 4px;
        }

        iframe::-webkit-scrollbar-track {
            background-color: #e5e7eb;
        }

        .store-name {
            font-family: 'Abril Fatface', serif;
            font-size: 2.6rem; /* text-3xl */
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: colorShift 6s infinite ease-in-out, glowEffect 3s infinite ease-in-out;
            transition: transform 0.3s ease-in-out;
        }


        @keyframes colorShift {
            0%   { color: #0ea5e9; }   /* Xanh dương (sky-500) */
            33%  { color: #22c55e; }   /* Xanh lá (green-500) */
            66%  { color: #f97316; }   /* Cam (orange-500) */
            100% { color: #0ea5e9; }   /* Quay lại xanh dương */
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

        .logoTD {
            display: flex;
            height: 80px;
            width: 80px;
            align-items: center;
            animation: rotateYSoft 7s linear infinite, glowPulse 2.5s ease-in-out infinite;
            transform-style: preserve-3d;
            backface-visibility: visible; /* <== không cho logo biến mất khi quay nghiêng */
            transition: transform 0.4s ease-in-out, filter 0.4s ease-in-out;
            filter: drop-shadow(0 0 6px rgba(0, 255, 255, 0.4));
        }

        /* Hover: zoom nhẹ + nghiêng */
        .logoTD:hover {
            transform: scale(1.1) rotateY(15deg);
            filter: drop-shadow(0 0 14px rgba(0, 255, 255, 0.8));
        }

        /* Xoay quanh trục Y liên tục nhưng không mất hình */
        @keyframes rotateYSoft {
            0%   { transform: rotateY(0deg); }
            25%  { transform: rotateY(-180deg); }
            50%  { transform: rotateY(0deg); }
            75%  { transform: rotateY(180deg); }
            100% { transform: rotateY(0deg); }
        }

        /* Glow mượt */
        @keyframes glowPulse {
            0%, 100% {
                filter: drop-shadow(0 0 6px rgba(0, 255, 255, 0.4));
            }
            50% {
                filter: drop-shadow(0 0 14px rgba(0, 255, 255, 0.8));
            }
        }

        .input-container {
    position: relative;
    width: 260px;
}

.input-field {
    width: 100%;
    padding: 10px 36px 10px 36px; /* chừa cả trái (search) và phải (mic) */
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    transition: 0.3s ease;
    box-shadow: 0 0 0 rgba(0, 123, 255, 0);
}

.input-field:focus {
    border-color: #2196f3;
    box-shadow: 0 0 6px rgba(33, 150, 243, 0.5);
    background-color: #f0faff;
}

.icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #2196f3;
    font-size: 16px;
}

.mic-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #333;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.mic-icon:hover {
    color: #e53935;
}

.mic-icon.listening i {
    color: #e53935;
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


    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <nav class="bg-white px-2 py-0 shadow-md">
    <div class="flex flex-col md:flex-row items-center md:items-center md:justify-between">
        <div class="flex items-center space-x-2 animate-fade-in">
            <img src="picture/logoshop.png" alt="Logo Cửa Hàng"
                class="logoTD cursor-pointer"
                id="logoTrigger"
                onclick="toggleMenu()">
                <span class="store-name hidden md:flex font-semibold text-blue-700 tracking-wide items-center gap-1">
                MONKEY STORE
            </span>
        </div>
    <style>
        /* Responsive */
        @media (max-width: 768px) {
            .timkiem {
                padding-bottom: 10px;
                padding-top: 0;
            }
        }
    </style>
        <!-- Search Bar -->
        <div class="timkiem">
            <div class="flex items-center space-x-2 w-full ">
                <div class="input-container">
                    <i class="fas fa-search icon"></i>
                    <input type="text" placeholder="Tìm kiếm" class="input-field search-bar" onkeyup="handleSearch(this.value)"     
                    aria-label="Tìm kiếm sản phẩm, người dùng hoặc đơn hàng" style="color: black">
                
                <button id="mic-btn" 
                    class="mic-icon mic-btn" 
                    aria-label="Tìm kiếm bằng giọng nói">
                    <i class="fas fa-microphone mr-2"></i>
                    <span class="mic-status hidden text-sm text-red-600"> Đang <br> lắng <br> nghe...</span>
                </button>
                </div>
            </div>
        </div>                   
        <div id="search-results" class="mt-2 w-full md:w-64 bg-white text-gray-800 rounded-md shadow-lg hidden"></div>
    </div>
    </nav>
    
<style>
    #sidebar {
    width: 270px;
    background: rgba(30, 41, 59, 0.4); /* màu xanh xám đậm, 60% độ mờ */
    backdrop-filter: blur(2px); 
    -webkit-backdrop-filter: blur(12px); /* Safari support */
    color: white;
    border-right: 1px solid rgba(255, 255, 255, 0.1); /* viền mờ */
    }
</style>

    <!-- Sidebar Menu -->
<nav id="sidebar" class="bg-[#2d3748] text-white fixed top-0 left-0 h-full transition-all duration-300 z-50 -translate-x-full" aria-label="Menu điều hướng">

<style>
    .menusidebar {
        padding: 16px 16px 0 16px;
    }
</style>
<!-- Tiêu đề Sidebar -->
<div class="menusidebar flex items-center justify-between">
    <h2 class="text-xl font-semibold">Menu</h2>
    <button id="sidebarClose" class="hamburger w-6 h-5 relative" aria-label="Đóng menu" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </button>
</div>
<!-- Danh sách menu -->
<div class="flex flex-col p-4">
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <button 
        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-600 text-white rounded-xl  transition duration-200 w-full md:w-auto"
            onclick="clearLocalStorage()" 
            aria-label="Xóa mô hình TensorFlow">
            <i class="fas fa-eraser mr-1"></i> Model
        </button>
    <?php endif; ?>

    <button 
        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-600 text-white rounded-xl  transition duration-200 w-full md:w-auto"            
        onclick="handleHomeClick()" 
        aria-label="Quay về trang chủ">
        <i class="fas fa-home mr-1"></i> Trang chủ
    </button>

    <!-- Thông báo -->
    <?php
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM yeucaudonhang WHERE trangthai = 0");
        $stmt->execute();
        $thongbaoCount = (int) $stmt->fetchColumn();
    ?>
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <button 
            class="relative flex items-center gap-2 px-4 py-2 hover:bg-gray-600 text-white rounded-xl transition duration-200 w-full md:w-auto"                
            id="menu-tb" 
            onclick="loadThongBao()" 
            aria-label="Xem thông báo">
            <i class="fas fa-bell mr-1"></i> Thông báo
            <?php if ($thongbaoCount > 0): ?>
                <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-2 py-1" aria-label="Số lượng thông báo chưa xem">
                    <?= $thongbaoCount ?>
                </span>
            <?php endif; ?>
        </button>
    <?php endif; ?>
</div>

<!-- Tiêu đề Sidebar -->
<div class="flex items-center justify-between">
    <h2 class="menusidebar text-xl font-semibold">Quản lý các tiện ích</h2>
</div>

<!-- Danh sách menu -->
<div class="flex flex-col p-4">

    <!-- Người dùng -->
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-user" onclick="loadDLUser()" aria-label="Quản lý người dùng">
            <i class="fas fa-users mr-2"></i> Quản lý người dùng
        </div>
    <?php else: ?>
        <div class="menu-item p-2 m-0 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-user" onclick="loadDLUser()" aria-label="Quản lý tài khoản cá nhân">
            <i class="fas fa-users mr-2"></i> Quản lý tài khoản cá nhân
        </div>
    <?php endif; ?>

    <!-- Sản phẩm -->
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-product" onclick="loadDLSanpham()" aria-label="Quản lý sản phẩm">
            <i class="fas fa-box mr-2"></i> Quản lý sản phẩm
        </div>
    <?php else: ?>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-product" onclick="loadDLSanpham()" aria-label="Quản lý mua sắm">
            <i class="fas fa-box mr-2"></i> Quản lý mua sắm
        </div>
    <?php endif; ?>

    <!-- Danh mục & Khuyến mãi (Chỉ admin) -->
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-category" onclick="loadDLDanhmuc()" aria-label="Quản lý danh mục">
            <i class="fas fa-list mr-2"></i> Quản lý danh mục
        </div>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-discount" onclick="loadDLMGG()" aria-label="Quản lý khuyến mãi">
            <i class="fas fa-tags mr-2"></i> Quản lý khuyến mãi
        </div>
    <?php endif; ?>

    <!-- Đơn hàng -->
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-order" onclick="loadDLDonhang()" aria-label="Quản lý đơn hàng">
            <i class="fas fa-chart-bar mr-2"></i> Quản lý đơn hàng
        </div>
    <?php else: ?>
        <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-order" onclick="loadDLDonhang()" aria-label="Quản lý đơn mua hàng">
            <i class="fas fa-chart-bar mr-2"></i> Quản lý đơn mua hàng
        </div>
    <?php endif; ?>

    <!-- Giỏ hàng & Hỗ trợ -->
    <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-gh" onclick="loadGH()" aria-label="Giỏ hàng">
        <i class="fas fa-shopping-cart mr-2"></i> Giỏ hàng
    </div>
    <div class="menu-item p-2 rounded-md hover:bg-gray-600 cursor-pointer" id="menu-support" onclick="loadPhanHoi()" aria-label="Hỗ trợ khách hàng">
        <i class="fas fa-headset mr-2"></i> Hỗ trợ khách hàng
    </div>
</div>
<style>
    .menutaikhoan {
        padding: 0 0 0 8px;
    }
    /* Phần tử cha của dropdown phải có position: relative */
.relative {
    position: relative;
}

/* Định vị dropdown */
#adminDropdown {
    position: absolute;
    right: 0;     
    left: 105%;
    bottom: 50%;
    background-color: white;
    color:rgb(255, 255, 255);
    border-radius: 10px;
    width: 200px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);

    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;

    background: rgba(30, 41, 59, 0.6);         /* Nền xanh nhạt trong suốt */
    border-right: 1px solid rgba(255, 255, 255, 0.1);  /* Viền nhẹ bên phải */
    border-radius: 16px;                          /* Bo góc mềm mại */
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);     /* Đổ bóng nhẹ */
    z-index: 10;
}

/* Khi active */
#adminDropdown.dropdown-active {
        opacity: 1;
    visibility: visible;
    transform: translateY(0);

    animation: dropdownFadeIn 0.8s ease forwards;
}
@keyframes dropdownFadeIn {
    0% {
        opacity: 0;
        transform: translateX(-10px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}
.btndx {
    transition: all 0.3s ease; /* Mượt mà */
}

.btndx:hover {
    color: white;
    transform: scale(1.05); 
    text-shadow: 0 4px 15px rgb(0, 255, 13); /* Đổ bóng đỏ */
}
</style>


<!-- Danh sách menu -->
<div class="menutaikhoan flex flex-col">
    <div class="relative w-full md:w-auto">
        <button 
            class="taikhoan flex items-center gap-2 px-3 py-2 font-medium rounded-md hover:bg-gray-600"
            onclick="ddadmin()" 
            aria-label="Tài khoản admin" 
            aria-expanded="false">
            <img src="<?= $admin_avatar ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-blue-200 shadow-sm">
            <span class="text-white font-sans"><?= !empty($admin_name) ? $admin_name : "Admin"; ?></span>
        </button>

        <div 
            id="adminDropdown"
            class="dropdowntk absolute bg-white text-gray-800 hidden z-50 animate-fade-in"
        >
            <div class="p-4 border-b text-center">
                <img src="<?= $admin_avatar ?>" alt="Avatar admin"
                    class="w-20 h-20 rounded-full mx-auto shadow-md border-4 border-indigo-200 hover:scale-105 transition transform"
                    id="menu-ttcn"
                    onclick="taikhoancn()" 
                    aria-label="Xem thông tin cá nhân">
                <p class="font-bold mt-3 text-lg"><?= $admin_name ?></p>
                <p class="text-sm "><?= $admin_phone ?></p>
                <p class="text-sm italic"><?= $admin_email ?></p>
            </div>
            <div 
                class="btndx p-3 text-center font-medium cursor-pointer"
                onclick="logout()" 
                aria-label="Đăng xuất">
                <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
            </div>
        </div>
    </div> 
</div>
<script>
    function ddadmin() {
        const dropdown = document.getElementById("adminDropdown");
        const taikhoanBtn = document.querySelector(".taikhoan");

        const isHidden = !dropdown.classList.contains("dropdown-active");

        dropdown.classList.toggle("dropdown-active", isHidden);
        dropdown.classList.toggle("hidden", !isHidden);

        taikhoanBtn.setAttribute("aria-expanded", isHidden);
    }
    
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById("adminDropdown");
        const trigger = document.querySelector(".taikhoan");

        if (
            !dropdown.contains(event.target) &&
            !trigger.contains(event.target)
        ) {
            dropdown.classList.remove("dropdown-active");
            trigger.setAttribute("aria-expanded", "false");
        }
    });
</script>
</nav>
<style>
    .main-content{
        padding: 0;
        bottom: -100px; 
    }
    .chan {
        margin: 0;
    }
</style>
    <!-- Main Content -->
    <div class="main-content flex-1 p-4 transition-all duration-300" id="main-content">
        <iframe id="Frame" src="" 
            class="frame w-full h-[calc(100vh-136px)] md:h-[calc(100vh-136px)] border-none" 
            scrolling="auto" aria-label="Nội dung chính">
        </iframe>
    </div>

    <footer class="bg-white border-t border-gray-300 text-gray-700 px-4 py-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center space-y-6 md:space-y-0">
            <!-- Bên trái -->
            <div class="text-center md:text-left text-sm">
                <span>© 2025 M'K STORE - All rights reserved.</span>
            </div>

            <!-- Ở giữa -->
            <div class="items-center md:items-start text-sm space-y-1 space-x-4">
                <span><i class="fas fa-map-marker-alt mr-2"></i>Địa chỉ: 73 Nguyễn Huệ, P.2, TP. Vĩnh Long</span>
                <span><i class="fas fa-phone mr-2"></i>0702 8045 94</span>
            </div>

            <!-- Bên phải -->
            <div class="flex gap-4 text-sm">
                <a href="#" class="text-blue-600 hover:underline" aria-label="Zalo">
                    <i class="fab fa-facebook-messenger mr-1"></i>Zalo
                </a>
                <a href="#" class="text-blue-600 hover:underline" aria-label="Facebook">
                    <i class="fab fa-facebook mr-1"></i>Facebook
                </a>
            </div>
        </div>
    </footer>



    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'cnuserT') {
                echo "<script>showCustomAlert('Thành Công!', 'Thông tin đã được cập nhật.', 'picture/success.png');</script>";
            } 
            else if ($_GET['status'] === 'cnuserF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else if ($_GET['status'] === 'themuserT') {
                echo "<script>showCustomAlert('Thành Công!', 'Người dùng đã được thêm vào danh sách!', 'picture/success.png');</script>";
            } 
            else if ($_GET['status'] === 'themuserF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else if ($_GET['status'] === 'cnspT') {
                echo "<script>showCustomAlert('Thành Công!', 'Thông tin sản phẩm đã được cập nhật thành công!', 'picture/success.png');</script>";
            } 
            else if ($_GET['status'] === 'cnspF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else if ($_GET['status'] === 'themspT') {
                echo "<script>showCustomAlert('Thành Công!', 'Sản phẩm đã được thêm thành công!', 'picture/success.png');</script>";
            } 
            else if ($_GET['status'] === 'themspF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else if ($_GET['status'] === 'xoaspT') {
                echo "<script>showCustomAlert('Thành Công!', 'Xóa sản phẩm thành công!', 'picture/success.png');</script>";
            } 
            else if ($_GET['status'] === 'xoaspF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else if ($_GET['status'] === 'cnanhuserT') {
                echo "<script>showCustomAlert('Thành Công!', 'Cập nhật ảnh thành công!', 'picture/success.png');</script>";
            } 
            else if ($_GET['status'] === 'cnanhuserF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
        }
    ?>
    <!-- JavaScript -->
    <script>
        // Lấy products từ PHP
        const rawProducts = <?php echo json_encode($products); ?>;
        console.log("Raw Products:", rawProducts); // Debug
        const products = rawProducts.map(p => ({
            id: parseInt(p.idsp, 10) || 0,
            name: p.tensp || 'unknown',
            category: String(p.iddm ?? 'unknown')
        })).filter(p => p.id > 0);
        console.log("Processed Products:", products); // Debug

        // Kiểm tra nếu products rỗng (chỉ áp dụng cho menu-product)
        function checkEmptyProducts() {
            if (products.length === 0) {
                console.warn("No products found. Disabling TensorFlow.js search.");
                document.getElementById('search-results').innerHTML = '<div>Không có sản phẩm để tìm kiếm. Vui lòng thêm sản phẩm.</div>';
                return true;
            }
            return false;
        }

        // Chuẩn hóa từ khóa
        const keywordMapping = {
            'backpack': 'balo',
            'cặp': 'balo',
            'túi đeo lưng': 'balo',
            'túi đi học': 'balo',
            'ba lô': 'balo',
            'bag': 'túi',
            'túi xách': 'túi',
            'túi đeo': 'túi',
            'wallet': 'ví',
            'clutch': 'ví',
            'ví tiền': 'ví',
            'watch': 'đồng hồ',
            'đồng hồ đeo tay': 'đồng hồ',
            'đồng hồ thời trang': 'đồng hồ',
            'leather': 'da',
            'đồ da': 'da',
            'phụ kiện da': 'da',
            'eye': 'mắt',
            'mỹ phẩm mắt': 'mắt',
            'phấn mắt': 'mắt',
            'lip': 'môi',
            'son môi': 'môi',
            'mỹ phẩm môi': 'môi',
            'tool': 'dụng cụ',
            'phụ kiện': 'dụng cụ',
            'đồ dùng': 'dụng cụ',
            'necklace': 'vòng cổ',
            'dây chuyền': 'vòng cổ',
            'chuỗi ngọc': 'vòng cổ',
            'vongco': 'vòng cổ',
            'bracelet': 'vòng lắc',
            'vòng tay': 'vòng lắc',
            'lắc': 'vòng lắc',
            'ring': 'nhẫn',
            'nhẫn đính hôn': 'nhẫn',
            'nhẫn cưới': 'nhẫn',
            'nhẫn vàng 18k': 'nhẫn',
            'earring': 'bông tai',
            'khuyên tai': 'bông tai',
            'hoa tai': 'bông tai',
            'teddy bear': 'gấu bông',
            'gấu teddy': 'gấu bông',
            'thú nhồi bông': 'gấu bông'
        };

        // Tạo trainingData động với từ khóa phong phú (chỉ khi menu-product)
        let trainingData = [];
        let vocabulary = [];
        function prepareTrainingData() {
            trainingData = products.map(product => {
                console.log("Processing product:", product); // Debug
                const categoryKeywords = (product.category && product.category.trim() !== '') 
                    ? product.category.toLowerCase().split(/\s+/) 
                    : [];
                const productNameWords = product.name.toLowerCase().split(/\s+/).filter(word => !/^\d+$/.test(word));
                return {
                    keywords: [
                        ...productNameWords,
                        ...categoryKeywords,
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
            console.log("Training Data:", trainingData); // Debug

            // Tạo vocabulary
            vocabulary = products.length > 0 ? [...new Set(trainingData.flatMap(item => item.keywords))] : [];
            console.log("Vocabulary size:", vocabulary.length); // Debug
            console.log("Vocabulary:", vocabulary); // Debug

            // Kiểm tra và xóa mô hình cũ nếu vocabulary thay đổi
            const currentVocabulary = JSON.stringify(vocabulary);
            const savedVocabulary = localStorage.getItem('search-vocabulary');
            if (savedVocabulary !== currentVocabulary) {
                console.log("Vocabulary changed, clearing old model");
                localStorage.removeItem('search-model');
                localStorage.setItem('search-vocabulary', currentVocabulary);
            }
        }

        function keywordsToVector(keywords) {
            const vector = vocabulary.map(word => keywords.includes(word) ? 1 : 0);
            console.log("Input vector size:", vector.length); // Debug
            console.log("Input vector:", vector); // Debug
            return vector;
        }

        let xs = null;
        let ys = null;
        function prepareTrainingTensors() {
            xs = products.length > 0 ? tf.tensor2d(trainingData.map(item => keywordsToVector(item.keywords))) : null;
            ys = products.length > 0 ? tf.tensor2d(trainingData.map(item => [item.productId]), [trainingData.length, 1]) : null;
        }

        async function trainModel() {
            if (products.length === 0) {
                console.warn("Cannot train model: No products available.");
                return null;
            }

            console.log("Training new model with vocabulary size:", vocabulary.length);
            const model = tf.sequential();
            model.add(tf.layers.dense({
                units: 32, // Tăng số units
                activation: 'relu',
                inputShape: [vocabulary.length]
            }));
            model.add(tf.layers.dense({
                units: 16, // Thêm tầng ẩn
                activation: 'relu'
            }));
            model.add(tf.layers.dense({
                units: Math.max(...products.map(p => p.id)) + 1 || 1,
                activation: 'softmax'
            }));

            model.compile({
                optimizer: tf.train.adam(0.005), // Giảm learning rate
                loss: 'sparseCategoricalCrossentropy',
                metrics: ['accuracy']
            });

            await model.fit(xs, ys, {
                epochs: 150, // Tăng số epoch
                verbose: 0,
                callbacks: {
                    onEpochEnd: (epoch, logs) => {
                        console.log(`Epoch ${epoch}: loss = ${logs.loss}, accuracy = ${logs.acc}`);
                    }
                }
            });

            await model.save('localstorage://search-model');
            console.log("Model saved");
            return model;
        }

        async function loadModel() {
            if (products.length === 0) {
                console.warn("Cannot load model: No products available.");
                return null;
            }

            try {
                console.log("Loading model...");
                const model = await tf.loadLayersModel('localstorage://search-model');
                const expectedShape = model.layers[0].input.shape[1];
                if (expectedShape !== vocabulary.length) {
                    console.log("Input shape mismatch, retraining model");
                    localStorage.removeItem('search-model');
                    return await trainModel();
                }
                return model;
            } catch (e) {
                console.error("Error loading model:", e);
                return await trainModel();
            }
        }

        async function predictProduct(searchQuery) {
            if (checkEmptyProducts()) {
                console.warn("Cannot predict: No products available.");
                return null;
            }

            // Chuẩn hóa query trước khi dự đoán
            let normalizedQuery = searchQuery.toLowerCase();
            for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                if (normalizedQuery.includes(keyword)) {
                    normalizedQuery = normalizedQuery.replace(keyword, mapped);
                }
            }
            const keywords = normalizedQuery.split(/\s+/);
            console.log("Normalized query keywords:", keywords); // Debug

            const inputVector = tf.tensor2d([keywordsToVector(keywords)]);
            const model = await loadModel();
            if (!model) return null;
            const prediction = model.predict(inputVector);
            const predictedId = tf.argMax(prediction, axis=1).dataSync()[0];
            console.log("Predicted ID:", predictedId); // Debug
            const product = products.find(p => p.id === predictedId) || null;
            console.log("Predicted product:", product); // Debug

            // Fallback nếu dự đoán không chính xác
            if (!product) {
                for (let keyword of keywords) {
                    const matchedProduct = products.find(p => p.name.toLowerCase().includes(keyword));
                    if (matchedProduct) {
                        console.log("Fallback: Found product with keyword", keyword, ":", matchedProduct);
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
                    // Chuẩn bị dữ liệu huấn luyện và mô hình chỉ khi ở menu-product
                    prepareTrainingData();
                    prepareTrainingTensors();
                    if (query) {
                        if (checkEmptyProducts()) {
                            console.log("Đang tìm kiếm theo key (query) khi (products.length === 0): " + query);
                            searchProducts(query);
                            return;
                        }
                        
                        try {
                            const predictedProduct = await predictProduct(query);
                            if (predictedProduct) {
                                resultsDiv.innerHTML = `
                                    <div class="search-result p-2 bg-gray-100 rounded-md">
                                        <span>${predictedProduct.name}</span>
                                    </div>
                                `;
                                // Truyền từ khóa chuẩn hóa thay vì tên sản phẩm
                                let normalizedQuery = query.toLowerCase();
                                for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                                    if (normalizedQuery.includes(keyword)) {
                                        normalizedQuery = normalizedQuery.replace(keyword, mapped);
                                        break;
                                    }
                                }
                                console.log("Đang tìm kiếm theo kết quả dự đoán: " + normalizedQuery);
                                searchProducts(normalizedQuery);
                            } else {
                                resultsDiv.innerHTML = '<div class="p-2 bg-gray-100 rounded-md">Không tìm thấy sản phẩm</div>';
                                console.log("Đang tìm kiếm theo key (query) khi (predictedProduct là false): " + query);
                                searchProducts(query);
                            }
                        } catch (e) {
                            console.error("Error in handleSearch:", e);
                            resultsDiv.innerHTML = '<div class="p-2 bg-gray-100 rounded-md">Lỗi khi tìm kiếm. Vui lòng thử lại.</div>';
                            console.log("Đang tìm kiếm theo key (query): " + query);
                            searchProducts(query);
                        }
                    } else {
                        console.log("Đang tìm kiếm theo key (query) khi lỗi (predictedProduct): " + query);
                        searchProducts(query);
                    }
                } else {
                    if (activeMenu === "menu-user") searchUsers(query);
                    if (activeMenu === "menu-order") searchDonHang(query);
                    if (activeMenu === "menu-support") searchPhanHoi(query);
                }
            }, 300);
        }

        function searchUsers(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "nguoidung/hienthinguoidung.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (users):", iframe.src); // Debug
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
                }
            }, 100);
        }

        function searchProducts(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "sanpham/hienthisanpham.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (products):", iframe.src); // Debug
                    iframe.onload = function() {
                        console.log("Iframe loaded successfully");
                        if (iframe.contentDocument && iframe.contentDocument.body.innerHTML.trim() === "") {
                            console.warn("Iframe content is empty");
                            iframe.contentDocument.body.innerHTML = '<div class="no-data-message p-4 text-gray-500">Không có sản phẩm nào để hiển thị.</div>';
                        }
                    };
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
                }
            }, 100);
        }

        function searchDonHang(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "donhang/hienthidonhang.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (orders):", iframe.src); // Debug
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
                }
            }, 100);
        }

        function searchPhanHoi(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "phanhoi/hienthiphanhoi.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (support):", iframe.src); // Debug
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
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


        // Toggle Menu for All Devices
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const hamburger = document.querySelector('.hamburger');
            const isExpanded = sidebar.classList.contains('translate-x-0');

            if (isExpanded) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                hamburger.classList.remove('active');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                hamburger.classList.add('active');
            }

            hamburger.setAttribute('aria-expanded', !isExpanded);
            localStorage.setItem('sidebarStateadmin', isExpanded ? 'collapsed' : 'expanded');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.querySelector('.hamburger');
            const logo = document.getElementById('logoTrigger');
            const dropdown = document.getElementById("adminDropdown");
            const trigger = document.querySelector(".taikhoan");

            // Ẩn sidebar khi chuột rời sidebar
            sidebar.addEventListener('mouseleave', () => {
                if (sidebar.classList.contains('translate-x-0')) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    hamburger.classList.remove('active');
                    hamburger.setAttribute('aria-expanded', 'false');
                    localStorage.setItem('sidebarStateadmin', 'collapsed');
                    dropdown.classList.remove("dropdown-active");
                    trigger.setAttribute("aria-expanded", "false");
                }
            });

            // Hiện lại sidebar khi rê chuột vào logo
            logo.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    hamburger.classList.add('active');
                    hamburger.setAttribute('aria-expanded', 'true');
                    localStorage.setItem('sidebarStateadmin', 'expanded');
                }
            });
        });


        // Handle Home Click
        function handleHomeClick() {
            goBackHome();
            localStorage.setItem('homeButtonClicked', 'true');
        }

        // Load Profile
        function taikhoancn() {
            loadTaiKhoanCN();
            localStorage.setItem('profileMenuClicked', 'true');
            localStorage.removeItem("activeMenu");
            localStorage.removeItem("homeButtonClicked");
            document.querySelectorAll(".menu-item").forEach(item => {
                item.classList.remove("active");
            });
        }

        // Handle Session Timeout
        handleSessionTimeout(<?= SESSION_TIMEOUT ?>);

        // Load Page State
        window.addEventListener('load', function() {
            if (localStorage.getItem('homeButtonClicked') === 'true') {
                goBackHome();
            }
            if (localStorage.getItem('profileMenuClicked') === 'true') {
                loadTaiKhoanCN();
            }
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const hamburger = document.querySelector('.hamburger');
            const savedState = localStorage.getItem('sidebarStateadmin');
            if (savedState === 'expanded') {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                hamburger.classList.add('active');
                hamburger.setAttribute('aria-expanded', 'true');
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                hamburger.classList.remove('active');
                hamburger.setAttribute('aria-expanded', 'false');
            }

            activateMenu();
            setTimeout(() => {
                let id = getActiveMenu();
                if (id === "menu-user") {
                    loadDLUser(); 
                    localStorage.removeItem('profileMenuClicked');
                } else if (id === "menu-product") {
                    loadDLSanpham();
                    localStorage.removeItem('profileMenuClicked');
                } else if (id === "menu-category") {
                    loadDLDanhmuc(); 
                    localStorage.removeItem('profileMenuClicked');
                } else if (id === "menu-order") { 
                    loadDLDonhang();
                    localStorage.removeItem('profileMenuClicked');
                } else if (id === "menu-discount") {
                    loadDLMGG();
                    localStorage.removeItem('profileMenuClicked');
                } else if (id === "menu-support") { 
                    loadPhanHoi();
                    localStorage.removeItem('profileMenuClicked');
                } else if (id === "menu-gh") {
                    loadGH();
                    localStorage.removeItem('profileMenuClicked');
                }
            }, 100);
        });
    </script>
</body>
</html>