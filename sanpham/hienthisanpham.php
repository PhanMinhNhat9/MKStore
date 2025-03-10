<?php
require_once '../config.php';
$pdo = connectDatabase();

$sql = "SELECT sp.idsp, sp.tensp, sp.mota, sp.giaban, sp.anh, sp.soluong,
            COALESCE(SUM(ctdh.soluong), 0) AS soluong_daban,
            COALESCE(AVG(dg.sosao), 0) AS trungbinhsao,
            COALESCE(mgg.phantram, 0) AS giamgia,
            (sp.soluong - COALESCE(SUM(ctdh.soluong), 0)) AS soluong_conlai
        FROM sanpham sp
        LEFT JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
        LEFT JOIN donhang dh ON ctdh.iddh = dh.iddh AND dh.trangthai = 'Đã thanh toán'
        LEFT JOIN danhgia dg ON sp.idsp = dg.idsp
        LEFT JOIN magiamgia_chitiet mgct ON sp.idsp = mgct.idsp
        LEFT JOIN magiamgia mgg ON mgct.idmgg = mgg.idmgg 
            AND mgg.ngayhieuluc <= CURDATE() 
            AND mgg.ngayketthuc >= CURDATE()
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
    <script src="trangchuadmin.js"></script>
    <style>
        .product-card img {
            transition: 0.3s;
        }
        .product-card.out-of-stock img {
            opacity: 0.5;
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
                $giaSauGiam = $row['giamgia'] > 0 ? $row['giaban'] * (1 - $row['giamgia'] / 100) : $row['giaban'];
                
                // Kiểm tra sản phẩm hết hàng
                $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
                
                echo '<div class="product-card ' . $classOutOfStock . '" style="position: relative;">';

                // Chỉ hiển thị nhãn giảm giá nếu sản phẩm CÒN hàng
                if ($row['giamgia'] > 0 && $soluong_conlai > 0) {
                    echo '<div class="sale-badge">-' . (int)$row['giamgia'] . '%</div>';
                }

                echo '    <img src="' . htmlspecialchars($row['anh']) . '" alt="' . htmlspecialchars($row['tensp']) . '">';
                echo '    <h3>' . htmlspecialchars(mb_strimwidth($row['tensp'], 0, 20, "...")) . '</h3>';
                echo '    <p class="desc">' . htmlspecialchars(mb_strimwidth($row['mota'], 0, 40, "...")) . '</p>';
                
                // Giá sản phẩm
                echo '    <div class="price">';
                if ($row['giamgia'] > 0 && $soluong_conlai > 0) {
                    echo '        <span class="old-price">' . number_format($row['giaban'], 0, ",", ".") . 'đ</span>';
                    echo '        <span class="new-price">' . number_format($giaSauGiam, 0, ",", ".") . 'đ</span>';
                } else {
                    echo '        <span class="new-price">' . number_format($row['giaban'], 0, ",", ".") . 'đ</span>';
                }
                echo '    </div>';
                
                // Thông tin số lượng và đánh giá
                echo '    <div class="info">';
                echo '        <p>📦 Đã bán: <strong>' . (int)$row['soluong_daban'] . '</strong></p>';
                echo '        <p>📦 Còn lại: <strong>' . ($soluong_conlai > 0 ? $soluong_conlai : 'Hết hàng') . '</strong></p>';
                echo '        <p>⭐ ' . round($row['trungbinhsao'], 1) . ' sao</p>';
                echo '    </div>';
                
                // Nút hành động
                echo '    <div class="btn-group-sp">';
                echo '        <button class="btn btn-update" onclick="capnhatsanpham('.(int)$row['idsp'].')">✏️ Sửa</button>';
                echo '        <button class="btn btn-delete" onclick="xoasanpham('.(int)$row['idsp'].')">🗑️ Xóa</button>';
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
