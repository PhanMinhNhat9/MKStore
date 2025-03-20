<?php
    require_once '../config.php';
    $pdo = connectDatabase();
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    
    $sql = "SELECT sp.idsp, sp.tensp, sp.mota, sp.giaban, sp.anh, sp.soluong, sp.iddm,
                   COALESCE(SUM(ctdh.soluong), 0) AS soluong_daban,
                   COALESCE(AVG(dg.sosao), 0) AS trungbinhsao,
                   (sp.soluong - COALESCE(SUM(ctdh.soluong), 0)) AS soluong_conlai,
                   mg.phantram AS giamgia
            FROM sanpham sp
            LEFT JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
            LEFT JOIN donhang dh ON ctdh.iddh = dh.iddh AND dh.trangthai = 'Đã thanh toán'
            LEFT JOIN danhgia dg ON sp.idsp = dg.idsp
            LEFT JOIN magiamgia mg ON sp.iddm = mg.iddm
            GROUP BY sp.idsp
            ORDER BY sp.thoigianthemsp ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthisanpham.css">   
    <script src="../trangchuadmin.js"></script>
    <style>
    .alert-success, .alert-error {
        display: none;
        position: fixed;
        top: 20%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        z-index: 1000;
        min-width: 180px;
        transition: transform 0.3s ease, opacity 0.3s ease;
        opacity: 0;
    }

    /* Thành công - màu xanh lá */
    .alert-success {
        background-color: white;
        color: #28a745;
        border: 2px solid #28a745;
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
    }

    /* Lỗi - màu đỏ */
    .alert-error {
        background-color: white;
        color: #dc3545;
        border: 2px solid #dc3545;
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
    }

    /* Hiệu ứng hiển thị */
    .alert-success.show, .alert-error.show {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
    }
    .floating-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #007bff;
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.floating-btn:hover {
    background-color: #0056b3;
}
    </style>
</head>
<body>
<div id="success-alert" class="alert-success"></div>
<div id="error-alert" class="alert-error"></div>
<div class="product-wrapper">
    <div class="product-container">
        <?php foreach ($products as $row): ?>
            <?php
                $soluong_conlai = max(0, $row['soluong'] - $row['soluong_daban']);
                $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
                
                // Tính toán giá sau giảm
                $giagoc = $row['giaban'];
                $phantram_giam = $row['giamgia'] ?? 0;
                $gia_sau_giam = ($phantram_giam > 0) ? $giagoc * (1 - $phantram_giam / 100) : $giagoc;
            ?>
            <div class="product-card <?= $classOutOfStock ?>">
                <img src="../<?= htmlspecialchars($row['anh']) ?>" alt="<?= htmlspecialchars($row['tensp']) ?>">
                <h3><?= htmlspecialchars(mb_strimwidth($row['tensp'], 0, 20, "...")) ?></h3>
                <p class="desc"><?= htmlspecialchars(mb_strimwidth($row['mota'], 0, 50, "...")) ?></p>
                
                <div class="price">
                    <?php if ($phantram_giam > 0): ?>
                        <span class="old-price"><?= number_format($giagoc, 0, ",", ".") ?>đ</span>
                        <span class="discount">-<?= (int)$phantram_giam ?>%</span>
                        <span class="new-price" style="color:red;">
                            <?= number_format($gia_sau_giam, 0, ",", ".") ?>đ
                        </span>
                    <?php else: ?>
                        <span class="new-price">
                            <?= number_format($giagoc, 0, ",", ".") ?>đ
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="info">
                    📦 Số lượng: <strong><?= (int)$row['soluong'] ?></strong>
                    📦 Đã bán: <strong><?= (int)$row['soluong_daban'] ?></strong>
                    📦 Còn lại: <strong><?= ($soluong_conlai > 0 ? $soluong_conlai : 'Hết hàng') ?></strong>
                    ⭐ <?= round($row['trungbinhsao'], 1) ?> sao
                </div>
                
                <div class="btn-group-sp">
                    <button class="btn btn-update" onclick="capnhatsanpham(<?= (int)$row['idsp'] ?>)">
                        <i class="fas fa-sync-alt"></i> Sửa
                    </button>
                    <button class="btn btn-delete" onclick="xoasanpham(<?= (int)$row['idsp'] ?>)">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                    <button class="btn btn-giohang" onclick="themvaogiohang(<?= (int)$row['idsp'] ?>)">
                        <i class="fas fa-shopping-cart"></i> Thêm
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>