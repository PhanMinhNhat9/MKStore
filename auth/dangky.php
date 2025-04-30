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
  <title>Đăng ký</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../fontawesome/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="../picture/logoTD.png" type="image/png">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { poppins: ['Poppins', 'sans-serif'] }
        }
      }
    }
  </script>
  <style>
    body { 
        font-family: 'Poppins', sans-serif; 
    }
    #showPassword:checked::after {
        content: '✔';
        color: white;
        font-size: 0.75rem;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }
    body {
        background: linear-gradient(45deg, #6b7280, #3b82f6, #8b5cf6, #ec4899);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
        min-h-screen flex items-center justify-center;
    }
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-500 via-blue-400 to-blue-800">
    <form class="bg-white/20 p-6 rounded-2xl shadow-xl max-w-3xl mx-auto grid grid-cols-2 gap-4 text-white" id="registerForm">
        <!-- Đã điều chỉnh h2 để chiếm 2 cột -->
        <h2 class="col-span-2 text-center text-2xl font-semibold mb-2 border-b border-white/30 pb-3">Đăng ký tài khoản</h2>
        <!-- Cột trái -->
        <div>
            <label class="text-sm text-gray-200 block mb-1">Họ và tên</label>
            <div class="flex items-center bg-white/10 rounded-md px-3">
                <i class="fas fa-user mr-2"></i>
                <input type="text" id="hoten" name="hoten" onkeypress="validateInput(event)" class="w-full bg-transparent p-2 focus:outline-none" required autocomplete="off"/>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-200 block mb-1">Tên đăng nhập</label>
            <div class="flex items-center bg-white/10 rounded-md px-3">
                <i class="fas fa-user-circle mr-2"></i>
                <input type="text" id="tendn" name="tendn" oninput="removeInvalidChars(this)" class="w-full bg-transparent p-2 focus:outline-none" required autocomplete="off"/>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-200 block mb-1">Email</label>
            <div class="flex items-center bg-white/10 rounded-md px-3">
                <i class="fas fa-envelope mr-2"></i>
                <input type="email" id="email" name="email" class="w-full bg-transparent p-2 focus:outline-none" required autocomplete="off" />
            </div>
        </div>

        <!-- Cột phải -->
        <div>
            <label class="text-sm text-gray-200 block mb-1">Mật khẩu</label>
            <div class="flex items-center bg-white/10 rounded-md px-3">
                <i class="fas fa-lock mr-2"></i>
                <input type="password" id="password" name="password" class="w-full bg-transparent p-2 focus:outline-none" required/>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-200 block mb-1">Số điện thoại</label>
            <div class="flex items-center bg-white/10 rounded-md px-3">
                <i class="fas fa-phone mr-2"></i>
                <input type="text" id="sdt" name="sdt" class="w-full bg-transparent p-2 focus:outline-none" required autocomplete="off"/>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-200 block mb-1">Địa chỉ</label>
            <div class="flex items-center bg-white/10 rounded-md px-3">
                <i class="fas fa-map-marker-alt mr-2"></i>
                <input type="text" class="w-full bg-transparent p-2 focus:outline-none" required autocomplete="off"/>
            </div>
        </div>

        <!-- Dòng cuối: checkbox và nút -->
        <div class="col-span-2 mt-4 flex flex-col items-center space-y-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" id="showPassword" class="appearance-none h-4 w-4 rounded bg-white/20 checked:bg-blue-500 
                checked:text-white flex items-center justify-center transition duration-150 ease-in-out" />
                <span class="text-sm text-gray-200">Hiển thị mật khẩu</span>
            </label>

            <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 
                hover:to-blue-500 text-white px-4 py-2 rounded-lg font-semibold shadow-md transition mx-auto"> Đăng ký
            </button>

            <div class="text-sm text-center text-white/100">
                <span>Tôi đã có tài khoản:</span>
                <a href="../auth/dangnhap.php" class="hover:underline">Đăng nhập</a>
            </div>
        </div>
    </form>

<script>
    // Hiển thị mật khẩu
    document.getElementById("showPassword").addEventListener("change", function () {
        let passwordInput = document.getElementById("password");
        passwordInput.type = this.checked ? "text" : "password";
    });

    // Validate password
    function isValidPassword(password) {
        if (password.length < 6) return false;
        let hasLower = /[a-z]/.test(password);
        let hasUpper = /[A-Z]/.test(password);
        let hasDigit = /\d/.test(password);
        let hasSpecial = /[!@#$%^&*]/.test(password);
        return hasLower && hasUpper && hasDigit && hasSpecial;
    }

    // Validate form
    document.getElementById("registerForm").addEventListener("submit", function (e) {
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const phone = document.getElementById("sdt").value.trim();
        const hoten = document.getElementById("hoten").value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^0\d{9,10}$/;
        const nameRegex = /^[a-zA-ZÀ-ỹ\s]+$/;

        if (!emailRegex.test(email) || !isValidPassword(password) || !phoneRegex.test(phone) || !nameRegex.test(hoten)) {
        e.preventDefault();
        alert("Thông tin không hợp lệ. Vui lòng kiểm tra lại.");
        }
    });

    function validateInput(event) {
        const char = String.fromCharCode(event.which || event.keyCode);
        
        // Kiểm tra xem ký tự có phải là số hoặc ký tự đặc biệt không được phép nhập
        const invalidChars = /[~`!@#$%^&*()_\-+=\{\}\[\]\\|:;"'<,>.?/]/;
        
        // Kiểm tra xem ký tự có phải là số hoặc ký tự đặc biệt không hợp lệ
        if (/\d/.test(char) || invalidChars.test(char)) {
            event.preventDefault(); // Ngừng nhập ký tự nếu là số hoặc ký tự đặc biệt
        }
    }

    function removeInvalidChars(input) {
    // Giữ lại chỉ chữ cái không dấu và số
    input.value = input.value.replace(/[^a-zA-Z0-9]/g, '');
}

    // Tự động tạo tên đăng nhập
    function generateRandomString(length) {
        const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        return Array.from({ length }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    }

    function removeDiacritics(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, "").toLowerCase();
    }
    
    document.getElementById("hoten").addEventListener("blur", function () {
        const fullName = this.value.trim();

        const usernameInput = document.querySelector("input[name='tendn']");

        if (fullName !== "") {
            // Capitalize first letter of each word
            const capitalizedName = fullName
                .split(" ")
                .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(" ");
            this.value = capitalizedName;

            // Generate username from the last name
            const parts = capitalizedName.split(" ");
            const lastName = removeDiacritics(parts[parts.length - 1]);
            usernameInput.value = lastName + generateRandomString(4);
        } else {
            // Nếu không nhập họ tên, xóa tên đăng nhập
            usernameInput.value = "";
        }
    });

    document.getElementById("sdt").addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").slice(0, 11);
    });
</script>
</body>
</html>



