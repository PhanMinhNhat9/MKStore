<?php
require_once '../config.php';

$errors = [];
$loaidm = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy danh sách danh mục cha để hiển thị trong dropdown
$pdo = connectDatabase();
$stmt = $pdo->query("SELECT iddm, tendm FROM danhmucsp WHERE loaidm = 0 ORDER BY tendm");
$parent_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['themdmcon'])) {
    $tendm = trim($_POST['tendm']);
    $loaidm = isset($_POST['loaidm']) ? intval($_POST['loaidm']) : 0;
    $mota = trim($_POST['mota']);

    // Ràng buộc tendm
    if (empty($tendm)) {
        $errors['tendm'] = "Vui lòng nhập tên danh mục.";
    } elseif (!preg_match('/^[\p{L}0-9\s\-\/\(\)\.]+$/u', $tendm)) {
        $errors['tendm'] = "Tên danh mục chỉ được chứa chữ, số, dấu cách, -, /, (), .";
    } elseif (mb_strlen($tendm, 'UTF-8') > 100) {
        $errors['tendm'] = "Tên danh mục không được vượt quá 100 ký tự.";
    }

    // Ràng buộc loaidm
    if ($loaidm < 0) {
        $errors['loaidm'] = "Loại danh mục phải là số không âm.";
    } elseif ($loaidm > 0) { // Chỉ kiểm tra tồn tại nếu loaidm > 0
        $stmt = $pdo->prepare("SELECT iddm FROM danhmucsp WHERE iddm = :loaidm");
        $stmt->execute(['loaidm' => $loaidm]);
        if (!$stmt->fetch()) {
            $errors['loaidm'] = "Loại danh mục không tồn tại.";
        }
    }

    // Ràng buộc mota
    if (!empty($mota)) {
        if (!preg_match('/^[\p{L}0-9\s\-\/\(\)\.]+$/u', $mota)) {
            $errors['mota'] = "Mô tả chỉ được chứa chữ, số, dấu cách, -, /, (), .";
        } elseif (mb_strlen($mota, 'UTF-8') > 500) {
            $errors['mota'] = "Mô tả không được vượt quá 500 ký tự.";
        }
    }

    // Ràng buộc icon
    $target_file = '';
    if (!empty($_FILES['icon']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_type = $_FILES['icon']['type'];
        $file_size = $_FILES['icon']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errors['icon'] = "Icon phải là định dạng JPG, PNG hoặc GIF.";
        } elseif ($file_size > $max_size) {
            $errors['icon'] = "Icon không được vượt quá 2MB.";
        } else {
            // Xử lý upload icon
            $target_dir = "../icon/";
            $file_extension = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
            $target_file = $target_dir . uniqid() . '.' . $file_extension;
            if (!move_uploaded_file($_FILES['icon']['tmp_name'], $target_file)) {
                $errors['icon'] = "Lỗi khi upload icon.";
            }
        }
    }

    // Nếu không có lỗi, tiến hành thêm danh mục
    if (empty($errors)) {
        $kq = themDMcon($tendm, $loaidm, $mota, $target_file);
        if ($kq['success']) {
            echo "<script>window.top.location.href = '../trangchu.php?status=themdmT';</script>";
        } else {
            $errors['general'] = $kq['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục con</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
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
        .form-control, .form-control-sm {
            font-size: 0.9rem;
        }
        .btn-custom {
            background: #1e3a8a;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            border: none;
            width: 100%;
            margin-top: 1rem;
        }
        .error {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.2rem;
            display: none;
        }
        .is-invalid + .error, .is-invalid ~ .error {
            display: block;
        }
        .nhomnut-nd {
            text-align: center;
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .nhomnut-nd button, .nhomnut-nd input[type="button"] {
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
        }
        .nhomnut-nd button[type="submit"] {
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
        <h2>Thêm danh mục con</h2>
        <form action="#" method="POST" enctype="multipart/form-data" id="categoryForm">
            <!-- Dòng 1: Tên danh mục + Loại danh mục -->
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="tendm"><i class="fas fa-tag"></i> Tên danh mục</label>
                    <input type="text" id="tendm" name="tendm" class="form-control" placeholder="Nhập tên danh mục" pattern="[\p{L}0-9\s\-\/\(\)\.]+" maxlength="100" required>
                    <div class="error" id="tendmError"><?= isset($errors['tendm']) ? $errors['tendm'] : 'Tên danh mục chỉ được chứa chữ, số, dấu cách, -, /, (), .' ?></div>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="loaidm"><i class="fas fa-list"></i> Loại danh mục</label>
                    <select id="loaidm" name="loaidm" class="form-control" required>
                        <option value="0" <?= $loaidm == 0 ? 'selected' : '' ?>>Danh mục gốc</option>
                        <?php foreach ($parent_categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['iddm']) ?>" <?= $loaidm == $category['iddm'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['tendm']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error" id="loaidmError"><?= isset($errors['loaidm']) ? $errors['loaidm'] : 'Vui lòng chọn loại danh mục.' ?></div>
                </div>
            </div>

            <!-- Dòng 2: Chọn Icon + Mô tả -->
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="icon"><i class="fas fa-image"></i> Chọn Icon</label>
                    <input type="file" id="icon" name="icon" class="form-control" accept="image/jpeg,image/png,image/gif">
                    <div class="error" id="iconError"><?= isset($errors['icon']) ? $errors['icon'] : 'Icon phải là JPG, PNG, GIF và không quá 2MB.' ?></div>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="mota"><i class="fas fa-edit"></i> Mô tả</label>
                    <textarea id="mota" name="mota" class="form-control" rows="2" placeholder="Nhập mô tả" pattern="[\p{L}0-9\s\-\/\(\)\.]*" maxlength="500"></textarea>
                    <div class="error" id="motaError"><?= isset($errors['mota']) ? $errors['mota'] : 'Mô tả chỉ được chứa chữ, số, dấu cách, -, /, (), .' ?></div>
                </div>
            </div>

            <!-- Nhóm nút -->
            <div class="nhomnut-nd">
                <button type="submit" name="themdmcon" class="btn-custom">
                    <i class="fas fa-plus-circle"></i> Thêm danh mục
                </button>
                <input type="button" id="nd-trove" onclick="goBack()" value="Trở về">
            </div>
        </form>
    </div>

    <script>
        function goBack() {
            window.top.location.href = "../trangchu.php";
        }

        // Kiểm tra định dạng client-side
        const form = document.getElementById('categoryForm');
        const inputs = form.querySelectorAll('input, textarea, select');

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

        function validateFileInput() {
            const fileInput = document.getElementById('icon');
            const errorElement = document.getElementById('iconError');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    fileInput.classList.add('is-invalid');
                    errorElement.textContent = 'Icon phải là định dạng JPG, PNG hoặc GIF.';
                    errorElement.style.display = 'block';
                    return false;
                }

                if (file.size > maxSize) {
                    fileInput.classList.add('is-invalid');
                    errorElement.textContent = 'Icon không được vượt quá 2MB.';
                    errorElement.style.display = 'block';
                    return false;
                }

                fileInput.classList.remove('is-invalid');
                errorElement.style.display = 'none';
                return true;
            }
            fileInput.classList.remove('is-invalid');
            errorElement.style.display = 'none';
            return true;
        }

        inputs.forEach(input => {
            input.addEventListener('input', () => validateInput(input));
            input.addEventListener('blur', () => validateInput(input));
            if (input.type === 'file') {
                input.addEventListener('change', validateFileInput);
            }
        });

        form.addEventListener('submit', (e) => {
            let hasError = false;
            inputs.forEach(input => {
                if (input.required && input.value.trim() === '') {
                    input.classList.add('is-invalid');
                    const errorElement = document.getElementById(`${input.id}Error`);
                    errorElement.textContent = `Vui lòng ${input.tagName === 'SELECT' ? 'chọn' : 'nhập'} ${input.placeholder?.toLowerCase() || input.labels[0].textContent.toLowerCase()}.`;
                    errorElement.style.display = 'block';
                    hasError = true;
                } else if (!input.checkValidity()) {
                    validateInput(input);
                    hasError = true;
                }
            });

            if (!validateFileInput()) {
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