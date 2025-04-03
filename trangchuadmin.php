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

    define('SESSION_TIMEOUT', 60);

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
    $admin_name = htmlspecialchars($_SESSION['user']['hoten'], ENT_QUOTES, 'UTF-8');
    $admin_email = htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8');
    $admin_phone = htmlspecialchars($_SESSION['user']['sdt'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="trangchuadmin.js"></script>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="trangchuadmin.css?v=<?= time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Thanh navbar -->
    <nav class="navbar">
        <div class="logo-container">
            <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="logo">
            <span class="store-name"> M'K STORE</span>
        </div>

        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Tìm kiếm..." onkeyup="handleSearch(this.value)">
            <button class="mic-btn"><i class="fas fa-microphone"></i></button>
            <button class="search-btn"> Tìm kiếm</button>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const micButton = document.querySelector(".mic-btn");
                const searchBar = document.querySelector(".search-bar");

                if (!('webkitSpeechRecognition' in window)) {
                    alert("Trình duyệt của bạn không hỗ trợ tìm kiếm bằng giọng nói.");
                } else {
                    const recognition = new webkitSpeechRecognition();
                    recognition.continuous = false;
                    recognition.interimResults = false;
                    recognition.lang = "vi-VN";

                    micButton.addEventListener("click", function() {
                        micButton.classList.add("listening"); // Thêm hiệu ứng khi mic hoạt động
                        recognition.start();
                    });

                    recognition.onresult = function(event) {
                        micButton.classList.remove("listening"); // Xóa hiệu ứng khi mic ngừng hoạt động
                        const speechResult = event.results[0][0].transcript;
                        searchBar.value = speechResult;
                        handleSearch(speechResult);
                    };

                    recognition.onerror = function(event) {
                        micButton.classList.remove("listening"); // Xóa hiệu ứng nếu có lỗi
                        console.error("Lỗi nhận dạng giọng nói: ", event.error);
                    };

                    recognition.onend = function() {
                        micButton.classList.remove("listening"); // Xóa hiệu ứng khi mic dừng hoạt động
                    };
                }
            });

            function handleSearch(query) {
                query = query.trim();
                let activeMenu = getActiveMenu(); // Lấy menu hiện tại

                if (activeMenu === "menu-user") {
                    searchUsers(query);
                }
                if (activeMenu === "menu-product") {
                    searchProducts(query);
                }
                if (activeMenu === "menu-order") {
                    searchDonHang(query);
                }
                if (activeMenu === "menu-support") {
                    searchPhanHoi(query);
                }
            }

            function searchUsers(query) {
                setTimeout(() => {
                    let iframe = document.getElementById("Frame");
                    if (iframe) {
                        iframe.src = "nguoidung/hienthinguoidung.php?query=" + encodeURIComponent(query);
                    } else {
                        console.error("Không tìm thấy iframe có ID 'Frame'");
                    }
                }, 100); // Đợi 100ms để đảm bảo iframe đã được render
            }

            function searchProducts(query) {
                setTimeout(() => {
                    let iframe = document.getElementById("Frame");
                    if (iframe) {
                        iframe.src = "sanpham/hienthisanpham.php?query=" + encodeURIComponent(query);
                    } else {
                        console.error("Không tìm thấy iframe có ID 'Frame'");
                    }
                }, 100); // Đợi 100ms để đảm bảo iframe đã được render
            }

            function searchDonHang(query) {
                setTimeout(() => {
                    let iframe = document.getElementById("Frame");
                    if (iframe) {
                        iframe.src = "donhang/hienthidonhang.php?query=" + encodeURIComponent(query);
                    } else {
                        console.error("Không tìm thấy iframe có ID 'Frame'");
                    }
                }, 100); // Đợi 100ms để đảm bảo iframe đã được render
            }

            function searchPhanHoi(query) {
                setTimeout(() => {
                    let iframe = document.getElementById("Frame");
                    if (iframe) {
                        iframe.src = "phanhoi/hienthiphanhoi.php?query=" + encodeURIComponent(query);
                    } else {
                        console.error("Không tìm thấy iframe có ID 'Frame'");
                    }
                }, 100); // Đợi 100ms để đảm bảo iframe đã được render
            }
        </script>

        <div class="nav-buttons">
            <!-- <button class="btn trangchu" onclick="window.location.href='admin_chat.php'">
                <i class="fas fa-home"></i> Trò chuyện
            </button> -->
            
            <button class="btn trangchu" onclick="goBackHome()"><i class="fas fa-home"></i> Trang chủ</button>
            <button class="btn thongbao"><i class="fas fa-bell"></i> Thông báo</button>
            <!-- Nút Admin với dropdown -->
            <div style="position: relative;">
                <button class="btn taikhoan" onclick="ddadmin()">
                    <i class="fas fa-user"></i> <?= !empty($admin_name) ? $admin_name : "Admin"; ?>
                </button>
                <div class="dropdowntk" id="adminDropdown">
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
        <div class="menu-item" id="menu-order" onclick="loadDLDonhang()"><i class="fas fa-chart-bar"></i> Quản lý đơn hàng</div>
        <div class="menu-item" id="menu-discount" onclick="loadDLMGG()"><i class="fas fa-tags"></i> Quản lý khuyến mãi</div>
        <div class="menu-item" id="menu-gh" onclick="loadGH()"><i class="fas fa-shopping-cart"></i> Giỏ hàng</div>
        <div class="menu-item" id="menu-support" onclick="loadPhanHoi()"><i class="fas fa-headset"></i> Hỗ trợ khách hàng</div>
    </nav>
    <script>
        activateMenu();
        setTimeout(() => {
            let id = getActiveMenu();
            if (id === "menu-user") loadDLUser();
            if (id === "menu-product") loadDLSanpham();
            if (id === "menu-category") loadDLDanhmuc();
            if (id === "menu-order") loadDLDonhang();
            if (id === "menu-discount") loadDLMGG();
            if (id === "menu-support") loadPhanHoi();
            if (id === "menu-gh") loadGH();
        }, 100); // Đợi 100ms để cập nhật menu
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

    <!-- Nội dung trang web -->
    <div class="main-content" id="main-content">
        <iframe id="Frame" src="" scrolling="no"></iframe>
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