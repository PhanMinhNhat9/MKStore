<?php
$tongtien = 0;
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

<style>
    .cart-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .cart-container h2 {
        text-align: center;
        font-size: 28px;
        color: #333;
        margin-bottom: 25px;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cart-table th,
    .cart-table td {
        padding: 14px;
        text-align: center;
        border-bottom: 1px solid #e0e0e0;
        font-size: 16px;
    }

    .cart-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        color: #444;
    }

    .cart-table td img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .quantity-input {
        width: 70px;
        height: 40px;
        padding: 5px;
        font-size: 18px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .action-buttons form {
        display: inline-block;
    }

    .action-buttons button {
        background-color: #ff4d4d;
        border: none;
        color: #fff;
        padding: 10px 16px;
        font-size: 14px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .action-buttons button:hover {
        background-color: #d63031;
    }

    .total-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #eee;
    }

    .total-text {
        font-size: 20px;
        font-weight: bold;
        color: #000;
    }

    .checkout-button {
        background-color: #2ecc71;
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .checkout-button:hover {
        background-color: #27ae60;
    }

    .no-cart {
        text-align: center;
        font-size: 20px;
        color: #888;
        padding: 30px;
    }

    .return-home {
        text-align: center;
        margin-top: 25px;
    }

    .return-home a {
        display: inline-block;
        background-color: #3498db;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .return-home a:hover {
        background-color: #2980b9;
    }
</style>

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

    <main class="cart-container">
    <h2 class="cart-title">Giỏ Hàng Của Bạn</h2>
    <form method="post" id="cart-form">
        <?php if (count($cartProducts) > 0): ?>
            <?php foreach ($cartProducts as $product): ?>
                <div class="cart-item" data-price="<?php echo $product['giaban']; ?>" data-idsp="<?php echo $product['idsp']; ?>">
                    <img src="<?php echo $product['anh']; ?>" alt="<?php echo $product['tensp']; ?>">
                    <div class="cart-details">
                        <h4><?php echo $product['tensp']; ?></h4>
                        <p>Đơn giá: <span class="unit-price"><?php echo number_format($product['giaban'], 0, ',', '.'); ?></span> VNĐ</p>
                        <label>Số lượng: 
                            <input type="number" class="quantity-input" name="quantities[<?php echo $product['idsp']; ?>]" value="<?php echo $product['quantity']; ?>" min="1">
                        </label>
                        <p>Tổng: <span class="item-total"><?php echo number_format($product['giaban'] * $product['quantity'], 0, ',', '.'); ?></span> VNĐ</p>
                    </div>
                    <div class="cart-actions">
                        <a href="giohang.php?delete=<?php echo $product['idsp']; ?>" class="btn delete-btn">Xóa</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-section">
                Tổng thanh toán: <span id="grand-total">0</span> VNĐ
            </div>

            <button type="submit" name="checkout" class="checkout-btn">Thanh toán</button>
        <?php else: ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php endif; ?>
    </form>
</main>

<script>
    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }

    function updateTotals() {
        let items = document.querySelectorAll('.cart-item');
        let grandTotal = 0;

        items.forEach(item => {
            let price = parseInt(item.getAttribute('data-price'));
            let quantityInput = item.querySelector('.quantity-input');
            let quantity = parseInt(quantityInput.value);
            let itemTotal = price * quantity;

            item.querySelector('.item-total').textContent = formatCurrency(itemTotal);
            grandTotal += itemTotal;
        });

        document.getElementById('grand-total').textContent = formatCurrency(grandTotal);
    }

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', () => {
            updateTotals();
            // Gửi AJAX để lưu session số lượng mới
            let formData = new FormData(document.getElementById('cart-form'));
            formData.append('update_cart', '1');
            fetch('giohang.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (!response.ok) console.error('Lỗi cập nhật giỏ hàng');
            });
        });
    });

    // Khởi động tính tổng khi trang tải
    updateTotals();
</script>

    <?php if (isset($_GET['thank_you'])): ?>
        <script>alert("Cảm ơn bạn đã đặt hàng! Đơn hàng đang được xử lý.");</script>
    <?php endif; ?>
</body>
</html>
