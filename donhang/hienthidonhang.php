<?php
require_once '../config.php';
$pdo = connectDatabase();

// Truy vấn đơn hàng
$sqlOrders = "SELECT iddh, idkh, tongtien, trangthai, phuongthuctt, thoigian FROM donhang ORDER BY thoigian DESC";
$stmtOrders = $pdo->prepare($sqlOrders);
$stmtOrders->execute();
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
            position: sticky;
            top: 0;
        }
        .status.complete {
            color: green;
        }
        .status.pending {
            color: red;
        }
        .edit-btn {
            cursor: pointer;
            background: none;
            border: none;
            color: #007bff;
            font-size: 16px;
        }
        .pay-btn {
            background: none;
            border: 1px solid #ffffff;
            color: #ffffff;
            padding: 6px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
            transition: background 0.3s, color 0.3s;
        }
        .pay-btn:hover {
            background-color: #ffffff;
            color: #007BFF;
        }
        .pay-btn i {
            color: #ffffff;
        }
        .pay-btn:hover i {
            color: #007BFF;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-file-invoice icon"></i>Mã ĐH 
                        <button class="pay-btn" onclick="loadDLThanhToan()"><i class="fas fa-wallet"></i> Thanh toán</button>
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
