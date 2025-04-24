<?php
    require "../config.php";
    $iduser = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;
    $user = getAllUsers($iduser); 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Người Dùng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="capnhatnguoidung.css?v=<?= time(); ?>">
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    <div class="container">
        <h2>🔹 Cập Nhật Người Dùng 🔹</h2>
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <img src="../<?= htmlspecialchars($user[0]['anh']) ?>" alt="Ảnh người dùng" class="rounded-circle border" width="80" height="80">
            </div>
            <input type="hidden" name="iduser" value="<?= htmlspecialchars($user[0]['iduser']) ?>">
            <div class="row g-2">
                <div class="col-4">
                    <input type="text" name="hoten" value="<?= htmlspecialchars($user[0]['hoten']) ?>" placeholder="👤 Họ tên" required>
                </div>
                <div class="col-4">
                    <input type="text" name="tendn" value="<?= htmlspecialchars($user[0]['tendn']) ?>" placeholder="👨‍💻 Tên đăng nhập" required>
                </div>
                <div class="col-4">
                    <input type="email" name="email" value="<?= htmlspecialchars($user[0]['email']) ?>" placeholder="📧 Email" required>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-4">
                    <input type="text" name="sdt" value="<?= htmlspecialchars($user[0]['sdt']) ?>" placeholder="📞 Số điện thoại" required>
                </div>
                <div class="col-4">
                    <input type="text" name="diachi" value="<?= htmlspecialchars($user[0]['diachi']) ?>" placeholder="🏡 Địa chỉ" required>
                </div>
                <div class="col-4">
                    <input type="password" name="matkhau" placeholder="🔒 Mật khẩu mới">
                </div>
            </div>
            <div class="input-group">
                <select name="quyen">
                    <option value="0" <?= $user[0]['quyen'] == '0' ? 'selected' : '' ?>>Admin</option>
                    <option value="1" <?= $user[0]['quyen'] == '1' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <div class="btn-container">
                <button type="submit" name="capnhatnd">Cập Nhật</button>
                <button type="button" onclick="goBack()">Trở Về</button>
            </div>
            <?php
                 if ($_SERVER["REQUEST_METHOD"] == "POST") {
                     if (isset($_POST['capnhatnd'])) {
                        $iduser  = intval($_POST['iduser']);
                        $hoten   = $_POST['hoten'];
                        $tendn   = $_POST['tendn'];
                        $email   = $_POST['email'];
                        $sdt     = $_POST['sdt'];
                        $diachi  = $_POST['diachi'];
                        $matkhau = password_hash($_POST['matkhau'], PASSWORD_BCRYPT);
                        $quyen   = $_POST['quyen'];
                        $kq = capnhatNguoiDung($iduser, $hoten, $tendn, $email, $sdt, $diachi, $quyen, $matkhau);
                        if ($kq) {
                            echo "
                            <script>
                                window.top.location.href = '../trangchuadmin.php?status=cnuserT';
                            </script>";
                        } else {
                            echo "
                            <script>
                                window.top.location.href = '../trangchuadmin.php?status=cnuserF';
                            </script>";
                        }
                    }
                }
            ?>
        </form>
    </div>
</body>
</html>





