<?php
require_once 'config.php';
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
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 256px;
            height: 100%;
            background-color: #1e293b;
            color: #fff;
            padding: 24px;
            overflow-y: auto;
        }

        .sidebar h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .sidebar label {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 8px;
            display: block;
        }

        .sidebar select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            background-color: #334155;
            color: #fff;
            border: 1px solid #475569;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .main-content {
            margin-left: 256px;
            padding: 32px;
            flex: 1;
        }

        .main-content h2 {
            font-size: 28px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 24px;
        }

        .table-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-body-container {
            max-height: 550px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #edf2f7;
        }

        .table-body-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-body-container::-webkit-scrollbar-track {
            background: #edf2f7;
        }

        .table-body-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
        }

        .table-body-container::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            padding: 14px 20px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        tbody td {
            padding: 14px 20px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        form {
            display: inline-flex;
            align-items: center;
        }

        input[type="text"] {
            padding: 6px 10px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #cbd5e0;
            margin-right: 8px;
            width: 100px;
        }

        select[name="trangthai"] {
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
            color: #1e293b;
            border: 1px solid #cbd5e0;
        }

        button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
        }

        .text-blue {
            color: #3b82f6;
        }

        .text-blue:hover {
            color: #2563eb;
        }

        .text-green {
            color: #22c55e;
        }

        .text-green:hover {
            color: #16a34a;
        }

        .no-data {
            text-align: center;
            padding: 32px;
            color: #64748b;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h1>Hệ Thống Bán Hàng</h1>
        <form id="filterForm" method="GET" action="">
            <label for="statusFilter">Lọc theo trạng thái:</label>
            <select id="statusFilter" name="status" onchange="document.getElementById('filterForm').submit()">
                <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="Chờ xác nhận" <?= $status == 'Chờ xử lý' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
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
