<?php
require_once 'config.php'; // Kết nối cơ sở dữ liệu

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user']);

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
function getProducts() {
    $pdo = connectDatabase();
    $stmt = $pdo->query("SELECT * FROM sanpham");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$products = getProducts();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Phụ Kiện</title>
    <link rel="stylesheet" href="trangchucss.css"> <!-- Liên kết đến tệp CSS -->
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
            <!-- Nút Tài khoản -->
            <div style="position: relative;">
                <button class="btn taikhoan" onclick="toggleAccountDropdown()">
                    <i class="fas fa-user"></i> 
                    <?= $isLoggedIn ? htmlspecialchars($_SESSION['user']['hoten'], ENT_QUOTES, 'UTF-8') : "Tài khoản"; ?>
                </button>
                <?php if ($isLoggedIn): ?>
                <div class="dropdown" id="accountDropdown">
                    <div class="logout" onclick="logout()">Đăng xuất</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main>
        <h2>Sản Phẩm Nổi Bật</h2>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <img src="<?php echo $product['anh']; ?>" alt="<?php echo $product['tensp']; ?>">
                    <h3><?php echo $product['tensp']; ?></h3>
                    <p><?php echo $product['mota']; ?></p>
                    <p class="price">Giá: <?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</p>
                    <button class="buy-button">Mua Ngay</button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Cửa Hàng Phụ Kiện. Tất cả quyền được bảo lưu.</p>
    </footer>

    <script>
        function toggleAccountDropdown() {
            var dropdown = document.getElementById("accountDropdown");
            if (dropdown) {
                dropdown.classList.toggle("active");
            }
        }

        function logout() {
            window.location.href = "logout.php"; // Thay bằng trang xử lý đăng xuất
        }

        document.addEventListener("click", function(event) {
            var dropdown = document.getElementById("accountDropdown");
            var button = document.querySelector(".taikhoan");

            if (dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });
    </script>
</body>
</html>