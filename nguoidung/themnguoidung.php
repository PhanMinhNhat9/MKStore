<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="themnguoidung.css">
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    <div class="nd-container">
        <h2>Thêm Người Dùng</h2>
        <form action="../config.php" method="POST" enctype="multipart/form-data">
            <div class="nd-form-container">
                <div class="nd-input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="fullname" placeholder="Họ và tên" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" name="username" placeholder="Tên đăng nhập" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="phone" placeholder="Số điện thoại" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="address" placeholder="Địa chỉ" required>
                </div>
                <div class="nd-input-group full-width">
                    <i class="fas fa-image"></i>
                    <input type="file" name="anh" required>
                </div>
                <div class="nd-input-group full-width">
                    <i class="fas fa-user-tag"></i>
                    <select name="role" required>
                        <option value="">Chọn quyền</option>
                        <option value="0">Admin</option>
                        <option value="1">User</option>
                    </select>
                </div>
            </div>
            <div class="nhomnut-nd">
                <input type="submit" id="nd-themnd" name="themnd" value="Thêm Người Dùng">
                <input type="button" id="nd-trove" onclick="goBack()" value="Trở về">
            </div>
        </form>
    </div>
</body>
</html>
