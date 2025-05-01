<?php
require_once '../config.php';
$pdo = connectDatabase();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($_SESSION['user']['quyen'] == 2589 || $_SESSION['user']['quyen'] == 0) {
    $sql = "
    SELECT iddh, sdt, tenkh, tongtien, trangthai, phuongthuctt, thoigian 
    FROM donhang 
";
    $params = [];
} else {
    $sql = "
    SELECT iddh, sdt, tenkh, tongtien, trangthai, phuongthuctt, thoigian 
    FROM donhang 
    WHERE sdt = :sdt
";
    $params = ['sdt' => $_SESSION['user']['sdt']];
}

// Tìm kiếm theo query

if ($query !== '') {
    $sql .= " AND (tenkh LIKE :searchTerm OR iddh LIKE :searchTerm1)";
    $params['searchTerm'] = "%$query%";
    $params['searchTerm1'] = "%$query%";
}

// Lọc theo trạng thái
if ($status !== 'all') {
    $sql .= " AND trangthai = :status";
    $params['status'] = $status;
}

$sql .= " ORDER BY thoigian DESC";
$stmtOrders = $pdo->prepare($sql);
$stmtOrders->execute($params);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// $sqluser = "SELECT `quyen` FROM `user` WHERE iduser = :iduser";
// $stmtuser = $pdo->prepare($sqluser);
// $stmtuser->execute([
//     'iduser' => $_SESSION['user']['iduser']
// ]);
// $user = $stmtuser->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="hienthidonhang.css?v=<?= time(); ?>">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-controls">
            <button class="hamburger" aria-label="Collapse Sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <h1>Hệ Thống Bán Hàng</h1>
        <form class="filter-form" id="filterForm" method="GET" action="">
            <label for="statusFilter">Lọc theo trạng thái:</label>
            <select id="statusFilter" name="status">
                <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="Chờ xác nhận" <?= $status == 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                <option value="Đã xác nhận" <?= $status == 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                <option value="Đã thanh toán" <?= $status == 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                <option value="Chưa thanh toán" <?= $status == 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                <option value="Đã gửi" <?= $status == 'Đã gửi' ? 'selected' : '' ?>>🚚 Đã gửi</option>
                <option value="Hủy đơn" <?= $status == 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
            </select>
        </form>
    </aside>
    <!-- Main Content -->
    <main class="container">
        <section class="table-container">
            <table class="order-table">
                <thead>
                    <tr>
                        <th scope="col">Mã ĐH</th>
                        <th scope="col">Mã KH</th>
                        <th scope="col">Tên KH</th>
                        <th scope="col">Tổng Tiền</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col">Ngày Đặt</th>
                        <th scope="col">Đánh Giá</th> <!-- New column -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr data-status="<?= htmlspecialchars($order['trangthai']) ?>">
                            <td><?= $order['iddh'] ?></td>
                            <td>
                                <form action="update_idkh.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <input 
                                        type="text" 
                                        name="idkh" 
                                        value="<?= htmlspecialchars($order['sdt']) ?>" 
                                        aria-label="Mã khách hàng"
                                    >
                                    <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                    <button 
                                        type="submit" 
                                        class="btn btn-update" 
                                        aria-label="Lưu mã khách hàng"
                                    >
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <?php
                                if (trim($order['tenkh']) === '') {
                                    $sqlUser = "SELECT hoten FROM user WHERE sdt = :sdt";
                                    $stmtUser = $pdo->prepare($sqlUser);
                                    $stmtUser->execute(['sdt' => $order['idkh']]);
                                    $tenkh = $stmtUser->fetchColumn() ?: '';
                                } else {
                                    $tenkh = $order['tenkh'];
                                }
                            ?>
                            <td>
                                <form action="update_tenkh.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <input 
                                        type="text" 
                                        name="tenkh" 
                                        value="<?= htmlspecialchars($tenkh) ?>" 
                                        placeholder="Tên KH" 
                                        aria-label="Tên khách hàng"
                                    >
                                    <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                    <button 
                                        type="submit" 
                                        class="btn btn-edit" 
                                        aria-label="Lưu tên khách hàng"
                                    >
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                            <td>
                            <?php if ($_SESSION['user']['quyen'] == 1): ?>
                                <form action="yeucauhuydon.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="idkh" value="<?= $_SESSION['user']['iduser'] ?>">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">

                                    <?php if ($order['trangthai'] == 'Hủy đơn' || $order['trangthai'] == 'Đã thanh toán'): ?>
                                        <select name="trangthai" onchange="this.form.submit()" aria-label="Trạng thái đơn hàng" disabled>
                                            <option value="<?= $order['trangthai'] ?>" selected disabled>
                                                <?= $order['trangthai'] ?> (Không thể chỉnh sửa)
                                            </option>
                                            <option value="Hủy đơn">🔴 Hủy đơn</option>                                        
                                        </select>
                                    <?php else: ?>
                                        <select name="trangthai" onchange="this.form.submit()" aria-label="Trạng thái đơn hàng">
                                            <option value="<?= $order['trangthai'] ?>" selected>
                                                <?= $order['trangthai'] ?> (Không thể chỉnh sửa)
                                            </option>
                                            <option value="Hủy đơn">🔴 Hủy đơn</option>                                        
                                        </select>
                                    <?php endif; ?>

                                </form>
                            <?php else: ?>
                                <!-- Admin hoặc người có quyền cao hơn -->
                                <form action="update_trangthai.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <select name="trangthai" onchange="this.form.submit()" aria-label="Trạng thái đơn hàng"
                                    <?= ($order['trangthai'] == 'Đã thanh toán' || $order['trangthai'] == 'Hủy đơn') ? 'disabled' : '' ?>
                                    >
                                        <option value="Chờ xác nhận" <?= $order['trangthai'] == 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                                        <option value="Đã xác nhận" <?= $order['trangthai'] == 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                                        <option value="Đã thanh toán" <?= $order['trangthai'] == 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                                        <option value="Chưa thanh toán" <?= $order['trangthai'] == 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                                        <option value="Đã gửi" <?= $order['trangthai'] == 'Đã gửi' ? 'selected' : '' ?>>🚚 Đã gửi</option>
                                        <option value="Hủy đơn" <?= $order['trangthai'] == 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
                                    </select>
                                </form>
                            <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($order['thoigian']) ?></td>
                            <td>
                                <?php if ($order['trangthai'] == 'Đã thanh toán'): ?>
                                    <?php if ($_SESSION['user']['quyen']==1): ?>
                                        <form action="../donhang/danhgia.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                            <input type="hidden" name="idkh" value="<?= $_SESSION['user']['iduser'] ?>">
                                            <button 
                                                type="submit" 
                                                class="btn btn-review" 
                                                aria-label="Đánh giá đơn hàng"
                                            >
                                                <i class="fas fa-star"></i> Đánh Giá
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="../donhang/chitietdanhgia.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                            <input type="hidden" name="idkh" value="<?= $_SESSION['user']['iduser'] ?>">
                                            <button 
                                                type="submit" 
                                                class="btn btn-review" 
                                                aria-label="Đánh giá đơn hàng"
                                            >
                                                <i class="fas fa-star"></i> Xem chi tiết
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($orders) === 0): ?>
                        <tr>
                            <td colspan="6" class="no-data">Không có đơn hàng nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- JavaScript -->
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            const isExpanded = !sidebar.classList.contains('collapsed');
            document.querySelector('.hamburger').setAttribute('aria-expanded', isExpanded);
        }
        // Auto-submit filter form on status change
        document.getElementById('statusFilter').addEventListener('change', () => {
            document.getElementById('filterForm').submit();
        });

        if (window.innerHeight > window.innerWidth) {
            alert("Vui lòngq xoay thiết bị sang chế độ ngang để xem đơn hàng.");
        }
    </script>
</body>
</html>
