<?php
    include 'config.php'; 
    $iduser = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $user = getAllUsers($iduser); 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Người Dùng</title>
    <link rel="stylesheet" href="capnhatnguoidung.css">
    <script src="trangchuadmin.js"></script>
</head>
<body>
    <h2>🔹 Cập Nhật Người Dùng 🔹</h2>
    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="container">
            <!-- Cột Trái: Hình Ảnh -->
            <div class="left">
                <label>🖼 Ảnh hiện tại:</label><br>
                <img src="<?= htmlspecialchars($user[0]['anh']) ?>" onerror="this.src='default.png'"><br>

                <label>📸 Chọn ảnh mới:</label>
                <input type="file" name="anh" accept="image/*">

                <label>👤 Họ tên:</label>
                <input type="text" name="hoten" value="<?php echo $user[0]['hoten']; ?>" required>

                <label>👨‍💻 Tên đăng nhập:</label>
                    <input type="text" name="tendn" value="<?php echo $user[0]['tendn']; ?>" required>
                
                <label>📸 Tên ảnh hiện tại:</label>
                <input type="text" name="anhht" value="<?php echo $user[0]['anh']; ?>" required>
            </div>
            <!-- Cột Phải: Thông Tin -->
            <div class="right">
                    <input type="hidden" name="iduser" value="<?php echo $user[0]['iduser']; ?>">

                    <label>📧 Email:</label>
                    <input type="email" name="email" value="<?php echo $user[0]['email']; ?>" required>

                    <label>📞 Số điện thoại:</label>
                    <input type="text" name="sdt" value="<?php echo $user[0]['sdt']; ?>" required>

                    <label>🏡 Địa chỉ:</label>
                    <input type="text" name="diachi" value="<?php echo $user[0]['diachi']; ?>" required>

                    <label>🔒 Mật khẩu mới:</label>
                    <input type="password" name="matkhau" placeholder="Set lại mk mới!" >

                    <label>🔰 Quyền:</label>
                    <select name="quyen">
                        <option value="0" <?= $user[0]['quyen'] == '0' ? 'selected' : '' ?>>Admin</option>
                        <option value="1" <?= $user[0]['quyen'] == '1' ? 'selected' : '' ?>>User</option>
                    </select>

                    <div class="btn-container">
                        <button type="submit" class="btn btn-update">💾 Cập Nhật</button>
                        <button type="button" class="btn btn-back" onclick="goBack()">⬅ Trở Về</button>
                    </div>
                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $iduser  = intval($_POST['iduser']);
                            $hoten   = $_POST['hoten'];
                            $tendn   = $_POST['tendn'];
                            $email   = $_POST['email'];
                            $sdt     = $_POST['sdt'];
                            $diachi  = $_POST['diachi'];
                            $matkhau  = $_POST['matkhau'];
                            $quyen   = $_POST['quyen'];
                            $file    = $_FILES['anh'];
                            $result = capnhatTK($iduser, $hoten, $tendn, $email, $sdt, $diachi, $matkhau, $quyen, $file);
                            if ($result === true) {
                                echo "<script> 
                                        alert('Thêm thành công!');
                                        window.location.href = 'trangchuadmin.html';
                                      </script>";
                            } else {
                                echo $result;
                            }
                        }
                    ?>
            </div>
        </div>
    </form>
</body>
</html>
