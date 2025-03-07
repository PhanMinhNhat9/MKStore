<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to the login page
    header("Location: GUI&dangnhap.php");
    exit();
}

// Get the username from the session
$username = $_SESSION['user']['tendn'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ Bán Phụ Kiện</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <header>
        <h1>Chào mừng đến với cửa hàng phụ kiện!</h1>
        <p>Xin chào, <?php echo htmlspecialchars($username); ?>!</p> <!-- Display the username -->
        <a href="logout.php">Đăng xuất</a> <!-- Link to logout -->
    </header>
    
    <main>
        <h2>Sản phẩm nổi bật</h2>
        <div class="product-list">
            <!-- Example product items -->
            <div class="product-item">
                <img src="path/to/image1.jpg" alt="Product 1">
                <h3>Tên sản phẩm 1</h3>
                <p>Giá: 100.000 VNĐ</p>
                <button>Mua ngay</button>
            </div>
            <div class="product-item">
                <img src="path/to/image2.jpg" alt="Product 2">
                <h3>Tên sản phẩm 2</h3>
                <p>Giá: 200.000 VNĐ</p>
                <button>Mua ngay</button>
            </div>
            <!-- Add more products as needed -->
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Cửa hàng phụ kiện. Tất cả quyền được bảo lưu.</p>
    </footer>
</body>
</html>