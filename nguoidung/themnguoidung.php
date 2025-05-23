<?php
require_once "../config.php";

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['themnd'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = $_POST['role'];
    $target_file = "picture/user.png";

    // Kiểm tra các trường bắt buộc không trống
    if (empty($fullname)) {
        $errors['fullname'] = "Vui lòng nhập họ tên.";
    } elseif (!preg_match('/^[a-zA-ZÀ-ỹ\s]+$/', $fullname)) {
        $errors['fullname'] = "Họ tên chỉ được chứa chữ và không chứa số hoặc ký tự đặc biệt.";
    }

    if (empty($username)) {
        $errors['username'] = "Vui lòng nhập tên đăng nhập.";
    } elseif (!preg_match('/^[a-z0-9]+$/', $username)) {
        $errors['username'] = "Tên đăng nhập chỉ được chứa chữ thường và số.";
    }

    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ.";
    }

    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors['password'] = "Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số, 1 ký tự đặc biệt và tối thiểu 8 ký tự.";
    }

    if (empty($phone)) {
        $errors['phone'] = "Vui lòng nhập số điện thoại.";
    } elseif (!preg_match('/^(0|\+84)[0-9]{9}$/', $phone)) {
        $errors['phone'] = "Số điện thoại phải bắt đầu bằng 0 hoặc +84, theo sau là 9 chữ số.";
    }

    if (empty($address)) {
        $errors['address'] = "Vui lòng nhập địa chỉ.";
    }

    if (empty($role)) {
        $errors['role'] = "Vui lòng chọn quyền.";
    }

    // Nếu không có lỗi, tiến hành thêm người dùng
    if (empty($errors)) {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);
        $kq = themNguoiDung($fullname, $username, $email, $password_hashed, $phone, $address, $role, $target_file);
        if ($kq) {
            echo "<script>window.top.location.href = '../trangchu.php?status=themuserT';</script>";
        } else {
            echo "<script>window.top.location.href = '../trangchu.php?status=themuserF';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../script.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 10px;
        }
        .nd-container {
            max-width: 600px;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding-top: 10px;
        }
        h2 {
            text-align: center;
            color: #1e3a8a;
            margin: 0;
            padding-bottom: 10px;
        }
        .nd-form-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .nd-input-group {
            display: flex;
            align-items: center;
            position: relative;
        }
        .nd-input-group i {
            position: absolute;
            left: 10px;
            color: #6c757d;
        }
        .nd-input-group input,
        .nd-input-group select {
            width: 100%;
            padding: 0.5rem 0.5rem 0.5rem 2.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
            outline: none;
        }
        .nd-input-group select {
            padding-left: 2.5rem;
        }
        .nhomnut-nd {
            text-align: center;
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .nhomnut-nd input[type="submit"],
        .nhomnut-nd input[type="button"] {
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
        }
        .nhomnut-nd input[type="submit"] {
            background: #1e3a8a;
            color: white;
        }
        .nhomnut-nd input[type="button"] {
            background: #6c757d;
            color: white;
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
    <div class="nd-container">
        <h2>Thêm Người Dùng</h2>
        <form action="#" method="POST" id="registerForm" enctype="multipart/form-data">
            <div class="nd-form-container">
                <div class="nd-input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="fullname" id="fullname" placeholder="Họ và tên" pattern="[a-zA-ZÀ-ỹ\s]+" required title="Họ tên chỉ được chứa chữ và không chứa số hoặc ký tự đặc biệt">
                    <div class="error" id="fullnameError"><?= isset($errors['fullname']) ? $errors['fullname'] : 'Họ tên chỉ được chứa chữ và không chứa số hoặc ký tự đặc biệt.' ?></div>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" name="username" id="username" placeholder="Tên đăng nhập" pattern="[a-z0-9]+" required title="Tên đăng nhập chỉ được chứa chữ thường và số">
                    <div class="error" id="usernameError"><?= isset($errors['username']) ? $errors['username'] : 'Tên đăng nhập chỉ được chứa chữ thường và số.' ?></div>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <div class="error" id="emailError"><?= isset($errors['email']) ? $errors['email'] : 'Vui lòng nhập email hợp lệ.' ?></div>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" id="password" placeholder="Mật khẩu" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}" required title="Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số, 1 ký tự đặc biệt và tối thiểu 8 ký tự">
                    <div class="error" id="passwordError"><?= isset($errors['password']) ? $errors['password'] : 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số, 1 ký tự đặc biệt và tối thiểu 8 ký tự.' ?></div>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="phone" id="phone" placeholder="Số điện thoại" pattern="(0|\+84)[0-9]{9}" required title="Số điện thoại phải bắt đầu bằng 0 hoặc +84, theo sau là 9 chữ số">
                    <div class="error" id="phoneError"><?= isset($errors['phone']) ? $errors['phone'] : 'Số điện thoại phải bắt đầu bằng 0 hoặc +84, theo sau là 9 chữ số.' ?></div>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="address" id="address" placeholder="Địa chỉ" required>
                    <div class="error" id="addressError"><?= isset($errors['address']) ? $errors['address'] : 'Vui lòng nhập địa chỉ.' ?></div>
                </div>
                <div class="nd-input-group full-width">
                    <i class="fas fa-user-tag"></i>
                    <select name="role" id="role" required>
                        <option value="">Chọn quyền</option>
                        <option value="2589">Quản trị viên</option>
                        <option value="0">Nhân viên</option>
                        <option value="1">User</option>
                    </select>
                    <div class="error" id="roleError"><?= isset($errors['role']) ? $errors['role'] : 'Vui lòng chọn quyền.' ?></div>
                </div>
            </div>
            <div class="nhomnut-nd">
                <input type="submit" id="nd-themnd" name="themnd" value="Thêm Người Dùng">
                <input type="button" id="nd-trove" onclick="goBack()" value="Trở về">
            </div>
        </form>
    </div>

    <script>
        // Hàm quay lại trang trước
        function goBack() {
            window.history.back();
        }

        // Hàm tạo chuỗi ngẫu nhiên
        function generateRandomString(length) {
            const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
            let result = "";
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        // Hàm xóa dấu tiếng Việt
        function removeDiacritics(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, "").toLowerCase();
        }

        // Kiểm tra định dạng client-side
        const form = document.getElementById('registerForm');
        const inputs = form.querySelectorAll('input');
        const select = form.querySelector('select[name="role"]');

        function validateInput(input) {
            const errorElement = document.getElementById(`${input.name}Error`);
            // Chỉ kiểm tra nếu trường đã được nhập
            if (input.value.trim() !== '') {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    errorElement.style.display = 'block';
                } else {
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                }
            } else {
                // Không hiển thị lỗi nếu trường trống
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
            }
        }

        function validateSelect() {
            const errorElement = document.getElementById('roleError');
            if (select.value === '') {
                select.classList.add('is-invalid');
                errorElement.style.display = 'block';
            } else {
                select.classList.remove('is-invalid');
                errorElement.style.display = 'none';
            }
        }

        inputs.forEach(input => {
            // Kiểm tra khi nhập hoặc rời trường
            input.addEventListener('input', () => validateInput(input));
            input.addEventListener('blur', () => validateInput(input));
        });

        select.addEventListener('change', validateSelect);
        select.addEventListener('blur', validateSelect);

        form.addEventListener('submit', (e) => {
            let hasError = false;
            inputs.forEach(input => {
                // Kiểm tra tất cả trường bắt buộc khi submit
                if (input.value.trim() === '') {
                    input.classList.add('is-invalid');
                    const errorElement = document.getElementById(`${input.name}Error`);
                    errorElement.textContent = `Vui lòng nhập ${input.placeholder.toLowerCase()}.`;
                    errorElement.style.display = 'block';
                    hasError = true;
                } else if (!input.checkValidity()) {
                    validateInput(input);
                    hasError = true;
                }
            });
            if (select.value === '') {
                validateSelect();
                hasError = true;
            }
            if (hasError) {
                e.preventDefault();
            }
        });

        // Xử lý fullname và tự động tạo username
        document.getElementById("fullname").addEventListener("blur", function () {
            let hotenInput = this.value.trim().replace(/[^a-zA-ZÀ-ỹ\s]/g, "").replace(/\s+/g, " ");
            hotenInput = hotenInput.toLowerCase().replace(/(?:^|\s)\p{L}/gu, match => match.toUpperCase());
            this.value = hotenInput;

            if (hotenInput) {
                let nameForUsername = removeDiacritics(hotenInput.split(" ").pop());
                let randomString = generateRandomString(4);
                document.querySelector("input[name='username']").value = nameForUsername + randomString;
            } else {
                document.querySelector("input[name='username']").value = "";
            }
            // Kiểm tra username sau khi tự động tạo
            validateInput(document.getElementById('username'));
        });

        // Xử lý phone (chỉ nhập số)
        document.getElementById("phone").addEventListener("input", function () {
            let sdtInput = this.value.replace(/\D/g, "");
            if (sdtInput.length > 11) {
                sdtInput = sdtInput.slice(0, 11);
            }
            this.value = sdtInput;
            validateInput(this);
        });
    </script>
</body>
</html>