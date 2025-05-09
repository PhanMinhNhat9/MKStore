<?php
require_once '../config.php';

$errors = [];
$current_date = date('Y-m-d'); // Ngày hiện tại: 2025-05-10

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['themmgg'])) {
    $code = trim($_POST['code']);
    $phantram = intval($_POST['phantram']);
    $ngayhieuluc = $_POST['ngayhieuluc'];
    $ngayketthuc = $_POST['ngayketthuc'];

    // Ràng buộc code
    if (empty($code)) {
        $errors['code'] = "Vui lòng nhập mã giảm giá.";
    } elseif (!preg_match('/^[a-zA-Z0-9\-]+$/', $code)) {
        $errors['code'] = "Mã giảm giá chỉ được chứa chữ, số và dấu -.";
    } elseif (strlen($code) < 4 || strlen($code) > 20) {
        $errors['code'] = "Mã giảm giá phải từ 4 đến 20 ký tự.";
    } else {
        // Kiểm tra mã duy nhất
        $pdo = connectDatabase();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM magiamgia WHERE code = :code");
        $stmt->execute(['code' => $code]);
        if ($stmt->fetchColumn() > 0) {
            $errors['code'] = "Mã giảm giá đã tồn tại.";
        }
    }

    // Ràng buộc phantram
    if (empty($phantram)) {
        $errors['phantram'] = "Vui lòng nhập phần trăm giảm.";
    } elseif ($phantram < 1 || $phantram > 100) {
        $errors['phantram'] = "Phần trăm giảm phải từ 1 đến 100.";
    }

    // Ràng buộc ngayhieuluc
    if (empty($ngayhieuluc)) {
        $errors['ngayhieuluc'] = "Vui lòng nhập ngày hiệu lực.";
    } elseif (strtotime($ngayhieuluc) < strtotime($current_date)) {
        $errors['ngayhieuluc'] = "Ngày hiệu lực phải từ hôm nay trở đi.";
    }

    // Ràng buộc ngayketthuc
    if (empty($ngayketthuc)) {
        $errors['ngayketthuc'] = "Vui lòng nhập ngày kết thúc.";
    } elseif (strtotime($ngayketthuc) < strtotime($ngayhieuluc)) {
        $errors['ngayketthuc'] = "Ngày kết thúc phải từ ngày hiệu lực trở đi.";
    }

    // Nếu không có lỗi, tiến hành thêm mã giảm giá
    if (empty($errors)) {
        $kq = themMGG($code, $phantram, $ngayhieuluc, $ngayketthuc);
        if ($kq) {
            echo "
                <script>
                    showCustomAlert('🐳 Thêm Thành Công!', 'Mã giảm giá đã được thêm vào danh sách!', '../picture/success.png');
                    setTimeout(function() {
                        goBack();
                    }, 3000);
                </script>";
        } else {
            $errors['general'] = "Không thể thêm mã giảm giá. Vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Mã Giảm Giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-container {
            max-width: 600px;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 1.5rem;
        }
        .form-control {
            font-size: 0.9rem;
        }
        .btn-custom {
            background: #1e3a8a;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            border: none;
            width: 100%;
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fas fa-tag"></i> Thêm Mã Giảm Giá</h2>
        <form action="#" method="post" id="discountForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code"><i class="fas fa-barcode"></i> Mã giảm giá</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Nhập mã giảm giá" pattern="[a-zA-Z0-9\-]+" minlength="4" maxlength="20" required>
                    <div class="error" id="codeError"><?= isset($errors['code']) ? $errors['code'] : 'Mã giảm giá chỉ được chứa chữ, số và dấu - (4-20 ký tự).' ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phantram"><i class="fas fa-percent"></i> Phần trăm giảm</label>
                    <input type="number" id="phantram" name="phantram" class="form-control" placeholder="Nhập phần trăm giảm" min="1" max="100" required>
                    <div class="error" id="phantramError"><?= isset($errors['phantram']) ? $errors['phantram'] : 'Phần trăm giảm phải từ 1 đến 100.' ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ngayhieuluc"><i class="fas fa-calendar-alt"></i> Ngày hiệu lực</label>
                    <input type="date" id="ngayhieuluc" name="ngayhieuluc" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    <div class="error" id="ngayhieulucError"><?= isset($errors['ngayhieuluc']) ? $errors['ngayhieuluc'] : 'Ngày hiệu lực phải từ hôm nay trở đi.' ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ngayketthuc"><i class="fas fa-calendar-times"></i> Ngày kết thúc</label>
                    <input type="date" id="ngayketthuc" name="ngayketthuc" class="form-control" required>
                    <div class="error" id="ngayketthucError"><?= isset($errors['ngayketthuc']) ? $errors['ngayketthuc'] : 'Ngày kết thúc phải từ ngày hiệu lực trở đi.' ?></div>
                </div>
            </div>

            <div class="nhomnut-nd">
                <input type="submit" name="themmgg" value="Thêm Mã">
                <input type="button" id="nd-trove" onclick="goBack()" value="Trở về">
            </div>
        </form>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        // Kiểm tra định dạng client-side
        const form = document.getElementById('discountForm');
        const inputs = form.querySelectorAll('input');
        const ngayhieulucInput = document.getElementById('ngayhieuluc');
        const ngayketthucInput = document.getElementById('ngayketthuc');

        function validateInput(input) {
            const errorElement = document.getElementById(`${input.id}Error`);
            if (input.value.trim() !== '' || input.required) {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    errorElement.style.display = 'block';
                } else {
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                }
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
            }
        }

        function validateDates() {
            const ngayhieuluc = ngayhieulucInput.value;
            const ngayketthuc = ngayketthucInput.value;
            const ngayhieulucError = document.getElementById('ngayhieulucError');
            const ngayketthucError = document.getElementById('ngayketthucError');

            if (ngayhieuluc && ngayketthuc && new Date(ngayketthuc) < new Date(ngayhieuluc)) {
                ngayketthucInput.classList.add('is-invalid');
                ngayketthucError.textContent = 'Ngày kết thúc phải từ ngày hiệu lực trở đi.';
                ngayketthucError.style.display = 'block';
                return false;
            } else {
                ngayketthucInput.classList.remove('is-invalid');
                ngayketthucError.style.display = 'none';
            }
            return true;
        }

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                validateInput(input);
                if (input.id === 'ngayhieuluc' || input.id === 'ngayketthuc') {
                    validateDates();
                }
            });
            input.addEventListener('blur', () => {
                validateInput(input);
                if (input.id === 'ngayhieuluc' || input.id === 'ngayketthuc') {
                    validateDates();
                }
            });
        });

        form.addEventListener('submit', (e) => {
            let hasError = false;
            inputs.forEach(input => {
                if (input.required && input.value.trim() === '') {
                    input.classList.add('is-invalid');
                    const errorElement = document.getElementById(`${input.id}Error`);
                    errorElement.textContent = `Vui lòng nhập ${input.placeholder.toLowerCase()}.`;
                    errorElement.style.display = 'block';
                    hasError = true;
                } else if (!input.checkValidity()) {
                    validateInput(input);
                    hasError = true;
                }
            });

            if (!validateDates()) {
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            }
        });

        // Hiển thị lỗi server-side nếu có
        <?php if (!empty($errors['general'])): ?>
            showCustomAlert('🐳 Thêm Không Thành Công!', '<?= htmlspecialchars($errors['general']) ?>', '../picture/error.png');
        <?php endif; ?>
    </script>
</body>
</html>