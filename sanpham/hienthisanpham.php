<?php
    require_once '../config.php';
    $pdo = connectDatabase();
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $products = [];
    if ($query != '') {
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
            WHERE sp.tensp LIKE :searchTerm OR sp.mota LIKE :searchTerm1
            GROUP BY sp.idsp
            ORDER BY sp.thoigianthemsp ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['searchTerm' => "%{$query}%", 'searchTerm1' => "%{$query}%"]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
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
                    ORDER BY sp.thoigianthemsp DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthisanpham.css?v=<?= time(); ?>">   
    <script src="../trangchuadmin.js"></script>
</head>
<body>
<div id="success-alert" class="alert-success"></div>
<div id="error-alert" class="alert-error"></div>
<div class="product-wrapper">
    <table class="product-table">
    <tr>
    <?php foreach ($products as $index => $row): ?> 
        <?php
            $soluong_conlai = max(0, $row['soluong'] - $row['soluong_daban']);
            $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
            
            // Tính giá sau giảm
            $giagoc = $row['giaban'];
            $phantram_giam = $row['giamgia'] ?? 0;
            $gia_sau_giam = ($phantram_giam > 0) ? $giagoc * (1 - $phantram_giam / 100) : $giagoc;
        ?>

        <td>
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
        </td>

        <?php if (($index + 1) % 5 == 0): ?>
            </tr><tr> <!-- Kết thúc hàng cũ và bắt đầu hàng mới sau mỗi 3 sản phẩm -->
        <?php endif; ?>
    <?php endforeach; ?>
    </tr>
</table>
</div>
<button class="floating-btn" onclick="themsanpham()">
        <i class="fas fa-plus"></i>
    </button>
</body>
</html>