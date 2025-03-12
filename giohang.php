<?php
require_once 'config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: GUI&dangnhap.php");
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $idsp = $_GET['delete'];
    unset($_SESSION['cart'][$idsp]);
    header("Location: giohang.php");
    exit();
}

// Xử lý cập nhật số lượng
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $idsp => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$idsp] = $quantity;
        } else {
            unset($_SESSION['cart'][$idsp]);
        }
    }
    header("Location: giohang.php");
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

    $stmt = $pdo->prepare("INSERT INTO donhang (idkh, tongtien, trangthai, phuongthuctt) VALUES (?, ?, 'Chờ xử lý', 'Tiền mặt')");
    $stmt->execute([$idkh, $tongtien]);
    $iddh = $pdo->lastInsertId();

    foreach ($cart as $idsp => $quantity) {
        $stmt = $pdo->prepare("INSERT INTO chitietdonhang (iddh, idsp, soluong, gia) VALUES (?, ?, ?, (SELECT giaban FROM sanpham WHERE idsp = ?))");
        $stmt->execute([$iddh, $idsp, $quantity, $idsp]);
    }

    unset($_SESSION['cart']);
    header("Location: giohang.php?thank_you=1");
    exit();
}

// Lấy thông tin sản phẩm trong giỏ
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
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="giohang.css">
    <link rel="stylesheet" href="trangchucss.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo-container">
            <img src="picture/logoTD.png" class="logo">
            <span class="store-name">M'K STORE</span>
        </div>
        <div class="nav-buttons">
            <button class="btn trangchu" onclick="window.location.href='trangchunguoidung.php'">
                <i class="fas fa-home"></i> Trang chủ
            </button>
        </div>
    </nav>

    <main>
        <h2>Giỏ Hàng Của Bạn</h2>
        <form method="post">
            <?php if (count($cartProducts) > 0): ?>
                <?php foreach ($cartProducts as $product): ?>
                    <div class="cart-item">
                        <img src="<?php echo $product['anh']; ?>" alt="<?php echo $product['tensp']; ?>">
                        <h3><?php echo $product['tensp']; ?></h3>
                        <p>Đơn giá: <?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</p>
                        <label>Số lượng: 
                            <input type="number" name="quantities[<?php echo $product['idsp']; ?>]" value="<?php echo $product['quantity']; ?>" min="1">
                        </label>
                        <p>Tổng: <?php echo number_format($product['giaban'] * $product['quantity'], 0, ',', '.'); ?> VNĐ</p>
                        <a href="giohang.php?delete=<?php echo $product['idsp']; ?>" class="btn">Xóa</a>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_cart" class="buy-button">Cập nhật giỏ hàng</button>
                <button type="submit" name="checkout" class="checkout-button">Thanh toán</button>
            <?php else: ?>
                <p>Giỏ hàng của bạn đang trống.</p>
            <?php endif; ?>
        </form>
    </main>

    <?php if (isset($_GET['thank_you'])): ?>
        <script>alert("Cảm ơn bạn đã đặt hàng! Đơn hàng đang được xử lý.");</script>
    <?php endif; ?>
</body>
</html>
