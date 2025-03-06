<?php
require_once 'config.php';
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
    <title>Quản lý Đơn Hàng</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .table-container {
            max-height: 395px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            margin-top: 10px;
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
        .edit-btn, .pay-btn {
            cursor: pointer;
            background: none;
            border: none;
            color: #007bff;
            font-size: 16px;
        }
        .top-actions {
            margin: 10px 0px;
            
        }
        .top-actions .pay-btn {
            background: none;
    border: 1px solid #007BFF;
    color: #007BFF;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    margin-left: 10px;
    font-size: 14px;
        }
        .top-actions .pay-btn:hover {
            background-color: #007BFF;
    color: white;
        }
    </style>
</head>
<body>
    <div class="top-actions">
        <button class="pay-btn"><i class="fas fa-wallet"></i> Thanh toán</button>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-file-invoice icon"></i>Mã ĐH</th>
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
