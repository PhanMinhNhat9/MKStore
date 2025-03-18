<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        .register-container {
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
        .input-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
        }
        input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }
        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        input[type='submit'] {
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
        input[type='submit']:hover {
            background: #0056b3;
        }
        .avatar-preview {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        .avatar-preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng Ký</h2>
        <form action="dangky.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="hoten" placeholder="Họ và Tên" >
            </div>
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="tendn" placeholder="Tên đăng nhập" >
            </div>
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="matkhau" placeholder="Mật khẩu" >
            </div>
            <div class="input-group">
                <i class="fa fa-phone"></i>
                <input type="text" name="sdt" placeholder="Số điện thoại" >
            </div>
            <div class="input-group">
                <i class="fa fa-map-marker-alt"></i>
                <input type="text" name="diachi" placeholder="Địa chỉ" >
            </div>
            <div class="avatar-preview">
                <img id="preview" src="#" alt="Ảnh đại diện">
            </div>
            <div class="input-group">
                <input type="file" name="anh" accept="image/*" onchange="previewAvatar(event)">
            </div>
            <input type="submit" name="dangkytkuser" value="Đăng ký">
        </form>
    </div>
    
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
