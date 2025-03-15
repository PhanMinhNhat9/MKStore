<?php
    require_once '../config.php';
    $pdo = connectDatabase();
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    if ($query != '') {
        $sql = "SELECT sp.idsp, sp.tensp, sp.mota, sp.giaban, sp.anh, sp.soluong,
                COALESCE(SUM(ctdh.soluong), 0) AS soluong_daban,
                COALESCE(AVG(dg.sosao), 0) AS trungbinhsao,
                (sp.soluong - COALESCE(SUM(ctdh.soluong), 0)) AS soluong_conlai
                FROM sanpham sp
                LEFT JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
                LEFT JOIN donhang dh ON ctdh.iddh = dh.iddh AND dh.trangthai = 'Đã thanh toán'
                LEFT JOIN danhgia dg ON sp.idsp = dg.idsp
                WHERE sp.idsp LIKE :searchTerm OR sp.mota LIKE :searchTerm1
                GROUP BY sp.idsp 
                ORDER BY sp.thoigianthemsp ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['searchTerm' => "%{$query}%", 'searchTerm1' => "%{$query}%"]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sql = "SELECT sp.idsp, sp.tensp, sp.mota, sp.giaban, sp.anh, sp.soluong,
                COALESCE(SUM(ctdh.soluong), 0) AS soluong_daban,
                COALESCE(AVG(dg.sosao), 0) AS trungbinhsao,
                (sp.soluong - COALESCE(SUM(ctdh.soluong), 0)) AS soluong_conlai
                FROM sanpham sp
                LEFT JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
                LEFT JOIN donhang dh ON ctdh.iddh = dh.iddh AND dh.trangthai = 'Đã thanh toán'
                LEFT JOIN danhgia dg ON sp.idsp = dg.idsp
                GROUP BY sp.idsp 
                ORDER BY sp.thoigianthemsp ASC";
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
    <script src="trangchuadmin.js"></script>
    <style>
        .product-card img {
            transition: 0.3s;
        }
        .product-card.out-of-stock img {
            opacity: 0.2;
        }
    </style>
</head>
<body>
<div class="product-wrapper">
    <div class="product-container">
        <?php
        if (count($products) > 0) {
            foreach ($products as $row) {
                $soluong_conlai = max(0, $row['soluong'] - $row['soluong_daban']);
                
                // Kiểm tra sản phẩm hết hàng
                $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
                
                echo '<div class="product-card ' . $classOutOfStock . '" style="position: relative;">';

                echo '    <img src="' . htmlspecialchars($row['anh']) . '" alt="' . htmlspecialchars($row['tensp']) . '">';
                echo '    <h3>' . htmlspecialchars(mb_strimwidth($row['tensp'], 0, 20, "...")) . '</h3>';
                echo '    <p class="desc">' . htmlspecialchars(mb_strimwidth($row['mota'], 0, 40, "...")) . '</p>';
                
                // Giá sản phẩm
                echo '    <div class="price">';
                echo '    <span class="old-price">' . number_format($row['giaban'], 0, ",", ".") . 'đ</span>';
                echo '    </div>';
                
                // Thông tin số lượng và đánh giá
                echo '    <div class="info">';
                echo '        📦 Số lượng: <strong>' . (int)$row['soluong'] . '</strong>';
                echo '        📦 Đã bán: <strong>' . (int)$row['soluong_daban'] . '</strong>';
                echo '        📦 Còn lại: <strong>' . ($soluong_conlai > 0 ? $soluong_conlai : 'Hết hàng') . '</strong>';
                echo '        ⭐ ' . round($row['trungbinhsao'], 1) . ' sao';
                echo '    </div>';
                
                // Nút hành động
                echo '    <div class="btn-group-sp">';
                echo '        <button class="btn btn-update" onclick="capnhatsanpham('.(int)$row['idsp'].')"> <i class="fas fa-sync-alt"></i> Sửa</button>';
                echo '        <button class="btn btn-delete" onclick="xoasanpham('.(int)$row['idsp'].')"><i class="fas fa-trash"></i> Xóa</button>';

                echo '        <button class="btn btn-giohang" onclick="themvaogiohang('.(int)$row['idsp'].')"><i class="fas fa-shopping-cart"></i> Thêm</button>';
                
                echo '    </div>';
                
                echo '</div>';
            }
        } else {
            echo "<p>Không có sản phẩm nào.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>
