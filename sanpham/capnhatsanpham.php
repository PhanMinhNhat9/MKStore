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
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="capnhatsanpham.css?v=<?php time();?>">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Cập Nhật Sản Phẩm</h2>
        <form action="#" method="POST" enctype="multipart/form-data">
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
                <button type="submit" name="capnhatsanpham" class="update-button"><i class="fas fa-save"></i> Cập nhật</button>
                <button type="button" class="back-button" onclick="goBack()"><i class="fas fa-arrow-left"></i> Trở về</button>
            </div>
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['capnhatsanpham'])) {
                        $kq = capnhatSanPham();
                        if ($kq) {
                            echo "
                            <script>
                                showCustomAlert('🐳 Cập Nhật Thành Công!', 'Thông tin sản phẩm đã được cập nhật thành công!', '../picture/success.png');
                                setTimeout(function() {
                                    goBack();
                                }, 3000); 
                            </script>";
                        } else {
                            echo "
                            <script>
                                showCustomAlert('🐳 Cập Nhật Thất Bại!', '$kq', '../picture/error.png');
                            </script>";
                        }
                    }
                }
            ?>
        </form>
    </div>
</body>
</html>
