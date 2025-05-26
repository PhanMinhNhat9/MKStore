<?php
require_once '../config.php';
$pdo = connectDatabase();
// Database connection (assuming PDO is already set up)
try {
    // Đếm số yêu cầu chưa xem (trangthai = 0)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM yeucaudonhang WHERE trangthai = 0");
    $stmt->execute();
    $thongbaoCount = (int) $stmt->fetchColumn();

    // Lấy danh sách yêu cầu với trạng thái = 0
    $stmtList = $pdo->prepare("SELECT idyc, idkh, lydo, iddh, thoigian, trangthai FROM yeucaudonhang WHERE trangthai = 0 ORDER BY thoigian DESC");
    $stmtList->execute();
    $yeucauList = $stmtList->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color: #e53e3e;'>Lỗi: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách yêu cầu chưa xử lý</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthithongbao.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-bell"></i> Yêu cầu chưa xử lý
                <?php if ($thongbaoCount > 0): ?>
                    <span class="badge"><?= $thongbaoCount ?></span>
                <?php endif; ?>
            </h1>
        </div>

        <!-- Table of pending requests -->
        <div class="table>table-container">
            <?php if (!empty($yeucauList)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID YC</th>
                            <th>ID KH</th>
                            <th>Lý do</th>
                            <th>ID Đơn</th>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($yeucauList as $yc): ?>
                            <tr>
                                <td><?= htmlspecialchars($yc['idyc']) ?></td>
                                <td><?= htmlspecialchars($yc['idkh']) ?></td>
                                <td><?= htmlspecialchars($yc['lydo']) ?></td>
                                <td><?= htmlspecialchars($yc['iddh']) ?></td>
                                <td><?= htmlspecialchars($yc['thoigian']) ?></td>
                                <td class="action-buttons">
                                    <form method="post" action="../donhang/update_trangthai.php">
                                        <input type="hidden" name="idyc" value="<?= $yc['idyc'] ?>">
                                        <input type="hidden" name="iddh" value="<?= $yc['iddh'] ?>">
                                        <input type="hidden" name="trangthai" value="Hủy đơn">
                                        <button type="submit" name="action" value="xacnhan" class="btn btn-confirm">Xác nhận</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">Không có yêu cầu nào chưa xử lý.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>