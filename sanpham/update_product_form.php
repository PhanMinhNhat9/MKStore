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
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="update_product_form.css">
</head>
<body>
    <canvas class="snow" id="snowCanvas"></canvas>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Cập Nhật Sản Phẩm</h2>
        <form action="update_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idsp" value="<?= $product['idsp'] ?>">
            <div class="input-group">
                <i class="fas fa-box"></i>
                <input type="text" name="tensp" placeholder="Tên sản phẩm" value="<?= htmlspecialchars($product['tensp']) ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-align-left"></i>
                <textarea name="mota" placeholder="Mô tả" required><?= htmlspecialchars($product['mota']) ?></textarea>
            </div>
            <div class="input-group">
                <i class="fas fa-dollar-sign"></i>
                <input type="number" name="giaban" placeholder="Giá bán" value="<?= $product['giaban'] ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-sort-numeric-up"></i>
                <input type="number" name="soluong" placeholder="Số lượng" value="<?= $product['soluong'] ?>" required>
            </div>
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
            <div class="button-group">
                <button type="submit" class="update-button"><i class="fas fa-save"></i> Cập nhật</button>
                <button type="button" class="back-button" onclick="goBack()"><i class="fas fa-arrow-left"></i> Trở về</button>
            </div>
        </form>
    </div>
    <script>
        const canvas = document.getElementById("snowCanvas");
        const ctx = canvas.getContext("2d");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        let particles = [];
        class Snowflake {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.radius = Math.random() * 4 + 1;
                this.speedY = Math.random() * 2 + 1;
                this.speedX = Math.random() * 1 - 0.5;
            }
            update() {
                this.y += this.speedY;
                this.x += this.speedX;
                if (this.y > canvas.height) {
                    this.y = -10;
                    this.x = Math.random() * canvas.width;
                }
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = "white";
                ctx.fill();
            }
        }
        function createSnowflakes() {
            for (let i = 0; i < 100; i++) {
                particles.push(new Snowflake());
            }
        }
        function animateSnow() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            requestAnimationFrame(animateSnow);
        }
        createSnowflakes();
        animateSnow();
    </script>
</body>
</html>
