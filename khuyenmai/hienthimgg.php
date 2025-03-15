<?php
require_once '../config.php';
$pdo = connectDatabase();

$sql = "SELECT mg.idmgg, mg.code, mg.phantram, mg.ngayhieuluc, mg.ngayketthuc, mg.giaapdung, mg.soluong, mg.thoigian 
        FROM magiamgia mg";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
$currentDate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Mã Giảm Giá</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <style>
       
    </style>
</head>
<body>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mã <button onclick="themmgg()" class="add-mgg-btn">
                        <i class="fas fa-plus-circle"></i> Thêm</button>
                    </th>
                    <th>Giảm (%)</th>
                    <th>Hiệu lực</th>
                    <th>Hết hạn</th>
                    <th>Giá tối thiểu áp dụng</th>
                    <th>Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                        <td><?= $coupon['phantram']?>%</td>
                        <td><?= date('d-m-Y', strtotime($coupon['ngayhieuluc'])) ?></td>
                        <td><?= date('d-m-Y', strtotime($coupon['ngayketthuc'])) ?></td>
                        <td><?= number_format($coupon['giaapdung'], 0, ',', '.') ?> VND</td>
                        <td><?= (int) $coupon['soluong'] ?></td>
                        <td>
                            <?php
                            if ($coupon['ngayhieuluc'] > $currentDate) {
                                echo '<span class="pending">Chưa hiệu lực</span>';
                            } elseif ($coupon['ngayketthuc'] < $currentDate || $coupon['soluong'] <= 0) {
                                echo '<span class="expired">Hết hạn</span>';
                            } else {
                                echo '<span class="valid">Còn hiệu lực</span>';
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <a href="khuyenmai/capnhatmgg.php?id=<?= $coupon['idmgg'] ?>" class="edit-btn">
                                <i class="fas fa-edit"></i> Cập nhật
                            </a>
                            <a href="khuyenmai/xoamgg.php?id=<?= $coupon['idmgg'] ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa mã này không?');">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
