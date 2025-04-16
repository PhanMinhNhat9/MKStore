<?php
require_once '../config.php';
$pdo = connectDatabase();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$sql = "
    SELECT iddh, idkh, tenkh, tongtien, trangthai, phuongthuctt, thoigian 
    FROM donhang 
    WHERE 1
";

// Tìm kiếm theo query
$params = [];
if ($query !== '') {
    $sql .= " AND (tenkh LIKE :searchTerm OR idkh LIKE :searchTerm1)";
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
    <title>Quản Lý Đơn Hàng - Hệ Thống Bán Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hienthidonhang.css?v=<?= time(); ?>">
</head>
<body>
    <div class="sidebar">
        <h1>Hệ Thống Bán Hàng</h1>
        <label for="statusFilter">Lọc theo trạng thái:</label>
        <form id="filterForm" method="GET" action="">
            <select id="statusFilter" name="status" onchange="document.getElementById('filterForm').submit()">
                <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="Chờ xác nhận" <?= $status == 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                <option value="Đã xác nhận" <?= $status == 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                <option value="Đã thanh toán" <?= $status == 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                <option value="Chưa thanh toán" <?= $status == 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                <option value="Hủy đơn" <?= $status == 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
            </select>
        </form>
    </div>

    <div class="main-content">
        <div class="table-container">
            <div class="table-body-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Mã KH</th>
                            <th>Tên KH</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr data-status="<?= $order['trangthai'] ?>">
                            <td><?= $order['iddh'] ?></td>
                            <td>
                                <form action="update_idkh.php" method="POST">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <input type="text" name="idkh" value="<?= $order['idkh'] ?>">
                                    <button type="submit"><i class="fas fa-save text-blue"></i></button>
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
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <input type="text" name="tenkh" value="<?= htmlspecialchars($tenkh) ?>" placeholder="Tên KH">
                                    <button type="submit"><i class="fas fa-save text-green"></i></button>
                                </form>
                            </td>
                            <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                            <td>
                                <form action="update_trangthai.php" method="POST">
                                    <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                    <select name="trangthai" onchange="this.form.submit()">
                                        <option value="Chờ xác nhận" <?= $order['trangthai'] == 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                                        <option value="Đã xác nhận" <?= $order['trangthai'] == 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                                        <option value="Đã thanh toán" <?= $order['trangthai'] == 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                                        <option value="Chưa thanh toán" <?= $order['trangthai'] == 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                                        <option value="Hủy đơn" <?= $order['trangthai'] == 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= $order['thoigian'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($orders) === 0): ?>
                        <tr>
                            <td colspan="6" class="no-data">Không có đơn hàng nào phù hợp.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
