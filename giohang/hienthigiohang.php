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
        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="input-box">
                <i class="fas fa-phone-alt"></i>
                <input type="text" name="phone" id="phone" placeholder="Số điện thoại">
            </div>
        <?php else: ?>
            <div class="input-box">
                <i class="fas fa-phone-alt"></i>
                <input type="text" name="phone" id="phone" placeholder="Số điện thoại" value="<?= $_SESSION['user']['sdt'] ?>" disabled>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" name="name" id="name" placeholder="Họ tên">
            </div>
        <?php else: ?>
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" name="name" id="name" placeholder="Họ tên" value="<?= $_SESSION['user']['hoten'] ?>" disabled>
            </div>
        <?php endif; ?>
        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <button class="floating-btn" onclick="quetma()">
                <i class="fas fa-camera-retro"></i>
            </button>
        <?php endif; ?>
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
                <button class="checkout-button" onclick="xulydl()"><i class="fas fa-credit-card"></i> Xử lý dữ liệu</button>
            </div>
        </div>
<script>
    document.getElementById('phone').addEventListener('blur', function() {
        const phone = this.value;

        if (phone.trim() !== "") {
            fetch('get_user.php?phone=' + encodeURIComponent(phone))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('name').value = data.hoten;
                    } else {
                        document.getElementById('name').value = "";
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }
    });

    function xulydl() {
        var phoneNumber = document.getElementById("phone").value.trim();
        var fullName = document.getElementById("name").value.trim();
        
        if (phoneNumber !== "" && fullName !== "") {
            var url = "xulydl.php?phone=" + encodeURIComponent(phoneNumber) + "&name=" + encodeURIComponent(fullName);
            window.location.href = url;
        } else {
            alert("Vui lòng nhập đầy đủ số điện thoại và họ tên.");
        }
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
