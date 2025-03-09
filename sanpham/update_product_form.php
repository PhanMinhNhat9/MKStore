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
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            border: 2px solid #007bff;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cập Nhật Sản Phẩm</h2>
        <form action="update_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idsp" value="<?= $product['idsp'] ?>">
            <div class="input-group">
                <input type="text" name="tensp" placeholder="Tên sản phẩm" value="<?= htmlspecialchars($product['tensp']) ?>" required>
            </div>
            <div class="input-group">
                <textarea name="mota" placeholder="Mô tả" required><?= htmlspecialchars($product['mota']) ?></textarea>
            </div>
            <div class="input-group">
                <input type="number" name="giaban" placeholder="Giá bán" value="<?= $product['giaban'] ?>" required>
            </div>
            <div class="input-group">
                <input type="number" name="soluong" placeholder="Số lượng" value="<?= $product['soluong'] ?>" required>
            </div>
            <div class="input-group">
                <input type="file" name="anh">
            </div>
            <div class="input-group">
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
            <button type="submit">Cập nhật</button>
        </form>
    </div>
</body>
</html>


