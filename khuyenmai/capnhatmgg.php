<?php
require_once '../config.php';
$pdo = connectDatabase();

$idmgg = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$magiamgia = null;

// Hàm tạo mã giảm giá ngẫu nhiên
function taoMaGiamGia() {
    return 'MGG' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
}

// Lấy dữ liệu mã giảm giá hiện tại
if ($idmgg > 0) {
    $stmt = $pdo->prepare("SELECT code, phantram, ngayhieuluc, ngayketthuc, giaapdung, soluong FROM magiamgia WHERE idmgg = :id");
    $stmt->execute(['id' => $idmgg]);
    $magiamgia = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Mã Giảm Giá</title>
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="capnhatmgg.css">
</head>
<body>

<div class="container">
    <h2 style="text-align: center; font-size: 16px; color: #007bff;">Cập Nhật Mã Giảm Giá</h2>
    <form method="post" action="../config.php">
        <div class="row">
            <div class="form-group">
                <label>Mã Code</label>
                <div class="input-group">
                    <i class="fas fa-barcode"></i>
                    <input type="text" name="code" value="<?= $magiamgia['code'] ?? 'MGGN500IM' ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Phần Trăm (%)</label>
                <div class="input-group">
                    <i class="fas fa-percent"></i>
                    <input type="number" name="phantram" value="<?= $magiamgia['phantram'] ?? '10' ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Ngày Hiệu Lực</label>
                <div class="input-group">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="ngayhieuluc" value="<?= $magiamgia['ngayhieuluc'] ?? '' ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Ngày Kết Thúc</label>
                <div class="input-group">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="ngayketthuc" value="<?= $magiamgia['ngayketthuc'] ?? '' ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Giá Áp Dụng (VNĐ)</label>
                <div class="input-group">
                    <i class="fas fa-money-bill-wave"></i>
                    <input type="number" name="giaapdung" value="<?= $magiamgia['giaapdung'] ?? '' ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Số Lượng</label>
                <div class="input-group">
                    <i class="fas fa-list-ol"></i>
                    <input type="number" name="soluong" value="<?= $magiamgia['soluong'] ?? '' ?>" required>
                </div>
            </div>
        </div>

        <div class="btn-group">
            <input type="hidden" name="idmgg" value="<?= $idmgg ?>">
            <input type="submit" name="capnhatmgg" class="btn btn-save" value="Cập nhật">
            <a href="danhsach_magiamgia.php" onclick="goBack()" class="btn btn-cancel">Hủy</a>
        </div>
    </form>
</div>

</body>
</html>
