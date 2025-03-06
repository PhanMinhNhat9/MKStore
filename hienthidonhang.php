<?php
require_once 'config.php';
$pdo = connectDatabase();

// Truy vấn đơn hàng
$sqlOrders = "SELECT iddh, idkh, tongtien, trangthai, phuongthuctt, thoigian FROM donhang ORDER BY thoigian DESC";
$stmtOrders = $pdo->prepare($sqlOrders);
$stmtOrders->execute();
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn chi tiết đơn hàng
$sqlDetails = "SELECT idctdh, iddh, idsp, soluong, gia FROM chitietdonhang";
$stmtDetails = $pdo->prepare($sqlDetails);
$stmtDetails->execute();
$orderDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn thông tin sản phẩm
$sqlProducts = "SELECT idsp, tensp, mota, giaban, soluong, anh FROM sanpham";
$stmtProducts = $pdo->prepare($sqlProducts);
$stmtProducts->execute();
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

// Gom thông tin sản phẩm theo idsp
$productMap = [];
foreach ($products as $product) {
    $productMap[$product['idsp']] = $product;
}

// Gom chi tiết đơn hàng theo iddh
$detailsMap = [];
foreach ($orderDetails as $detail) {
    $detailsMap[$detail['iddh']][] = $detail;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn Hàng</title>
    <script src="trangchuadmin.js"></script>
    <style>
        .order-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 100%;
            margin: auto;
            max-height: 435px;
            overflow-y: auto;
            padding: 10px;
        }
        .order-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            cursor: pointer;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            color: #333;
        }
        .order-details {
            display: none;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .order-item {
            font-size: 14px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>

</head>
<body>
    <div class="order-container">
        <?php foreach ($orders as $order): ?>
            <div class="order-card" onclick="toggleDetails(<?= $order['iddh'] ?>)">
                <div class="order-header">
                    <span>Đơn hàng #<?= $order['iddh'] ?> - <?= $order['thoigian'] ?></span>
                    <span style="color: <?= $order['trangthai'] == 'Hoàn thành' ? 'green' : 'red' ?>;">
                        <?= $order['trangthai'] ?>
                    </span>
                </div>
                <div class="order-details" id="details-<?= $order['iddh'] ?>">
                    <p><strong>Khách hàng:</strong> <?= $order['idkh'] ?></p>
                    <p><strong>Tổng tiền:</strong> <?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</p>
                    <p><strong>Phương thức TT:</strong> <?= $order['phuongthuctt'] ?></p>
                    <p><strong>Sản phẩm:</strong></p>
                    <?php if (!empty($detailsMap[$order['iddh']])): ?>
                        <?php foreach ($detailsMap[$order['iddh']] as $item): ?>
                            <?php $product = $productMap[$item['idsp']] ?? null; ?>
                            <div class="order-item">
                                <?php if ($product): ?>
                                    <img src="<?= htmlspecialchars($product['anh']) ?>" alt="<?= htmlspecialchars($product['tensp']) ?>">
                                    <span><?= htmlspecialchars($product['tensp']) ?> - <?= $item['soluong'] ?> x <?= number_format($item['gia'], 0, ',', '.') ?> VNĐ</span>
                                <?php else: ?>
                                    <span>Sản phẩm #<?= $item['idsp'] ?> không tìm thấy.</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="order-item">Không có sản phẩm.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
