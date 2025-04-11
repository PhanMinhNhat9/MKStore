<?php
include "config.php"; // Đường dẫn phù hợp với cấu trúc thư mục của bạn
$conn = connectDatabase();
// Truy vấn dữ liệu thống kê sản phẩm đã bán
$sql = "
    SELECT sp.tenmon, sp.hinhanh, SUM(ctdh.soluong) AS soluong_ban, SUM(ctdh.soluong * ctdh.dongia) AS doanhthu
    FROM sanpham sp
    JOIN chitietdonhang ctdh ON sp.id = ctdh.sanpham_id
    JOIN donhang dh ON ctdh.donhang_id = dh.id
    WHERE dh.trangthai = 'Đã giao'
    GROUP BY sp.id
    ORDER BY doanhthu DESC;
";
$result = mysqli_query($conn, $sql);

// Tính tổng doanh thu
$tongDoanhThu = 0;
$tongSanPham = 0;
$tongLuotBan = 0;
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tongDoanhThu += $row['doanhthu'];
    $tongSanPham++;
    $tongLuotBan += $row['soluong_ban'];
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê sản phẩm</title>
    <link rel="stylesheet" href="thongke.css?v=<?= time(); ?>">
</head>
<body>
    <div class="thongke-wrapper">
        <h2>Thống kê sản phẩm bán chạy</h2>

        <div class="summary-cards">
            <div class="card">
                <p>Sản phẩm được bán ra</p>
                <strong><?= $tongSanPham ?></strong>
            </div>
            <div class="card">
                <p>Số lượng bán ra</p>
                <strong><?= $tongLuotBan ?></strong>
            </div>
            <div class="card">
                <p>Doanh thu</p>
                <strong><?= number_format($tongDoanhThu, 0, ',', '.') ?> đ</strong>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên món</th>
                    <th>Số lượng bán</th>
                    <th>Doanh thu</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <img src="<?= $item['hinhanh'] ?>" alt="<?= $item['tenmon'] ?>" class="thumbnail">
                            <?= htmlspecialchars($item['tenmon']) ?>
                        </td>
                        <td><?= $item['soluong_ban'] ?></td>
                        <td><?= number_format($item['doanhthu'], 0, ',', '.') ?> đ</td>
                        <td><button class="btn-detail">Chi tiết</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
