<?php
require_once 'config.php'; // Kết nối cơ sở dữ liệu

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
    <link rel="stylesheet" href="style.css"> <!-- Liên kết đến tệp CSS -->
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Cửa Hàng Phụ Kiện</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Trang Chủ</a></li>
                <li><a href="GUI&dangnhap.php">Đăng Nhập</a></li>
                <li><a href="GUI&quenMK.php">Quên Mật Khẩu</a></li>
            </ul>
        </nav>
    </header>

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
</body>
</html>