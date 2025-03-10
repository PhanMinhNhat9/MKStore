<?php
require_once '../config.php';
$pdo = connectDatabase();

$sql = "SELECT mg.idmgg, mg.code, mg.phantram, mg.ngayhieuluc, mg.ngayketthuc, mg.thoigian, 
               GROUP_CONCAT(DISTINCT ct.idsp ORDER BY ct.idsp ASC SEPARATOR ', ') AS ds_sanpham,
               GROUP_CONCAT(DISTINCT ct.iddm ORDER BY ct.iddm ASC SEPARATOR ', ') AS ds_danhmuc
        FROM magiamgia mg
        LEFT JOIN magiamgia_chitiet ct ON mg.idmgg = ct.idmgg
        GROUP BY mg.idmgg";

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
                    <th>Thời gian</th>
                    <th>Áp dụng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                        <td><?= $coupon['phantram'] ?>%</td>
                        <td><?= $coupon['ngayhieuluc'] ?></td>
                        <td><?= $coupon['ngayketthuc'] ?></td>
                        <td><?= $coupon['thoigian'] ?></td>
                        <td>
                            <?php
                            $dsSanPham = $coupon['ds_sanpham'];
                            $dsDanhMuc = $coupon['ds_danhmuc'];
                            echo ($dsSanPham && !$dsDanhMuc) ? "Sản phẩm" :
                                 ((!$dsSanPham && $dsDanhMuc) ? "Danh mục" : "Cả hai");
                            ?>
                        </td>
                        <td>
                            <?= ($coupon['ngayketthuc'] < $currentDate) ? '<span class="expired">Hết hạn</span>' : 'Còn hiệu lực' ?>
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
