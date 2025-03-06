<?php
require_once 'config.php';
$pdo = connectDatabase();

// Truy vấn dữ liệu từ bảng magiamgia
$sql = "SELECT idmgg, code, idsp, iddm, phantram, ngayhieuluc, ngayketthuc, thoigian FROM magiamgia";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Mã Giảm Giá</title>
    <style>
        .coupon-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            max-width: 100%;
            overflow-y: auto;
            max-height: 425px;
            padding: 10px;

        }
        .coupon-card {
            background: linear-gradient(135deg, #4a90e2, #86c5da);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 200px;
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="coupon-container">
        <?php foreach ($coupons as $coupon): ?>
            <div class="coupon-card">
                <div class="coupon-code">Mã: <?= htmlspecialchars($coupon['code']) ?></div>
                <div class="coupon-info">
                    <p>🔖 Giảm: <strong><?= $coupon['phantram'] ?>%</strong></p>
                    <p>📦 ID Sản phẩm: <?= $coupon['idsp'] ?></p>
                    <p>📂 ID Danh mục: <?= $coupon['iddm'] ?></p>
                    <p>📅 Hiệu lực: <?= $coupon['ngayhieuluc'] ?></p>
                    <p>⏳ Hết hạn: <?= $coupon['ngayketthuc'] ?></p>
                    <p>🕒 Thời gian: <?= $coupon['thoigian'] ?></p>
                </div>
                <div class="action-buttons">
                    <a href="#?id=<?= $coupon['idmgg'] ?>" class="edit-btn">
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
