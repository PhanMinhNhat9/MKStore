<?php
    include "../config.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $hoten = $_POST['hoten'];
            $tendn = $_POST['tendn'];
            $mk = $_POST['password'];
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
                echo "<script> alert('Gửi email thất bại!'); </script>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Đăng ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="../picture/logoTD.png" type="image/png">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { inter: ['Inter', 'sans-serif'] },
                    animation: {
                        'gradient': 'gradient 10s ease infinite',
                        'pulse': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        gradient: {
                            '0%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                            '100%': { backgroundPosition: '0% 50%' },
                        },
                        pulse: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.5' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6, #7c3aed, #db2777);
            background-size: 400% 400%;
            animation: gradient 10s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            padding: 1rem;
        }
        #showPassword:checked::after {
            content: '✔';
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.9rem;
            width: 100%;
            height: 100%;
        }
        input:focus {
            outline: none;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        #loi {
            color: #ff4d4d;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        @media (max-width: 640px) {
            .bgkhung {
                width: 100%;
                max-width: 340px;
                padding: 1.5rem;
            }
            label {
                font-size: 0.95rem;
            }
            input {
                font-size: 1rem;
                padding: 0.75rem;
            }
            button {
                font-size: 1.1rem;
                padding: 0.85rem;
            }
            .text-sm {
                font-size: 0.9rem;
            }
            body {
                padding-bottom: 10rem;
            }
            .grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 400px) {
            .bgkhung {
                max-width: 300px;
            }
            input, button {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <form method="post" class="bgkhung bg-white/10 backdrop-blur-2xl p-6 rounded-3xl shadow-xl w-full max-w-3xl mx-auto grid grid-cols-2 gap-4 text-white transition-all duration-300" id="registerForm">
        <div class="col-span-2 flex justify-center border-b border-white/20 pb-4">
            <span class="text-2xl sm:text-3xl font-semibold tracking-tight">Đăng ký tài khoản</span>
        </div>
        <div>
            <label class="text-sm sm:text-base font-medium text-gray-100">Họ và tên</label>
            <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                <i class="fa-solid fa-user text-white mr-3 text-lg"></i>
                <input type="text" id="hoten" name="hoten" onkeypress="validateInput(event)"
                       class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none" required autocomplete="off"
                       placeholder="Nhập họ và tên">
            </div>
        </div>
        <div>
            <label class="text-sm sm:text-base font-medium text-gray-100">Tên đăng nhập</label>
            <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                <i class="fa-solid fa-user-circle text-white mr-3 text-lg"></i>
                <input type="text" id="tendn" name="tendn" oninput="removeInvalidChars(this)"
                       class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none" required autocomplete="off"
                       placeholder="Nhập tên đăng nhập">
            </div>
        </div>
        <div>
            <label class="text-sm sm:text-base font-medium text-gray-100">Email</label>
            <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                <i class="fa-solid fa-envelope text-white mr-3 text-lg"></i>
                <input type="email" id="email" name="email"
                       class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none" required autocomplete="off"
                       placeholder="Nhập email">
            </div>
        </div>
        <div>
            <label class="text-sm sm:text-base font-medium text-gray-100">Mật khẩu</label>
            <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                <i class="fa-solid fa-lock text-white mr-3 text-lg"></i>
                <input type="password" id="password" name="password"
                       class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none" required
                       placeholder="Nhập mật khẩu">
            </div>
        </div>
        <div>
            <label class="text-sm sm:text-base font-medium text-gray-100">Số điện thoại</label>
            <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                <i class="fa-solid fa-phone text-white mr-3 text-lg"></i>
                <input type="text" id="sdt" name="sdt"
                       class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none" required autocomplete="off"
                       placeholder="Nhập số điện thoại">
            </div>
        </div>
        <div>
            <label class="text-sm sm:text-base font-medium text-gray-100">Địa chỉ</label>
            <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                <i class="fa-solid fa-map-marker-alt text-white mr-3 text-lg"></i>
                <input type="text" name="diachi"
                       class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none" required autocomplete="off"
                       placeholder="Nhập địa chỉ">
            </div>
        </div>
        <div class="col-span-2 mt-4 flex flex-col items-center space-y-4">
            <div class="flex items-center gap-3 text-sm sm:text-base text-gray-200">
                <input type="checkbox" id="showPassword" 
                       class="appearance-none h-6 w-6 rounded bg-white/20 checked:bg-blue-600 checked:text-white flex items-center justify-center transition duration-200 ease-in-out cursor-pointer" />
                <label for="showPassword" class="cursor-pointer">Hiển thị mật khẩu</label>
            </div>
            <button type="submit"
                    class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3.5 px-4 rounded-lg font-semibold shadow-lg transition-all duration-300 flex items-center justify-center relative overflow-hidden group touch-manipulation">
                <span class="relative z-10">Đăng ký</span>
                <span class="absolute inset-0 bg-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>
            <div class="text-sm sm:text-base text-center text-gray-200">
                <span>Tôi đã có tài khoản: </span>
                <a href="../auth/dangnhap.php" class="hover:text-blue-300 transition-colors">Đăng nhập</a>
            </div>
            <span id="loi" class="bg-red-600/90 text-white text-sm sm:text-base text-center px-4 py-3 rounded-lg shadow-md animate-pulse hidden"></span>
        </div>
    </form>
    <script>
        document.getElementById("showPassword").addEventListener("change", function () {
            let passwordInput = document.getElementById("password");
            passwordInput.type = this.checked ? "text" : "password";
        });

        function isValidPassword(password) {
            if (password.length < 6) return false;
            let hasLower = /[a-z]/.test(password);
            let hasUpper = /[A-Z]/.test(password);
            let hasDigit = /\d/.test(password);
            let hasSpecial = /[!@#$%^&*]/.test(password);
            return hasLower && hasUpper && hasDigit && hasSpecial;
        }

        document.getElementById("registerForm").addEventListener("submit", function (e) {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const phone = document.getElementById("sdt").value.trim();
            const hoten = document.getElementById("hoten").value.trim();
            const loiSpan = document.getElementById("loi");
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^0\d{9,10}$/;
            const nameRegex = /^[a-zA-ZÀ-ỹ\s]+$/;

            loiSpan.textContent = "";
            loiSpan.classList.add("hidden");

            if (!emailRegex.test(email) || !isValidPassword(password) || !phoneRegex.test(phone) || !nameRegex.test(hoten)) {
                e.preventDefault();
                loiSpan.textContent = "Thông tin không hợp lệ. Vui lòng kiểm tra lại.";
                loiSpan.classList.remove("hidden");
            }
        });

        function validateInput(event) {
            const char = String.fromCharCode(event.which || event.keyCode);
            const invalidChars = /[~`!@#$%^&*()_\-+=\{\}\[\]\\|:;"'<,>.?/]/;
            if (/\d/.test(char) || invalidChars.test(char)) {
                event.preventDefault();
            }
        }

        function removeInvalidChars(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9]/g, '');
        }

        document.getElementById("hoten").addEventListener("blur", function () {
            const fullName = this.value.trim();
            const usernameInput = document.querySelector("input[name='tendn']");

            if (fullName !== "") {
                const capitalizedName = fullName
                    .split(" ")
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                    .join(" ");
                this.value = capitalizedName;
                const parts = capitalizedName.split(" ");
                const lastName = removeDiacritics(parts[parts.length - 1]);
                usernameInput.value = lastName + generateRandomString(4);
            } else {
                usernameInput.value = "";
            }
        });

        document.getElementById("sdt").addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 11);
        });

        function generateRandomString(length) {
            const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
            return Array.from({ length }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
        }

        function removeDiacritics(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, "").toLowerCase();
        }
    </script>
</body>
</html>