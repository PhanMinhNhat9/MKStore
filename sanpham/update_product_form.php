<?php
require_once '../config.php';
$pdo = connectDatabase();

if (isset($_GET['id'])) {
    $id = intval(base64_decode($_GET['id']));
    $sql = "SELECT * FROM sanpham WHERE idsp = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        echo "Không tìm thấy sản phẩm!";
        exit();
    }
} else {
    echo "ID sản phẩm không hợp lệ!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Sản Phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../trangchuadmin.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
            width: 600px;
            text-align: center;
            border: 2px solid #007bff;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 22px;
        }
        .input-group {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 8px;
            background: #fff;
            transition: all 0.3s ease-in-out;
            width: 100%;
        }
        .input-group i {
            margin-right: 10px;
            color: #007bff;
        }
        input, select, textarea {
            width: 100%;
            border: none;
            outline: none;
            font-size: 14px;
            background: transparent;
        }
        textarea {
            resize: none;
            height: 80px;
        }
        .row-inputs {
            display: flex;
            gap: 10px;
        }
        .row-inputs .input-group {
            flex: 1;
        }
        .file-select-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .file-select-group .input-group {
            flex: 1;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 48%;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .row-inputs, .file-select-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Cập Nhật Sản Phẩm</h2>
        <form action="update_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idsp" value="<?= $product['idsp'] ?>">

            <!-- 3 input trên 1 dòng -->
            <div class="row-inputs">
                <div class="input-group">
                    <i class="fas fa-box"></i>
                    <input type="text" name="tensp" placeholder="Tên sản phẩm" value="<?= htmlspecialchars($product['tensp']) ?>" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-dollar-sign"></i>
                    <input type="number" name="giaban" placeholder="Giá bán" value="<?= $product['giaban'] ?>" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-sort-numeric-up"></i>
                    <input type="number" name="soluong" placeholder="Số lượng" value="<?= $product['soluong'] ?>" required>
                </div>
            </div>

            <div class="input-group" style="margin-top: 10px;">
                <i class="fas fa-align-left"></i>
                <textarea name="mota" placeholder="Mô tả sản phẩm" required><?= htmlspecialchars($product['mota']) ?></textarea>
            </div>

            <!-- Input file và select trên 1 dòng -->
            <div class="file-select-group">
                <div class="input-group">
                    <i class="fas fa-image"></i>
                    <input type="file" name="anh">
                </div>
                <div class="input-group">
                    <i class="fas fa-list"></i>
                    <select name="iddm" required>
                        <?php
                        $sql_dm = "SELECT * FROM danhmucsp";
                        $stmt_dm = $pdo->query($sql_dm);
                        while ($row = $stmt_dm->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($row['iddm'] == $product['iddm']) ? "selected" : "";
                            echo "<option value='{$row['iddm']}' $selected>{$row['tendm']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="button-group">
                <button type="submit" class="update-button"><i class="fas fa-save"></i> Cập nhật</button>
                <button type="button" class="back-button" onclick="goBack()"><i class="fas fa-arrow-left"></i> Trở về</button>
            </div>
        </form>
    </div>
</body>
</html>
