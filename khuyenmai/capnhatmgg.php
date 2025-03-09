<?php
require_once '../config.php';
$pdo = connectDatabase();

// Lấy ID mã giảm giá từ URL
$idmgg = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn lấy dữ liệu mã giảm giá
$sql = "SELECT * FROM magiamgia WHERE idmgg = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idmgg]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy, chuyển hướng về danh sách
if (!$coupon) {
    header("Location: danh_sach_magiamgia.php");
    exit;
}

// Xử lý cập nhật khi form được gửi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'];
    $phantram = $_POST['phantram'];
    $idsp = $_POST['idsp'];
    $iddm = $_POST['iddm'];
    $ngayhieuluc = $_POST['ngayhieuluc'];
    $ngayketthuc = $_POST['ngayketthuc'];

    $sql = "UPDATE magiamgia 
            SET code=?, phantram=?, idsp=?, iddm=?, ngayhieuluc=?, ngayketthuc=? 
            WHERE idmgg=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$code, $phantram, $idsp, $iddm, $ngayhieuluc, $ngayketthuc, $idmgg]);

    // Chuyển hướng về danh sách sau khi cập nhật
    header("Location: danh_sach_magiamgia.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật Mã Giảm Giá</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f9ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
            width: 800px;
            overflow: hidden;
        }
        .info-container, .form-container {
            padding: 20px;
            width: 50%;
            padding-right: 35px;
        }
        .info-container {
            background: #007bff;
            color: white;
        }
        .info-container h2 {
            font-size: 22px;
            margin-bottom: 15px;
        }
        .info-item {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .form-container h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
            color: #007bff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-size: 14px;
            font-weight: 500;
            color: #007bff;
            display: block;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            font-size: 14px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-custom {
            background: #007bff;
            color: white;
            transition: 0.3s;
            font-size: 14px;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-custom:hover {
            background: #0056b3;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="info-container">
            <h2>Thông tin Mã Giảm Giá</h2>
            <div class="info-item"><strong>Mã:</strong> <?= htmlspecialchars($coupon['code']) ?></div>
            <div class="info-item"><strong>Phần trăm:</strong> <?= $coupon['phantram'] ?>%</div>
            <div class="info-item"><strong>ID Sản phẩm:</strong> <?= $coupon['idsp'] ?></div>
            <div class="info-item"><strong>ID Danh mục:</strong> <?= $coupon['iddm'] ?></div>
            <div class="info-item"><strong>Ngày hiệu lực:</strong> <?= $coupon['ngayhieuluc'] ?></div>
            <div class="info-item"><strong>Ngày kết thúc:</strong> <?= $coupon['ngayketthuc'] ?></div>
        </div>
        <div class="form-container">
            <h2>Cập nhật</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Mã giảm giá:</label>
                    <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($coupon['code']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Phần trăm giảm giá:</label>
                    <input type="number" name="phantram" class="form-control" value="<?= $coupon['phantram'] ?>" required min="0" max="100">
                </div>
                <div class="form-group">
                    <label>ID Sản phẩm:</label>
                    <input type="number" name="idsp" class="form-control" value="<?= $coupon['idsp'] ?>" required>
                </div>
                <div class="form-group">
                    <label>ID Danh mục:</label>
                    <input type="number" name="iddm" class="form-control" value="<?= $coupon['iddm'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Ngày hiệu lực:</label>
                    <input type="date" name="ngayhieuluc" class="form-control" value="<?= $coupon['ngayhieuluc'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Ngày kết thúc:</label>
                    <input type="date" name="ngayketthuc" class="form-control" value="<?= $coupon['ngayketthuc'] ?>" required>
                </div>
                <button type="submit" class="btn-custom">Cập nhật</button>
            </form>
            <a href="danh_sach_magiamgia.php" class="back-link">⬅ Quay lại</a>
        </div>
    </div>
</body>
</html>

