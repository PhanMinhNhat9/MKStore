<?php
require_once '../config.php';
$error = "";
$success = "";
$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["verify"])) {
        $email = trim($_POST["email"] ?? '');
        $sdt = trim($_POST["sdt"] ?? '');
        $user = verifyUser($email, $sdt);
        if ($user) {
            $_SESSION["reset_user_id"] = $user["iduser"];
            $verificationCode = rand(100000, 999999);
            $_SESSION['verification_code'] = $verificationCode;
            sendVerificationEmail($email, $verificationCode);
            $step = 2;
        } else {
            $error = "Thông tin không chính xác. Vui lòng thử lại!";
        }
    } 
    elseif (isset($_POST["xacthuc"])) {
        $maxt = $_POST["verification_code"] ?? '';
        if ($maxt == $_SESSION['verification_code']) {
            $step = 3;
        } else {
            $error = "Mã xác thực không chính xác. Vui lòng thử lại!";
        }
    }
    elseif (isset($_POST["reset_password"])) {
        $newPassword = trim($_POST["new_password"] ?? '');
        $confirmPassword = trim($_POST["confirm_password"] ?? '');

        if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $iduser = $_SESSION["reset_user_id"] ?? null;
            if ($iduser && updatePassword($iduser, $newPassword)) {
                unset($_SESSION["reset_user_id"]);
                $success = "Mật khẩu đã được cập nhật thành công! Hãy đăng nhập lại.";
                $step = 1;
            } else {
                $error = "Có lỗi xảy ra. Vui lòng thử lại!";
            }
        } else {
            $error = "Mật khẩu không khớp hoặc quá ngắn!";
            $step = 3;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Quên Mật Khẩu</title>
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
    <div class="bgkhung bg-white/10 backdrop-blur-2xl p-6 rounded-3xl shadow-xl text-white w-full max-w-md space-y-6 transition-all duration-300">
        <div class="flex justify-center border-b border-white/20 pb-4">
            <span class="text-2xl sm:text-3xl font-semibold tracking-tight">Quên Mật Khẩu</span>
        </div>
        <?php if (!empty($error)): ?>
            <div class="bg-red-600/90 text-white text-sm sm:text-base text-center px-4 py-3 rounded-lg shadow-md animate-pulse">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="bg-green-600/90 text-white text-sm sm:text-base text-center px-4 py-3 rounded-lg shadow-md animate-pulse">
                <?= $success ?>
            </div>
        <?php endif; ?>
        <?php if ($step == 1): ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="text-sm sm:text-base font-medium text-gray-100">Email</label>
                    <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                        <i class="fa-solid fa-envelope text-white mr-3 text-lg"></i>
                        <input type="email" name="email" required autocomplete="off"
                               class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                               placeholder="Nhập email">
                    </div>
                </div>
                <div>
                    <label class="text-sm sm:text-base font-medium text-gray-100">Số điện thoại</label>
                    <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                        <i class="fa-solid fa-phone text-white mr-3 text-lg"></i>
                        <input type="text" name="sdt" required autocomplete="off"
                               class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                               placeholder="Nhập số điện thoại">
                    </div>
                </div>
                <button type="submit" name="verify"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3.5 rounded-lg font-semibold shadow-lg transition-all duration-300 flex items-center justify-center relative overflow-hidden group touch-manipulation">
                    <span class="relative z-10">Xác nhận</span>
                    <span class="absolute inset-0 bg-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                </button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="text-sm sm:text-base font-medium text-gray-100">Mã xác thực</label>
                    <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                        <i class="fa-solid fa-key text-white mr-3 text-lg"></i>
                        <input type="text" name="verification_code" required autocomplete="off"
                               class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                               placeholder="Nhập mã xác thực">
                    </div>
                </div>
                <button type="submit" name="xacthuc"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3.5 rounded-lg font-semibold shadow-lg transition-all duration-300 flex items-center justify-center relative overflow-hidden group touch-manipulation">
                    <span class="relative z-10">Xác nhận</span>
                    <span class="absolute inset-0 bg-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                </button>
            </form>
        <?php elseif ($step == 3): ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="text-sm sm:text-base font-medium text-gray-100">Mật khẩu mới</label>
                    <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                        <i class="fa-solid fa-lock text-white mr-3 text-lg"></i>
                        <input type="password" name="new_password" id="new_password" required
                               class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                               placeholder="Nhập mật khẩu mới">
                    </div>
                </div>
                <div>
                    <label class="text-sm sm:text-base font-medium text-gray-100">Xác nhận mật khẩu</label>
                    <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                        <i class="fa-solid fa-lock text-white mr-3 text-lg"></i>
                        <input type="password" name="confirm_password" id="confirm_password" required
                               class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                               placeholder="Xác nhận mật khẩu">
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm sm:text-base text-gray-200">
                    <input type="checkbox" id="showPassword" 
                           class="appearance-none h-6 w-6 rounded bg-white/20 checked:bg-blue-600 checked:text-white flex items-center justify-center transition duration-200 ease-in-out cursor-pointer" />
                    <label for="showPassword" class="cursor-pointer">Hiển thị mật khẩu</label>
                </div>
                <button type="submit" name="reset_password"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3.5 rounded-lg font-semibold shadow-lg transition-all duration-300 flex items-center justify-center relative overflow-hidden group touch-manipulation">
                    <span class="relative z-10">Đặt lại mật khẩu</span>
                    <span class="absolute inset-0 bg-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                </button>
            </form>
        <?php endif; ?>
        <div class="text-sm sm:text-base text-center text-gray-200">
            <a href="../auth/dangnhap.php" class="hover:text-blue-300 transition-colors">Quay lại đăng nhập</a>
        </div>
    </div>
    <script>
        document.getElementById("showPassword")?.addEventListener("change", function () {
            let newPassword = document.getElementById("new_password");
            let confirmPassword = document.getElementById("confirm_password");
            if (newPassword && confirmPassword) {
                newPassword.type = this.checked ? "text" : "password";
                confirmPassword.type = this.checked ? "text" : "password";
            }
        });
    </script>
</body>
</html>