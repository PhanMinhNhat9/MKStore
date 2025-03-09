<?php
require_once '../config.php';
$pdo = connectDatabase();

// Truy vấn dữ liệu từ hai bảng
$sql = "SELECT mg.idmgg, mg.code, mg.phantram, mg.ngayhieuluc, mg.ngayketthuc, mg.thoigian, 
               GROUP_CONCAT(DISTINCT ct.idsp ORDER BY ct.idsp ASC SEPARATOR ', ') AS ds_sanpham,
               GROUP_CONCAT(DISTINCT ct.iddm ORDER BY ct.iddm ASC SEPARATOR ', ') AS ds_danhmuc
        FROM magiamgia mg
        LEFT JOIN magiamgia_chitiet ct ON mg.idmgg = ct.idmgg
        GROUP BY mg.idmgg";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy ngày hiện tại
$currentDate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Mã Giảm Giá</title>
    <style>
        .coupon-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            max-width: 100%;
            overflow-y: auto;
            max-height: 450px;
            padding: 10px;
        }
        .coupon-card {
            background: linear-gradient(135deg, #4a90e2, #86c5da);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            transition: transform 0.3s ease-in-out;
            flex-shrink: 0;
            color: #fff;
            position: relative;
        }
        .coupon-card:hover {
            transform: scale(1.05);
        }
        .coupon-code {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            background: #1f78d1;
            padding: 6px 10px;
            border-radius: 5px;
            display: inline-block;
            text-align: center;
        }
        .coupon-info {
            margin-top: 10px;
            font-size: 13px;
            color: #f0f0f0;
        }
        .coupon-info p {
            margin: 4px 0;
        }
        .apply-notice {
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
            color: #ffeb3b;
            text-align: center;
        }
        .expired-label {
            background: #e74c3c;
            color: white;
            padding: 5px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            margin-top: 10px;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .action-buttons a {
            text-decoration: none;
            color: #fff;
            padding: 5px 8px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            transition: background 0.3s ease;
        }
        .delete-btn {
            background: #e74c3c;
        }
        .edit-btn {
            background: #2ecc71;
        }
        .action-buttons a i {
            margin-right: 5px;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
        .edit-btn:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="coupon-container">
        <?php foreach ($coupons as $coupon): ?>
            <div class="coupon-card">
                <div class="coupon-code">Mã: <?= htmlspecialchars($coupon['code']) ?></div>
                <div class="coupon-info">
                    <p>🔖 Giảm: <strong><?= $coupon['phantram'] ?>%</strong></p>
                    <p>📅 Hiệu lực: <?= $coupon['ngayhieuluc'] ?></p>
                    <p>⏳ Hết hạn: <?= $coupon['ngayketthuc'] ?></p>
                    <p>🕒 Thời gian: <?= $coupon['thoigian'] ?></p>
                </div>

                <!-- Kiểm tra xem mã đã hết hạn chưa -->
                <?php if ($coupon['ngayketthuc'] < $currentDate): ?>
                    <div class="expired-label">⏳ Hết hạn</div>
                <?php endif; ?>

                <!-- Hiển thị thông báo áp dụng -->
                <div class="apply-notice">
                    <?php
                    $dsSanPham = $coupon['ds_sanpham'];
                    $dsDanhMuc = $coupon['ds_danhmuc'];

                    if ($dsSanPham && !$dsDanhMuc) {
                        echo "📦 Mã giảm giá này chỉ áp dụng cho sản phẩm.";
                    } elseif (!$dsSanPham && $dsDanhMuc) {
                        echo "📂 Mã giảm giá này chỉ áp dụng cho danh mục.";
                    } else {
                        echo "🌍 Mã giảm giá này áp dụng toàn bộ cửa hàng.";
                    }
                    ?>
                </div>

                <div class="action-buttons">
                    <a href="khuyenmai/capnhatmgg.php?id=<?= $coupon['idmgg'] ?>" class="edit-btn">
                        <i class="fas fa-edit"></i> Cập nhật
                    </a>
                    <a href="#?id=<?= $coupon['idmgg'] ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa mã này không?');">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
