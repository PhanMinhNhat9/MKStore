<?php
    require_once '../config.php';
    $pdo = connectDatabase();
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    if ($query != '') {
        $sqlOrders = "SELECT iddh, idkh, tenkh, tongtien, trangthai, phuongthuctt, thoigian 
                    FROM donhang 
                    WHERE (tenkh LIKE :searchTerm OR idkh LIKE :searchTerm1)
                    ORDER BY thoigian DESC";
        $stmtOrders = $pdo->prepare($sqlOrders);
        $stmtOrders->execute(['searchTerm' => "%{$query}%", 'searchTerm1' => "%{$query}%"]);
    } else {
        $sqlOrders = "SELECT iddh, idkh, tenkh, tongtien, trangthai, phuongthuctt, thoigian 
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
    <style>
        .edit-idkh-form {
    display: flex;
    gap: 6px;
    align-items: center;
    justify-content: center;
    
}

.idkh-input {
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    width: 100px;
    transition: border-color 0.2s;
}

.idkh-input:focus {
    border-color: #007BFF;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.save-btn {
    background-color: #007bff; 
    color: white;
    border: none;
    border-radius: 6px;
    padding: 6px 10px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s, transform 0.2s;
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.2);
}

.save-btn i {
    pointer-events: none;
    font-size: 14px;
}

.save-btn:hover {
    background-color: #0056b3;     
    transform: scale(1.05);        
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}
.tenkh-input {
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    width: 140px;
    transition: border-color 0.2s;
}

.tenkh-input:focus {
    border-color: #007BFF;
    outline: none;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.edit-tenkh-form {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.no-name {
    color: #888;
    font-style: italic;
}


    </style>
</head>
<body>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Mã KH</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian đặt hàng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['iddh'] ?></td>
                        <!-- <td><?= $order['idkh'] ?></td> -->
                        <td>
                            <form action="update_idkh.php" method="POST"  class="edit-idkh-form">
                                <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                <input type="text" name="idkh" value="<?= $order['idkh'] ?>" class="idkh-input">
                                <button type="submit" title="Lưu IDKH" class="save-btn"><i class="fas fa-save"></i></button>
                            </form>
                        </td>

                        <?php 
                            if ($order['tenkh']=='' or $order['tenkh']==" ") {
                                $sql = "SELECT u.hoten 
                                        FROM donhang AS d 
                                        JOIN user AS u ON d.idkh = u.sdt 
                                        WHERE u.sdt = :sdt";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['sdt' => $order['idkh']]);
                                $tenkh = $stmt->fetchColumn();
                            } else $tenkh = $order['tenkh'];
                        ?>
                        <td>
                            <form action="update_tenkh.php" method="POST" class="edit-tenkh-form">
                                <input type="hidden" name="iddh" value="<?= $order['iddh'] ?>">
                                <input type="text" name="tenkh" value="<?= htmlspecialchars($tenkh) ?>" class="tenkh-input" placeholder="Nhập tên KH!">
                                <button type="submit" class="save-btn" title="Lưu tên khách hàng">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                           
                        </td>

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
