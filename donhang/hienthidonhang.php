<?php
require_once '../config.php';
$pdo = connectDatabase();
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if ($query != '') {
    // Truy vấn đơn hàng
    $sqlOrders = "SELECT iddh, idkh, tongtien, trangthai, phuongthuctt, thoigian 
    FROM donhang 
    WHERE iddh LIKE :searchTerm OR idkh LIKE :searchTerm1
    ORDER BY thoigian DESC";
    $stmtOrders = $pdo->prepare($sqlOrders);
    $stmtOrders->execute(['searchTerm' => "%{$query}%", 'searchTerm1' => "%{$query}%"]);
} else {
    // Truy vấn đơn hàng
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
    <title></title>
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthidonhang.css">
</head>
<body>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-file-invoice icon"></i>Mã ĐH 
                        <button class="pay-btn" onclick="loadGioHang()"><i class="fas fa-wallet"></i> Giỏ hàng</button>
                    </th>
                    <th><i class="fas fa-user icon"></i>Khách hàng</th>
                    <th><i class="fas fa-money-bill icon"></i>Tổng tiền</th>
                    <th><i class="fas fa-check-circle icon"></i>Trạng thái</th>
                    <th><i class="fas fa-clock icon"></i>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['iddh'] ?></td>
                        <td><?= $order['idkh'] ?></td>
                        <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                        <td class="status <?= $order['trangthai'] == 'Hoàn thành' ? 'complete' : 'pending' ?>">
                            <?= $order['trangthai'] ?>
                            <button class="edit-btn"><i class="fas fa-edit"></i></button>
                        </td>
                        <td><?= $order['thoigian'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
