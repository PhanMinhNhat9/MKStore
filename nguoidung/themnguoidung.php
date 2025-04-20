<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="themnguoidung.css?v=<?php time(); ?>">
    <script src="../trangchuadmin.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
</head>
<body>
    <div class="nd-container">
        <h2>Thêm Người Dùng</h2>
        <form action="#" method="POST" id="registerForm" enctype="multipart/form-data">
            <div class="nd-form-container">
                <div class="nd-input-group">
                    <i class="fas fa-user"></i>
                    <input style="outline: none;" type="text" name="fullname" id="fullname" placeholder="Họ và tên" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-user-circle"></i>
                    <input style="outline: none;" type="text" name="username" id="username" placeholder="Tên đăng nhập" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-envelope"></i>
                    <input style="outline: none;" type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-key"></i>
                    <input style="outline: none;" type="password" name="password" id="password" placeholder="Mật khẩu" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-phone"></i>
                    <input style="outline: none;" type="text" name="phone" id="phone" placeholder="Số điện thoại" required>
                </div>
                <div class="nd-input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <input style="outline: none;" type="text" name="address" placeholder="Địa chỉ" required>
                </div>
                <div class="nd-input-group full-width">
                    <i class="fas fa-user-tag"></i>
                    <select style="outline: none;" name="role" required>
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
            <?php
                require_once "../config.php";
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['themnd'])) {
                        // Lấy dữ liệu từ form
                        $fullname = $_POST['fullname'];
                        $username = $_POST['username'];
                        $email = $_POST['email'];
                        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        $phone = $_POST['phone'];
                        $address = $_POST['address'];
                        $role = $_POST['role'];
                        $target_file = "picture/user.png";
                        $kq = themNguoiDung($fullname, $username, $email, $password, $phone, $address, $role, $target_file); 
                        if ($kq) {
                            echo "
                            <script>
                                showCustomAlert('🐳 Thêm Thành Công!', 'Người dùng đã được thêm vào danh sách!', '../picture/success.png');
                                setTimeout(function() {
                                    goBack();
                                }, 3000); 
                            </script>";
                        } else {
                            echo "
                            <script>
                                showCustomAlert('🐳 Thêm Không Thành Công!', '$kq', '../picture/error.png');
                            </script>";
                        }
                    }
                }
            ?>
        </form>
    </div>
</body>
</html>
<script>
    function isValidPassword(password) {
        // Kiểm tra độ dài tối thiểu
        if (password.length < 6) return false;

        let hasLower = false;
        let hasUpper = false;
        let hasDigit = false;
        let hasSpecial = false;
        const specialChars = "!@#$%^&*()-_=+[]{}|;:'\",.<>?/`~\\";

        for (let i = 0; i < password.length; i++) {
            let char = password[i];

            if (char >= 'a' && char <= 'z') hasLower = true;
            else if (char >= 'A' && char <= 'Z') hasUpper = true;
            else if (char >= '0' && char <= '9') hasDigit = true;
            else if (specialChars.includes(char)) hasSpecial = true;
            
            // Nếu phát hiện ký tự có dấu, trả về false ngay
            if (char !== char.normalize("NFD")) {
                return false;
            }
        }

        // Nếu thiếu bất kỳ điều kiện nào, trả về false
        if (!hasLower || !hasUpper || !hasDigit || !hasSpecial) {
            return false;
        }

        return true;
    }
    document.getElementById("registerForm").addEventListener("submit", function (event) {
        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("password").value;
        let sdt = document.getElementById("phone").value;
        let hoten = document.getElementById("fullname").value;

        let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let phonePattern = /^(0\d{9,10})$/;
        let namePattern = /^[a-zA-ZÀ-ỹ\s]+$/;

        if (!emailPattern.test(email)) {
            // showCustomAlert("🐳 Oops!", "Email không hợp lệ! Vui lòng nhập đúng định dạng.", "warning");
            event.preventDefault();
        }

        if (!isValidPassword(password)) {
            // showCustomAlert("🐳 Oops!", "Mật khẩu phải có ít nhất 6 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt!", "warning");
            event.preventDefault();
        }

        if (!phonePattern.test(sdt)) {
            // showCustomAlert("🐳 Oops!", "Số điện thoại không hợp lệ! Số điện thoại phải bắt đầu bằng số 0 và có 10 hoặc 11 số.", "warning");
            event.preventDefault();
        }

        if (!namePattern.test(hoten)) {
            event.preventDefault();
        }
        });

        document.getElementById("phone").addEventListener("input", function () {
            let sdtInput = this.value.replace(/\D/g, ""); // Chỉ giữ lại số

            if (sdtInput.length > 11) {
                sdtInput = sdtInput.slice(0, 11);
            }

            this.value = sdtInput;
        });

        function generateRandomString(length) {
            const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
            let result = "";
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        function removeDiacritics(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, "").toLowerCase();
        }

        document.getElementById("fullname").addEventListener("blur", function () {
            let hotenInput = this.value.trim().replace(/[^a-zA-ZÀ-ỹ\s]/g, "").replace(/\s+/g, " ");
            hotenInput = hotenInput.toLowerCase().replace(/(?:^|\s)\p{L}/gu, match => match.toUpperCase());
            this.value = hotenInput;

            // Nếu có họ tên mới tạo tên đăng nhập
            if (hotenInput) {
                let nameForUsername = removeDiacritics(hotenInput.split(" ").pop());
                let randomString = generateRandomString(4);
                document.querySelector("input[name='username']").value = nameForUsername + randomString;
            } else {
                document.querySelector("input[name='username']").value = "";
            }
        });
       
</script>