<?php
    require_once '../config.php';
    $pdo = connectDatabase();

    // Lấy danh sách sản phẩm trong giỏ hàng, kết hợp với bảng magiamgia để lấy giá giảm
    $stmt = $pdo->prepare("SELECT gh.idgh, sp.idsp, sp.tensp, sp.anh, gh.soluong, sp.giaban, gh.thanhtien, gh.giagiam,
                                COALESCE(mg.phantram, 0) AS phantram
                        FROM giohang gh 
                        JOIN sanpham sp ON gh.idsp = sp.idsp
                        LEFT JOIN (
                            SELECT iddm, phantram
                            FROM magiamgia
                            GROUP BY iddm
                        ) mg ON sp.iddm = mg.iddm
                        GROUP BY gh.idgh, sp.idsp, sp.tensp, sp.anh, gh.soluong, sp.giaban, mg.phantram
                        ORDER BY gh.thoigian DESC");
    $stmt->execute();
    $giohang = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT SUM(thanhtien) AS thanhtien FROM giohang");
    $stmt->execute();
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    $thanhtien = 0;
    $thanhtien = $tt['thanhtien'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthigiohang.css?v=<?= time(); ?>">
    <script src="../trangchuadmin.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

    <div id="success-alert" class="alert-success"></div>
    <div id="error-alert" class="alert-error"></div>
        
    <div class="form-group">
        <div class="input-box">
            <i class="fas fa-phone-alt"></i>
            <input type="text" name="phone" id="phone" placeholder="Số điện thoại">
        </div>
        <div class="input-box">
            <i class="fas fa-user"></i>
            <input type="text" name="name" id="name" placeholder="Họ tên">
        </div>
    </div>

        <div class="cart-table-wrapper">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($giohang as $item): ?>
                        <?php
                            $giagoc = $item['giaban'];
                            $phantram_giam = $item['phantram'];
                            $gia_sau_giam = ($phantram_giam > 0) ? $giagoc * (1 - $phantram_giam / 100) : $giagoc;
                        ?>
                        <tr>
                            <td><img src="../<?= htmlspecialchars($item['anh']) ?>" alt="<?= htmlspecialchars($item['tensp']) ?>" width="80"></td>
                            <td><?= htmlspecialchars($item['tensp']) ?></td>
                            <td>
                                <?php if ($phantram_giam > 0): ?>
                                    <span class="old-price"><?= number_format($giagoc, 0, ',', '.') ?> VND</span>
                                    <span class="discount">-<?= (int)$phantram_giam ?>%</span>
                                    <span class="new-price">
                                        <?= number_format($gia_sau_giam, 0, ',', '.') ?> VND
                                    </span>
                                <?php else: ?>
                                    <span class="new-price"><?= number_format($giagoc, 0, ',', '.') ?> VND</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="updateQuantity(<?= $item['idsp'] ?>, -1)"><i class="fas fa-minus"></i></button>
                                <input type="text" class="quantity-input" value="<?= $item['soluong'] ?>" readonly>
                                <button onclick="updateQuantity(<?= $item['idsp'] ?>, 1)"><i class="fas fa-plus"></i></button>
                            </td>
                            <td><?= number_format($item['thanhtien'], 0, ',', '.') ?> VND</td>
                            <td class="action-buttons">
                                <button onclick="deleteItem(<?= $item['idsp'] ?>)"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="total-section">
            <span class="total-text">Tổng tiền: <?= number_format($thanhtien, 0, ',', '.') ?> VND</span>
            <div class="button-group">
                <button class="back-button" onclick="goBack()"><i class="fas fa-arrow-left"></i> Quay về</button>
                <button class="checkout-button" onclick="thanhtoan()"><i class="fas fa-credit-card"></i> Thanh toán</button>
            </div>
        </div>

<!-- Nút icon máy ảnh để bật/tắt quét -->
<button class="floating-btn" onclick="quetma()">
    <i class="fas fa-camera-retro"></i>
</button>

<script>
    function thanhtoan() {
        var phoneNumber = document.getElementById("phone").value;
        var fullName = document.getElementById("name").value;
        // Mã hóa dữ liệu để tránh lỗi URL
        var url = "thanhtoan.php?phone=" + encodeURIComponent(phoneNumber) + "&name=" + encodeURIComponent(fullName);
        window.location.href = url;
    }

    function updateQuantity(idsp, change) {
        fetch("capnhatsp.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `idsp=${idsp}&change=${change}`
        }).then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                location.reload();
            } else {
                alert("❌ " + data.message);
            }
        });
    }

    function deleteItem(idsp) {
        fetch("xoasp.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `idsp=${idsp}`
        }).then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                location.reload();
            } else {
                alert("❌ " + data.message);
            }
        });
    }
</script>

</body>
</html>
