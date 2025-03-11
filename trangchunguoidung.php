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
            <button class="btn taikhoan"><i class="fas fa-user"></i> Tài khoản</button>
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
</body>
</html>