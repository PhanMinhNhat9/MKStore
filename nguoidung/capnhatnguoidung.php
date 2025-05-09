<?php
require "../config.php";
$iduser = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;
$user = getAllUsers($iduser);

// Kiểm tra dữ liệu đầu vào phía server
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['capnhatnd'])) {
    $iduser = intval($_POST['iduser']);
    $hoten = trim($_POST['hoten']);
    $tendn = trim($_POST['tendn']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);
    $matkhau = $_POST['matkhau'];
    $quyen = $_POST['quyen'];

    // Ràng buộc tendn: chỉ số và chữ thường
    if (!preg_match('/^[a-z0-9]+$/', $tendn)) {
        $errors['tendn'] = "Tên đăng nhập chỉ được chứa chữ thường và số.";
    }

    // Ràng buộc hoten: chỉ chữ, hỗ trợ dấu tiếng Việt
    if (!preg_match('/^[a-zA-ZÀ-ỹ\s]+$/', $hoten)) {
        $errors['hoten'] = "Họ tên chỉ được chứa chữ và không chứa số hoặc ký tự đặc biệt.";
    }

    // Ràng buộc sdt: định dạng số điện thoại Việt Nam
    if (!preg_match('/^(0|\+84)[0-9]{9}$/', $sdt)) {
        $errors['sdt'] = "Số điện thoại phải bắt đầu bằng 0 hoặc +84, theo sau là 9 chữ số.";
    }

    // Ràng buộc matkhau: nếu nhập, phải đủ chữ hoa, thường, số, ký tự đặc biệt, tối thiểu 8 ký tự
    if (!empty($matkhau) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $matkhau)) {
        $errors['matkhau'] = "Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số, 1 ký tự đặc biệt và tối thiểu 8 ký tự.";
    }

    // Nếu không có lỗi, tiến hành cập nhật
    if (empty($errors)) {
        $matkhau_hashed = !empty($matkhau) ? password_hash($matkhau, PASSWORD_BCRYPT) : $user[0]['matkhau'];
        $kq = capnhatNguoiDung($iduser, $hoten, $tendn, $email, $sdt, $diachi, $quyen, $matkhau_hashed);
        if ($kq) {
            echo "<script>window.top.location.href = '../trangchu.php?status=cnuserT';</script>";
        } else {
            echo "<script>window.top.location.href = '../trangchu.php?status=cnuserF';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Người Dùng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 4px;
            font-size: 0.9rem;
            padding: 0.5rem;
        }
        .input-group select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .btn-container {
            text-align: center;
            margin-top: 1.5rem;
        }
        .btn-container button {
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin: 0 0.5rem;
        }
        .btn-container button[type="submit"] {
            background: #1e3a8a;
            color: white;
            border: none;
        }
        .btn-container button[type="button"] {
            background: #6c757d;
            color: white;
            border: none;
        }
        .error {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.2rem;
            display: none;
        }
        .is-invalid + .error {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔹 Cập Nhật Người Dùng 🔹</h2>
        <form action="#" method="POST" enctype="multipart/form-data" id="userForm">
            <div class="mb-2 text-center">
                <img src="../<?= htmlspecialchars($user[0]['anh']) ?>" alt="Ảnh người dùng" class="rounded-circle border" width="80" height="80">
            </div>
            <input type="hidden" name="iduser" value="<?= htmlspecialchars($user[0]['iduser']) ?>">
            <div class="row g-2">
                <div class="col-4">
                    <input type="text" class="form-control" name="hoten" value="<?= htmlspecialchars($user[0]['hoten']) ?>" placeholder="👤 Họ tên" pattern="[a-zA-ZÀ-ỹ\s]+" required title="Họ tên chỉ được chứa chữ và không chứa số hoặc ký tự đặc biệt">
                    <div class="error" id="hotenError"><?= isset($errors['hoten']) ? $errors['hoten'] : 'Họ tên chỉ được chứa chữ và không chứa số hoặc ký tự đặc biệt.' ?></div>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="tendn" value="<?= htmlspecialchars($user[0]['tendn']) ?>" placeholder="👨‍💻 Tên đăng nhập" pattern="[a-z0-9]+" required title="Tên đăng nhập chỉ được chứa chữ thường và số">
                    <div class="error" id="tendnError"><?= isset($errors['tendn']) ? $errors['tendn'] : 'Tên đăng nhập chỉ được chứa chữ thường và số.' ?></div>
                </div>
                <div class="col-4">
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user[0]['email']) ?>" placeholder="📧 Email" required>
                    <div class="error" id="emailError">Vui lòng nhập email hợp lệ.</div>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-4">
                    <input type="text" class="form-control" name="sdt" value="<?= htmlspecialchars($user[0]['sdt']) ?>" placeholder="📞 Số điện thoại" pattern="(0|\+84)[0-9]{9}" required title="Số điện thoại phải bắt đầu bằng 0 hoặc +84, theo sau là 9 chữ số">
                    <div class="error" id="sdtError"><?= isset($errors['sdt']) ? $errors['sdt'] : 'Số điện thoại phải bắt đầu bằng 0 hoặc +84, theo sau là 9 chữ số.' ?></div>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="diachi" value="<?= htmlspecialchars($user[0]['diachi']) ?>" placeholder="🏡 Địa chỉ" required>
                    <div class="error" id="diachiError">Vui lòng nhập địa chỉ.</div>
                </div>
                <div class="col-4">
                    <input type="password" class="form-control" name="matkhau" placeholder="🔒 Mật khẩu mới" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}">
                    <div class="error" id="matkhauError"><?= isset($errors['matkhau']) ? $errors['matkhau'] : 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số, 1 ký tự đặc biệt và tối thiểu 8 ký tự.' ?></div>
                </div>
            </div>
            <div class="input-group mt-2">
                <select name="quyen" class="form-control">
                    <option value="0" <?= $user[0]['quyen'] == '0' ? 'selected' : '' ?>>Admin</option>
                    <option value="1" <?= $user[0]['quyen'] == '1' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <div class="btn-container">
                <button type="submit" name="capnhatnd">Cập Nhật</button>
                <button type="button" onclick="goBack()">Trở Về</button>
            </div>
        </form>
    </div>

    <script>
        // Hàm quay lại trang trước
        function goBack() {
            window.history.back();
        }

        // Kiểm tra định dạng client-side
        const form = document.getElementById('userForm');
        const inputs = form.querySelectorAll('input');
        
        function validateInput(input) {
            const errorElement = document.getElementById(`${input.name}Error`);
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', () => validateInput(input));
            input.addEventListener('blur', () => validateInput(input));
        });

        form.addEventListener('submit', (e) => {
            let hasError = false;
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    validateInput(input);
                    hasError = true;
                }
            });
            if (hasError) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>