<?php
require_once 'config.php';

$pdo = connectDatabase();

if (!isset($_SESSION['user'])) {
    header("Location: GUI&dangnhap.php");
    exit();
}

$idkh = $_SESSION['user']['iduser'];

// Số đơn hàng trên mỗi trang
$ordersPerPage = 10;

// Trang hiện tại
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán offset
$offset = ($currentPage - 1) * $ordersPerPage;

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT iddh) FROM donhang WHERE idkh = ?");
$stmt->execute([$idkh]);
$totalOrders = $stmt->fetchColumn();
$totalPages = ceil($totalOrders / $ordersPerPage);

// Lấy danh sách đơn hàng và sản phẩm với phân trang
$stmt = $pdo->prepare("
    SELECT dh.iddh, dh.tongtien, dh.trangthai, dh.thoigian, sp.tensp, sp.anh, ctdh.soluong, ctdh.gia
    FROM donhang dh
    JOIN chitietdonhang ctdh ON dh.iddh = ctdh.iddh
    JOIN sanpham sp ON ctdh.idsp = sp.idsp
    WHERE dh.idkh = ?
    ORDER BY dh.thoigian DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$idkh, $ordersPerPage, $offset]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .order-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .order-container h2 {
        text-align: center;
        font-size: 28px;
        color: #1f2937;
        margin-bottom: 25px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 20px;
    }

    .order-table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-table th,
    .order-table td {
        padding: 14px;
        text-align: center;
        border-bottom: 1px solid #e0e0e0;
        font-size: 16px;
    }

    .order-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        color: #444;
    }

    .order-table td {
        color: #555;
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .no-orders {
        text-align: center;
        font-size: 20px;
        color: #888;
        padding: 30px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        gap: 5px;
    }

    .pagination a {
        text-decoration: none;
        color: #007BFF;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .pagination a:hover {
        background-color: #007BFF;
        color: white;
    }

    .pagination a.active {
        background-color: #007BFF;
        color: white;
        border-color: #007BFF;
    }

    .pagination .dots {
        padding: 8px 12px;
        color: #888;
    }

    .pagination .arrow {
        font-weight: bold;
    }
</style>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử mua hàng</title>
    <link rel="stylesheet" href="trangchucss.css">
    <link rel="stylesheet" href="giohang.css">
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
            <button class="btn giohang" onclick="window.location.href='giohang.php'">
                <i class="fas fa-shopping-cart"></i> Giỏ hàng
            </button>
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
        </div>
    </nav>

    <main class="order-container">
        <h2>Lịch sử mua hàng</h2>
        <?php if (count($orders) > 0): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentOrderId = null;
                    foreach ($orders as $order):
                        if ($currentOrderId !== $order['iddh']):
                            $currentOrderId = $order['iddh'];
                    ?>
                            <tr>
                                <td rowspan="<?php echo count(array_filter($orders, fn($o) => $o['iddh'] === $order['iddh'])); ?>">#<?php echo $order['iddh']; ?></td>
                                <td rowspan="<?php echo count(array_filter($orders, fn($o) => $o['iddh'] === $order['iddh'])); ?>"><?php echo date('d/m/Y H:i', strtotime($order['thoigian'])); ?></td>
                                <td>
                                    <img src="<?php echo $order['anh']; ?>" alt="<?php echo $order['tensp']; ?>" class="product-image">
                                    <br><?php echo $order['tensp']; ?>
                                </td>
                                <td><?php echo $order['soluong']; ?></td>
                                <td><?php echo number_format($order['gia'], 0, ',', '.'); ?> VNĐ</td>
                                <td rowspan="<?php echo count(array_filter($orders, fn($o) => $o['iddh'] === $order['iddh'])); ?>"><?php echo number_format($order['tongtien'], 0, ',', '.'); ?> VNĐ</td>
                                <td rowspan="<?php echo count(array_filter($orders, fn($o) => $o['iddh'] === $order['iddh'])); ?>"><?php echo htmlspecialchars($order['trangthai']); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $order['anh']; ?>" alt="<?php echo $order['tensp']; ?>" class="product-image">
                                    <br><?php echo $order['tensp']; ?>
                                </td>
                                <td><?php echo $order['soluong']; ?></td>
                                <td><?php echo number_format($order['gia'], 0, ',', '.'); ?> VNĐ</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a class="arrow" href="?page=<?= $currentPage - 1 ?>">« Trước</a>
                <?php endif; ?>

                <?php
                // Số lượng trang muốn hiển thị xung quanh trang hiện tại
                $range = 2;

                // Hiển thị trang 1 luôn
                if ($currentPage > $range + 2) {
                    echo '<a href="?page=1">1</a>';
                    echo '<span class="dots">...</span>';
                }

                // Các trang ở giữa
                for ($i = max(1, $currentPage - $range); $i <= min($totalPages, $currentPage + $range); $i++) {
                    $activeClass = ($i === $currentPage) ? 'active' : '';
                    echo '<a class="' . $activeClass . '" href="?page=' . $i . '">' . $i . '</a>';
                }

                // Hiển thị trang cuối nếu cách xa trang hiện tại
                if ($currentPage < $totalPages - ($range + 1)) {
                    echo '<span class="dots">...</span>';
                    echo '<a href="?page=' . $totalPages . '">' . $totalPages . '</a>';
                }
                ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a class="arrow" href="?page=<?= $currentPage + 1 ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-orders">Bạn chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </main>
</body>

</html>