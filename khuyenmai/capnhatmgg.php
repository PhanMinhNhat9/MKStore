<?php
    require_once '../config.php';
    $pdo = connectDatabase();

    $idmgg = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;
    $magiamgia = null;

    // Hàm tạo mã giảm giá ngẫu nhiên
    function taoMaGiamGia() {
        return 'MGG' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    }

    // Lấy dữ liệu mã giảm giá hiện tại
    if ($idmgg > 0) {
        $stmt = $pdo->prepare("SELECT code, phantram, ngayhieuluc, ngayketthuc, giaapdung, soluong, iddm FROM magiamgia WHERE idmgg = :id");
        $stmt->execute(['id' => $idmgg]);
        $magiamgia = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $sql_danhmuc = "
        SELECT dm.iddm, dm.tendm, dm.loaidm
        FROM danhmucsp dm
        WHERE dm.loaidm<>0";
    $stmt_dm = $pdo->prepare($sql_danhmuc);
    $stmt_dm->execute();
    $danhmucs = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Mã Giảm Giá</title>
    <script src="../script.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="capnhatmgg.css?v=<?= time(); ?>">
</head>
<body>

<div class="container">
    <h2 style="text-align: center; font-size: 16px; color: #007bff;">Cập Nhật Mã Giảm Giá</h2>
    <form method="POST" action="#">
        <div class="row">
            <div class="form-group">
                <label>Mã Code</label>
                <div class="input-group">
                    <i class="fas fa-barcode"></i>
                    <input type="text" name="code" value="<?= $magiamgia['code'] ?? taoMaGiamGia() ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Phần Trăm (%)</label>
                <div class="input-group">
                    <i class="fas fa-percent"></i>
                    <input type="number" name="phantram" value="<?= $magiamgia['phantram']?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Ngày Hiệu Lực</label>
                <div class="input-group">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="ngayhieuluc" value="<?= $magiamgia['ngayhieuluc']?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Ngày Kết Thúc</label>
                <div class="input-group">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="ngayketthuc" value="<?= $magiamgia['ngayketthuc']?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Giá Áp Dụng (VNĐ)</label>
                <div class="input-group">
                    <i class="fas fa-money-bill-wave"></i>
                    <input type="number" name="giaapdung" value="<?= $magiamgia['giaapdung']?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Số Lượng</label>
                <div class="input-group">
                    <i class="fas fa-list-ol"></i>
                    <input type="number" name="soluong" value="<?= $magiamgia['soluong']?>" required>
                </div>
            </div>
        </div>

        <div class="row">
        <div class="form-group">
    <label>Danh Mục Áp Dụng</label>
    <div class="input-group">
        <i class="fas fa-tags"></i>
        <select name="iddm" required>
            <?php foreach ($danhmucs as $dm): ?>
                <option 
                    value="<?= $dm['iddm'] ?>"
                    <?= (isset($magiamgia['iddm']) && $magiamgia['iddm'] == $dm['iddm']) ? 'selected' : '' ?> 
                >🌱
    <?= htmlspecialchars($dm['tendm']) ?>
</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

        </div>

        <div class="btn-group">
            <input type="hidden" name="idmgg" value="<?= $idmgg ?>">
            <input type="submit" name="capnhatmgg" class="btn btn-save" value="Cập nhật">
            <a href="danhsach_magiamgia.php" onclick="goBack()" class="btn btn-cancel">Hủy</a>
        </div>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['capnhatmgg'])) {
                    $code = !empty($_POST['code']) ? $_POST['code'] : taoMaGiamGia();
                    $idmgg = $_POST['idmgg'];
                    $phantram = $_POST['phantram'];
                    $ngayhieuluc = $_POST['ngayhieuluc'];
                    $ngayketthuc = $_POST['ngayketthuc'];
                    $giaapdung = $_POST['giaapdung'];
                    $soluong = $_POST['soluong'];
                    $iddm = $_POST['iddm'];
                    $kq = capnhatMGG($code, $idmgg, $phantram, $ngayhieuluc, $ngayketthuc, $giaapdung, $soluong, $iddm);
                    if ($kq) {
                        echo "
                            <script>
                                showCustomAlert('🐳 Cập Nhật Thành Công!', 'MGG đã được cập nhật vào danh sách!', '../picture/success.png');
                                setTimeout(function() {
                                    goBack();
                                }, 3000); 
                            </script>";
                    } else {
                        echo "
                            <script>
                                showCustomAlert('🐳 Cập Nhật Không Thành Công!', '', '../picture/error.png');
                            </script>";
                        }
                    }
                }
        ?>
    </form>
</div>

</body>
</html>
