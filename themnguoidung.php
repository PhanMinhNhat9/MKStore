<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="themnguoidung.css">
</head>
<body>
    <div class="container">
        <h2>Thêm Người Dùng</h2>
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="form-container">
                <div class="left">
                    <div class="input-group">
                        <input type="text" name="fullname" placeholder="Họ và tên" required>
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Tên đăng nhập" required>
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Mật khẩu" required>
                        <i class="fas fa-key"></i>
                    </div>
                </div>
                <div class="right">
                    <div class="input-group">
                        <input type="text" name="phone" placeholder="Số điện thoại" required>
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="input-group">
                        <input type="text" name="address" placeholder="Địa chỉ" required>
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="input-group">
                        <input type="file" name="anh" required>
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="input-group">
                        <select name="role" required>
                            <option value="">Chọn quyền</option>
                            <option value="0">Admin</option>
                            <option value="1">User</option>
                        </select>
                        <i class="fas fa-user-tag"></i>
                    </div>
                </div>
            </div>
            <input type="submit" id="themnd" name="themnd" value="Thêm Người Dùng">
            <?php
            require_once 'config.php';
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['themnd'])) {
                    themTK();
                }
            }
            ?>
        </form>
    </div>
</body>
</html>
