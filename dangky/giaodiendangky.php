<?php
    include "../config.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $hoten = $_POST['hoten'];
            $tendn = $_POST['tendn'];
            $mk = $_POST['mk'];
            $sdt = $_POST['sdt'];
            $diachi = $_POST['diachi'];
           
            $verificationCode = rand(100000, 999999);
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['email_temp'] = $email;
            $_SESSION['hoten_temp'] = $hoten;
            $_SESSION['tendn_temp'] = $tendn;
            $_SESSION['mk_temp'] = $mk;
            $_SESSION['sdt_temp'] = $sdt;
            $_SESSION['diachi_temp'] = $diachi;
            if (sendVerificationEmail($email, $verificationCode)) {
                echo "<script> alert('Mã xác thực đã được gửi!'); 
                               window.location.href = 'xacthuc.php';
                     </script>";

            } else {
                echo "Gửi email thất bại!";
            }
        } else {
            echo "<script> alert('Vui lòng nhập email!'); </script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="giaodiendangky.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
</head>
<body>
    <div class="register-container">
        <h2>Đăng Ký</h2>
        <form id="registerForm" action="#" method="POST">
            <div class="input-row">
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="hoten" id="hoten" placeholder="Họ và Tên" autocomplete="off" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="tendn" placeholder="Tên đăng nhập" autocomplete="off" required>
                </div>
            </div>
            <div class="input-row">
                <div class="input-group">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email" autocomplete="off" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="mk" id="password" placeholder="Mật khẩu" autocomplete="off" required>
                </div>
            </div>
            <div class="input-row">
                <div class="input-group">
                    <i class="fa fa-phone"></i>
                    <input type="text" name="sdt" id="sdt" placeholder="Số điện thoại" autocomplete="off" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-map-marker-alt"></i>
                    <input type="text" name="diachi" placeholder="Địa chỉ" autocomplete="off"  required>
                </div>
            </div>
            <input type="submit" name="dangkytkuser" value="Đăng ký">
        </form>
    </div>
    <script>
        document.getElementById("registerForm").addEventListener("submit", function (event) {
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value;
            let sdt = document.getElementById("sdt").value;
            let hoten = document.getElementById("hoten").value;

            let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;
            let phonePattern = /^(0\d{9,10})$/;
            let namePattern = /^[a-zA-ZÀ-ỹ\s]+$/;

            if (!emailPattern.test(email)) {
                alert("Email không hợp lệ! Vui lòng nhập đúng định dạng.");
                event.preventDefault();
            }

            if (!passwordPattern.test(password)) {
                // Swal.fire({
                //     title: "🐳 Oops!",
                //     text: "Họ tên không hợp lệ! Không chứa số hoặc ký tự đặc biệt.",
                //     icon: "warning",
                //     confirmButtonText: "Okay nè! 💙",
                //     confirmButtonColor: "#4a90e2", // Xanh dương pastel
                //     background: "#e0f7fa", // Nền xanh nhạt
                //     color: "#1565c0", // Màu chữ xanh đậm
                //     width: "300px", // Thu nhỏ kích thước popup
                //     padding: "10px", // Giảm padding
                // });

                //alert("Mật khẩu phải có ít nhất 6 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt!");
                event.preventDefault();
            }

            if (!phonePattern.test(sdt)) {
                
                // alert("Số điện thoại không hợp lệ! Số điện thoại phải bắt đầu bằng số 0 và có 10 hoặc 11 số.");
                event.preventDefault();
            }

            if (!namePattern.test(hoten)) {
                // alert("Họ tên không hợp lệ! Không chứa số hoặc ký tự đặc biệt.");
                
                event.preventDefault();
            }
        });

        document.getElementById("sdt").addEventListener("input", function () {
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

        document.getElementById("hoten").addEventListener("blur", function () {
            let hotenInput = this.value.trim().replace(/[^a-zA-ZÀ-ỹ\s]/g, "").replace(/\s+/g, " ");
            hotenInput = hotenInput.toLowerCase().replace(/(?:^|\s)\p{L}/gu, match => match.toUpperCase());
            this.value = hotenInput;

            // Nếu có họ tên mới tạo tên đăng nhập
            if (hotenInput) {
                let nameForUsername = removeDiacritics(hotenInput.split(" ").pop());
                let randomString = generateRandomString(4);
                document.querySelector("input[name='tendn']").value = nameForUsername + randomString;
            } else {
                document.querySelector("input[name='tendn']").value = "";
            }
        });

    </script>
</body>
</html>
