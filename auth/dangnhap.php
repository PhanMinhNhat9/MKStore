<?php
    // Kết nối với tệp cấu hình để sử dụng cơ sở dữ liệu và thiết lập phiên
    require_once '../config.php';
      
    // Khởi tạo biến lỗi
    $error = "";

    // Xử lý yêu cầu POST khi người dùng gửi biểu mẫu đăng nhập
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Kiểm tra mã CSRF để ngăn chặn tấn công giả mạo yêu cầu
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Yêu cầu không hợp lệ");
        }

        // Làm sạch dữ liệu đầu vào
        $tendn = isset($_POST['tendn']) ? trim($_POST['tendn']) : '';
        $matkhau = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';

        // Kiểm tra xem tên đăng nhập và mật khẩu có được cung cấp không
        if (!empty($tendn) && !empty($matkhau)) {
            // Thực hiện đăng nhập với thông tin được cung cấp
            $error = dangnhap($tendn, $matkhau);
            if (!empty($error)) {
                // Lưu thông báo lỗi vào phiên và chuyển hướng
                $_SESSION['login_error'] = $error;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            // Lưu thông báo lỗi trống và chuyển hướng
            $_SESSION['login_error'] = "";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Đặt lại số lần thử đăng nhập nếu thời gian khóa đã hết
    if ($_SESSION['login_attempts'] >= 2 && time() > $_SESSION['lock_time']) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lock_time'] = 0;
        $error = "";
        $tendn = "";
        $matkhau = "";
    }

    // Tính thời gian khóa còn lại
    $lockTime = $_SESSION['lock_time'] - time();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <!-- Thiết lập mã hóa ký tự và giao diện responsive -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    
    <!-- Tiêu đề trang -->
    <title>Đăng nhập</title>

    <!-- Kết nối các tệp CSS và JavaScript bên ngoài -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="../picture/logoTD.png" type="image/png">

    <!-- Cấu hình Tailwind CSS -->
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

    <!-- CSS nội bộ -->
    <style>
        /* Thiết lập phông chữ toàn cục và nền gradient động */
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

        /* Tùy chỉnh giao diện checkbox hiển thị mật khẩu */
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

        /* Tùy chỉnh input khi focus */
        input:focus {
            outline: none;
        }

        /* Hiệu ứng hover và active cho nút */
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Responsive cho thiết bị di động */
        @media (max-width: 640px) {
            .bgkhung {
                width: 100%;
                max-width: 340px;
                padding: 1.5rem;
            }

            /* Tăng kích thước chữ và trường nhập */
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

            /* Đảm bảo khung đăng nhập không bị che bởi bàn phím ảo */
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
    <!-- Khung đăng nhập với hiệu ứng kính mờ -->
    <div class="bgkhung bg-white/10 backdrop-blur-2xl p-6 rounded-3xl shadow-xl text-white w-full max-w-md space-y-6 transition-all duration-300">
        <!-- Phần tiêu đề -->
        <div class="flex justify-center border-b border-white/20 pb-4">
            <span class="text-2xl sm:text-3xl font-semibold tracking-tight">Đăng nhập</span>
        </div>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (!empty($_SESSION['login_error'])): ?>
            <div id="loi" class="bg-red-600/90 text-white text-sm sm:text-base text-center px-4 py-3 rounded-lg shadow-md animate-pulse">
                <?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị đồng hồ đếm ngược nếu tài khoản bị khóa -->
        <?php if ($lockTime > 0): ?>
            <div id="countdown" class="text-amber-300 text-sm sm:text-base text-center font-medium bg-gray-800/50 px-4 py-2 rounded-lg"></div>
        <?php endif; ?>

        <!-- Biểu mẫu đăng nhập -->
        <form method="POST" class="space-y-5">
            <!-- Mã CSRF để bảo mật -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <!-- Trường nhập tên đăng nhập -->
            <div>
                <label class="text-sm sm:text-base font-medium text-gray-100">Tên đăng nhập</label>
                <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                    <i class="fa-solid fa-user text-white mr-3 text-lg"></i>
                    <input type="text" name="tendn"
                           class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                           placeholder="Nhập tên đăng nhập"
                           required autocomplete="username">
                </div>
            </div>

            <!-- Trường nhập mật khẩu -->
            <div>
                <label class="text-sm sm:text-base font-medium text-gray-100">Mật khẩu</label>
                <div class="flex items-center bg-white/10 rounded-lg px-3 py-2.5 mt-1 transition-all duration-300 hover:bg-white/20">
                    <i class="fa-solid fa-lock text-white mr-3 text-lg"></i>
                    <input type="password" name="matkhau" id="password"
                           class="w-full bg-transparent text-white placeholder-gray-400 focus:outline-none"
                           placeholder="Nhập mật khẩu"
                           required autocomplete="current-password">
                </div>
            </div>

            <!-- Checkbox hiển thị mật khẩu -->
            <div class="flex items-center gap-3 text-sm sm:text-base text-gray-200">
                <input type="checkbox" id="showPassword" 
                       class="appearance-none h-6 w-6 rounded bg-white/20 checked:bg-blue-600 
                       checked:text-white flex items-center justify-center transition duration-200 ease-in-out cursor-pointer" />
                <label for="showPassword" class="cursor-pointer">Hiển thị mật khẩu</label>
            </div>

            <!-- Nút gửi biểu mẫu -->
            <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 
                    hover:to-indigo-500 text-white py-3.5 rounded-lg font-semibold shadow-lg transition-all 
                    duration-300 flex items-center justify-center relative overflow-hidden group touch-manipulation">
                <span class="relative z-10">Đăng nhập</span>
                <span class="absolute inset-0 bg-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>

            <!-- Liên kết quên mật khẩu và đăng ký -->
            <div class="text-sm sm:text-base text-center text-gray-200">
                <a href="../auth/quenmk.php" class="hover:text-blue-300 transition-colors">Quên mật khẩu?</a> |
                <a href="../auth/dangky.php" class="hover:text-blue-300 transition-colors">Đăng ký</a>
            </div>
        </form>
    </div>

    <!-- JavaScript cho chức năng phía client -->
    <script>
        // Chuyển đổi hiển thị mật khẩu
        document.getElementById("showPassword").addEventListener("change", function () {
            let passwordInput = document.getElementById("password");
            passwordInput.type = this.checked ? "text" : "password";
        });

        // Đồng hồ đếm ngược cho tài khoản bị khóa
        <?php if ($lockTime > 0): ?>
            let lockTime = <?= $lockTime ?>;
            const countdown = document.getElementById("countdown");
            const loi = document.getElementById("loi");

            function updateCountdown() {
                let m = Math.floor(lockTime / 60);
                let s = lockTime % 60;
                countdown.textContent = `⏳ Vui lòng thử lại sau: ${m} phút ${s} giây`;
                if (lockTime > 0) {
                    lockTime--;
                    setTimeout(updateCountdown, 1000);
                } else {
                    countdown.textContent = "✅ Bạn có thể đăng nhập lại.";
                    loi.style.display = "none"; 
                }
            }
            updateCountdown();
        <?php endif; ?>
    </script>
</body>
</html>