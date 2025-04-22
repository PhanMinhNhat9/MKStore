<?php
require_once '../config.php';
$pdo = connectDatabase();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$sql = "
    SELECT iddh, sdt, tenkh, tongtien, trangthai, phuongthuctt, thoigian 
    FROM donhang 
    WHERE 1
";

// Tìm kiếm theo query
$params = [];
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
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="hienthidonhang.css">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../trangchuadmin.js"></script>
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
                                    <button 
                                        type="submit" 
                                        class="btn btn-update" 
                                        aria-label="Lưu mã khách hàng"
                                    >
                                        <i class="fas fa-save"></i>
                                    </button>
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
                                    <button 
                                        type="submit" 
                                        class="btn btn-edit" 
                                        aria-label="Lưu tên khách hàng"
                                    >
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                            <td>
                                <form action="update_trangthai.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <select name="trangthai" onchange="this.form.submit()" aria-label="Trạng thái đơn hàng">
                                        <option value="Chờ xác nhận" <?= $order['trangthai'] == 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                                        <option value="Đã xác nhận" <?= $order['trangthai'] == 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                                        <option value="Đã thanh toán" <?= $order['trangthai'] == 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                                        <option value="Chưa thanh toán" <?= $order['trangthai'] == 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                                        <option value="Hủy đơn" <?= $order['trangthai'] == 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($order['thoigian']) ?></td>
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
    </script>
</body>
</html>
