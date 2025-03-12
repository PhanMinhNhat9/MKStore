<?php
require_once 'config.php'; // Kết nối cơ sở dữ liệu

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    header("Location: GUI&dangnhap.php");
    exit();
}

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
function getProducts() {
    $pdo = connectDatabase();
    $stmt = $pdo->query("SELECT * FROM sanpham");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$products = getProducts();

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $idsp = $_POST['idsp'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$idsp])) {
        $_SESSION['cart'][$idsp]++;
    } else {
        $_SESSION['cart'][$idsp] = 1;
    }

    // Chuyển hướng về chính trang này để tránh việc gửi lại form khi refresh
    header("Location: trangchunguoidung.php");
    exit();
}

// Xử lý thanh toán
if (isset($_POST['checkout'])) {
    $pdo = connectDatabase();
    $cart = $_SESSION['cart'] ?? [];
    $idkh = $_SESSION['user']['iduser'];
    $tongtien = 0;

    foreach ($cart as $idsp => $quantity) {
        $stmt = $pdo->prepare("SELECT giaban FROM sanpham WHERE idsp = ?");
        $stmt->execute([$idsp]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $tongtien += $product['giaban'] * $quantity;
        }
    }

    // Tạo đơn hàng mới
    $stmt = $pdo->prepare("INSERT INTO donhang (idkh, tongtien, trangthai, phuongthuctt) VALUES (?, ?, 'Chờ xử lý', 'Tiền mặt')");
    $stmt->execute([$idkh, $tongtien]);

    // Lấy ID đơn hàng vừa tạo
    $iddh = $pdo->lastInsertId();

    // Thêm chi tiết đơn hàng
    foreach ($cart as $idsp => $quantity) {
        $stmt = $pdo->prepare("INSERT INTO chitietdonhang (iddh, idsp, soluong, gia) VALUES (?, ?, ?, (SELECT giaban FROM sanpham WHERE idsp = ?))");
        $stmt->execute([$iddh, $idsp, $quantity, $idsp]);
    }

    // Xóa giỏ hàng sau khi thanh toán
    unset($_SESSION['cart']);

    // Chuyển hướng về trang cảm ơn
    header("Location: trangchunguoidung.php?thank_you=1");
    exit();
}

// Lấy thông tin sản phẩm từ giỏ hàng
function getCartProducts() {
    $pdo = connectDatabase();
    $cart = $_SESSION['cart'] ?? [];
    $products = [];

    foreach ($cart as $idsp => $quantity) {
        $stmt = $pdo->prepare("SELECT * FROM sanpham WHERE idsp = ?");
        $stmt->execute([$idsp]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $quantity;
            $products[] = $product;
        }
    }

    return $products;
}

$cartProducts = getCartProducts();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Phụ Kiện</title>
    <link rel="stylesheet" href="trangchucss.css">
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
                    <?= htmlspecialchars($_SESSION['user']['hoten'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
                <div class="dropdown" id="accountDropdown">
                    <div class="logout" onclick="logout()">Đăng xuất</div>
                </div>
            </div>
        </div>
    </nav>

    <main>
    <h2>Giỏ Hàng</h2>
    <div class="cart-section">
        <div class="cart-list">
            <?php if (count($cartProducts) > 0): ?>
                <?php foreach ($cartProducts as $product): ?>
                    <div class="cart-item">
                        <img src="<?php echo $product['anh']; ?>" alt="<?php echo $product['tensp']; ?>">
                        <h3><?php echo $product['tensp']; ?></h3>
                        <p>Số lượng: <?php echo $product['quantity']; ?></p>
                        <p class="price">Giá: <?php echo number_format($product['giaban'] * $product['quantity'], 0, ',', '.'); ?> VNĐ</p>
                    </div>
                <?php endforeach; ?>
                <form action="trangchunguoidung.php" method="post" class="checkout-form">
                    <button type="submit" name="checkout" class="checkout-button">🛒 Thanh Toán Ngay</button>
                </form>
            <?php else: ?>
                <p>Giỏ hàng của bạn đang trống.</p>
            <?php endif; ?>
        </div>
    </div>

    <h2>Sản Phẩm Nổi Bật</h2>
    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="<?php echo $product['anh']; ?>" alt="<?php echo $product['tensp']; ?>">
                <h3><?php echo $product['tensp']; ?></h3>
                <p><?php echo $product['mota']; ?></p>
                <p class="price">Giá: <?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</p>
                <form action="trangchunguoidung.php" method="post">
                    <input type="hidden" name="idsp" value="<?php echo $product['idsp']; ?>">
                    <button type="submit" name="add_to_cart" class="buy-button">Thêm vào giỏ</button>
                </form>
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

<?php
// Hiển thị thông báo cảm ơn nếu thanh toán thành công
if (isset($_GET['thank_you'])) {
    echo "<script>alert('Cảm ơn bạn đã mua hàng! Đơn hàng của bạn đang được xử lý.');</script>";
}
?>