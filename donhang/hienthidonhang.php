<?php
    require_once '../config.php';
    $pdo = connectDatabase();
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    if ($query != '') {
        $sqlOrders = "SELECT iddh, idkh, tongtien, trangthai, phuongthuctt, thoigian 
                    FROM donhang 
                    WHERE iddh LIKE :searchTerm OR idkh LIKE :searchTerm1
                    ORDER BY thoigian DESC";
        $stmtOrders = $pdo->prepare($sqlOrders);
        $stmtOrders->execute(['searchTerm' => "%{$query}%", 'searchTerm1' => "%{$query}%"]);
    } else {
        $sqlOrders = "SELECT iddh, idkh, tongtien, trangthai, phuongthuctt, thoigian 
                    FROM donhang 
                    ORDER BY thoigian DESC";
        $stmtOrders = $pdo->prepare($sqlOrders);
        $stmtOrders->execute();
    }
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn Hàng</title>
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthidonhang.css?v=<?= time(); ?>">
</head>
<body>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['iddh'] ?></td>
                        <td><?= $order['idkh'] ?></td>
                        <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                        <td>
                            <form action="update_trangthai.php" method="POST">
                                <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                <select name="trangthai" class="status-select" onchange="this.form.submit()">
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
            </tbody>
        </table>
    </div>
</body>
</html>
