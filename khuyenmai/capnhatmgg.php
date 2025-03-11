<?php
require_once '../config.php';
$pdo = connectDatabase();

$idmgg = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM magiamgia WHERE idmgg = :idmgg";
$stmt = $pdo->prepare($sql);
$stmt->execute(['idmgg' => $idmgg]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

$sqlProducts = "
    SELECT sanpham.idsp, sanpham.tensp 
    FROM sanpham
    INNER JOIN magiamgia_chitiet ON sanpham.idsp = magiamgia_chitiet.idsp
    WHERE magiamgia_chitiet.idmgg = :idmgg
    GROUP BY sanpham.idsp, sanpham.tensp
    HAVING COUNT(DISTINCT magiamgia_chitiet.idmgg) = 1

    UNION

    SELECT sanpham.idsp, sanpham.tensp 
    FROM sanpham
    LEFT JOIN magiamgia_chitiet ON sanpham.idsp = magiamgia_chitiet.idsp
    WHERE magiamgia_chitiet.idsp IS NULL
";
$stmtProducts = $pdo->prepare($sqlProducts);
$stmtProducts->execute(['idmgg' => $idmgg]);
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

$sqlCategories = "
    -- Lấy danh mục thuộc mã giảm giá đang xét nhưng không thuộc mã khác
    SELECT d.iddm, d.tendm, d.loaidm, d.icon, d.mota, d.thoigian
    FROM danhmucsp d
    INNER JOIN magiamgia_chitiet mgg ON d.iddm = mgg.iddm
    WHERE mgg.idmgg = :idmgg
    GROUP BY d.iddm, d.tendm, d.loaidm, d.icon, d.mota, d.thoigian
    HAVING COUNT(DISTINCT mgg.idmgg) = 1

    UNION

    -- Lấy danh mục chưa có mã giảm giá nào
    SELECT d.iddm, d.tendm, d.loaidm, d.icon, d.mota, d.thoigian
    FROM danhmucsp d
    LEFT JOIN magiamgia_chitiet mgg ON d.iddm = mgg.iddm
    WHERE mgg.iddm IS NULL

    -- Chỉ lấy danh mục con (loaidm != 0) hoặc danh mục cha không có con
    AND (d.loaidm != 0 OR NOT EXISTS (
        SELECT 1 FROM danhmucsp dc WHERE dc.loaidm = d.iddm
    ))
";

$stmtCategories = $pdo->prepare($sqlCategories);
$stmtCategories->execute(['idmgg' => $idmgg]);
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$sqlApplied = "SELECT idsp, iddm FROM magiamgia_chitiet WHERE idmgg = :idmgg";
$stmtApplied = $pdo->prepare($sqlApplied);
$stmtApplied->execute(['idmgg' => $idmgg]);
$appliedItems = $stmtApplied->fetchAll(PDO::FETCH_ASSOC);

$appliedProducts = array_column($appliedItems, 'idsp');
$appliedCategories = array_column($appliedItems, 'iddm');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Mã Giảm Giá</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 20px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .section {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        h2, h3 {
            text-align: center;
            color: #007BFF;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 90%;
            padding: 4px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 13px;
            text-align: center;
        }
        .actions {
            text-align: center;
            margin-top: 10px;
        }
        .save-btn {
            background: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            padding: 4px 12px;
            font-size: 13px;
        }
        .save-btn:hover {
            background: #0056b3;
        }
        .table-container {
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid #ddd;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }
        th, td {
            padding: 5px;
        }
        th {
            background-color: #007BFF;
            color: white;
            position: sticky;
            top: 0;
        }
        .checkbox {
            text-align: center;
        }
        .icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h2><i class="fas fa-tags icon"></i>Cập Nhật Mã Giảm Giá</h2>
    <div class="container">
        <form action="capnhattientrinh.php" method="post">
            <div class="form-container">
                <div class="section">
                    <input type="hidden" name="idmgg" value="<?= $coupon['idmgg'] ?>">
                    <label for="code"><i class="fas fa-barcode icon"></i>Mã Giảm Giá:</label>
                    <input type="text" id="code" name="code" value="<?= htmlspecialchars($coupon['code']) ?>" required>
                    
                    <label for="phantram"><i class="fas fa-percent icon"></i>Phần Trăm Giảm:</label>
                    <input type="number" id="phantram" name="phantram" value="<?= $coupon['phantram'] ?>" required>
                    
                    <label for="ngayhieuluc"><i class="fas fa-calendar-alt icon"></i>Ngày Hiệu Lực:</label>
                    <input type="date" id="ngayhieuluc" name="ngayhieuluc" value="<?= $coupon['ngayhieuluc'] ?>" required>
                    
                    <label for="ngayketthuc"><i class="fas fa-calendar-times icon"></i>Ngày Kết Thúc:</label>
                    <input type="date" id="ngayketthuc" name="ngayketthuc" value="<?= $coupon['ngayketthuc'] ?>" required>
                </div>
                <div class="section">
                    <h3><i class="fas fa-box icon"></i>Sản Phẩm & Danh Mục Áp Dụng</h3>
                    <div class="table-container">
                        <table>
                            <tr><th>Tên Sản Phẩm</th><th class="checkbox">Chọn</th></tr>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['tensp'] ?></td>
                                <td class="checkbox">
                                    <input type="checkbox" name="sanpham[]" value="<?= $product['idsp'] ?>" <?= in_array($product['idsp'], $appliedProducts) ? 'checked' : '' ?>>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="table-container">
                        <table>
                            <tr><th>Tên Danh Mục</th><th class="checkbox">Chọn</th></tr>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['tendm'] ?></td>
                                <td class="checkbox">
                                    <input type="checkbox" name="danhmuc[]" value="<?= $category['iddm'] ?>" <?= in_array($category['iddm'], $appliedCategories) ? 'checked' : '' ?>>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="save-btn"><i class="fas fa-save icon"></i>Lưu Cập Nhật</button>
            </div>
        </form>
    </div>
</body>
</html>
