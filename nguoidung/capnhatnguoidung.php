<?php
    include '../config.php'; 
    $iduser = isset($_GET['id']) ? intval($_GET['id']) : 0;
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
    <script src="../trangchuadmin.js"></script>
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
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 600px;
            text-align: center;
            border: 2px solid #007bff;
        }
        h2 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .input-group {
            margin-bottom: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
            font-size: 14px;
            height: 38px;
            margin: 5px 0px;
        }
        input:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px;
            width: 48%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔹 Cập Nhật Người Dùng 🔹</h2>
        <form action="../config.php" method="POST" enctype="multipart/form-data">
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
                <button type="submit" name="capnhatnd">💾 Cập Nhật</button>
                <button type="button" onclick="goBack()">⬅ Trở Về</button>
            </div>
        </form>
    </div>
</body>
</html>





