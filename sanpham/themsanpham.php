<?php
require_once '../config.php';

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['themsp'])) {
    $tensp = trim($_POST['tensp']);
    $giaban = floatval($_POST['giaban']);
    $soluong = intval($_POST['soluong']);
    $iddm = intval($_POST['iddm']);

    // Ràng buộc tensp
    if (empty($tensp)) {
        $errors['tensp'] = "Vui lòng nhập tên sản phẩm.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-\/\(\)\.]+$/', $tensp)) {
        $errors['tensp'] = "Tên sản phẩm chỉ được chứa chữ, số, dấu cách, -, /, (), .";
    } elseif (strlen($tensp) > 255) {
        $errors['tensp'] = "Tên sản phẩm không được vượt quá 255 ký tự.";
    }

    // Ràng buộc giaban
    if (empty($giaban)) {
        $errors['giaban'] = "Vui lòng nhập giá bán.";
    } elseif ($giaban < 1000 || $giaban > 1000000000) {
        $errors['giaban'] = "Giá bán phải từ 1,000 đến 1,000,000,000 VNĐ.";
    }

    // Ràng buộc soluong
    if (empty($soluong)) {
        $errors['soluong'] = "Vui lòng nhập số lượng.";
    } elseif ($soluong < 1 || $soluong > 10000) {
        $errors['soluong'] = "Số lượng phải từ 1 đến 10,000.";
    }

    // Ràng buộc iddm
    if (empty($iddm)) {
        $errors['iddm'] = "Vui lòng chọn danh mục.";
    } else {
        // Kiểm tra danh mục tồn tại
        $pdo = connectDatabase();
        $stmt = $pdo->prepare("SELECT iddm FROM danhmucsp WHERE iddm = :iddm AND loaidm != '0'");
        $stmt->execute(['iddm' => $iddm]);
        if (!$stmt->fetch()) {
            $errors['iddm'] = "Danh mục không hợp lệ.";
        }
    }

    // Ràng buộc ảnh
    if (empty($_FILES['anh']['name'])) {
        $errors['anh'] = "Vui lòng chọn ảnh sản phẩm.";
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $file_type = $_FILES['anh']['type'];
        $file_size = $_FILES['anh']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errors['anh'] = "Ảnh phải là định dạng JPG, PNG hoặc GIF.";
        } elseif ($file_size > $max_size) {
            $errors['anh'] = "Ảnh không được vượt quá 5MB.";
        }
    }

    // Nếu không có lỗi, tiến hành thêm sản phẩm
    if (empty($errors)) {
        // Xử lý upload ảnh
        $target_dir = "../picture/";
        $file_extension = pathinfo($_FILES['anh']['name'], PATHINFO_EXTENSION);
        $target_file = $target_dir . uniqid() . '.' . $file_extension;

        if (move_uploaded_file($_FILES['anh']['tmp_name'], $target_file)) {
            $kq = themSanPham($tensp, $giaban, $soluong, $iddm, $target_file);
            if ($kq) {
                echo "<script>window.top.location.href = '../trangchu.php?status=themspT';</script>";
            } else {
                echo "<script>window.top.location.href = '../trangchu.php?status=themspF';</script>";
            }
        } else {
            $errors['anh'] = "Lỗi khi upload ảnh.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../script.js"></script>
    <link rel="stylesheet" href="themsanpham.css?v=<?= time() ?>">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
        }
        .form-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h4 {
            color: #1e3a8a;
        }
        .form-control-sm, .form-select-sm {
            font-size: 0.9rem;
        }
        #previewContainer {
            display: none;
            margin-bottom: 1rem;
            text-align: center;
        }
        #preview {
            max-width: 200px;
            max-height: 200px;
            margin-bottom: 0.5rem;
            border-radius: 4px;
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
    <div class="container">
        <div class="form-container">
            <h4 class="text-center mb-3">Thêm Sản Phẩm</h4>
            <form action="#" method="POST" enctype="multipart/form-data" id="productForm">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="giaban" id="giaban" placeholder="Giá bán" min="1000" max="1000000000" required>
                        <div class="error" id="giabanError"><?= isset($errors['giaban']) ? $errors['giaban'] : 'Giá bán phải từ 1,000 đến 1,000,000,000 VNĐ.' ?></div>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="soluong" id="soluong" placeholder="Số lượng" min="1" max="10000" required>
                        <div class="error" id="soluongError"><?= isset($errors['soluong']) ? $errors['soluong'] : 'Số lượng phải từ 1 đến 10,000.' ?></div>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <select class="form-select form-select-sm" name="iddm" id="iddm" required>
                            <option value="" disabled selected>Chọn danh mục</option>
                            <?php
                                $pdo = connectDatabase();
                                $query = "SELECT iddm, tendm FROM danhmucsp WHERE loaidm != '0'";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['iddm']}'>{$row['tendm']}</option>";
                                }
                            ?>
                        </select>
                        <div class="error" id="iddmError"><?= isset($errors['iddm']) ? $errors['iddm'] : 'Vui lòng chọn danh mục.' ?></div>
                    </div>
                </div>

                <div class="mb-2">
                    <textarea class="form-control form-control-sm" name="tensp" id="tensp" rows="2" placeholder="Tên sản phẩm" pattern="[a-zA-Z0-9\s\-\/\(\)\.]+" maxlength="255" required></textarea>
                    <div class="error" id="tenspError"><?= isset($errors['tensp']) ? $errors['tensp'] : 'Tên sản phẩm chỉ được chứa chữ, số, dấu cách, -, /, (), .' ?></div>
                </div>

                <div class="mb-2">
                    <input type="file" class="form-control form-control-sm" id="anh" name="anh" accept="image/jpeg,image/png,image/gif" onchange="handleImage(event)" required>
                    <div class="error" id="anhError"><?= isset($errors['anh']) ? $errors['anh'] : 'Ảnh phải là JPG, PNG, GIF và không quá 5MB.' ?></div>
                </div>

                <div id="previewContainer">
                    <img id="preview" src="#" alt="Xem trước ảnh">
                    <button type="button" class="btn btn-danger btn-sm" onclick="handleImage()">Xóa ảnh</button>
                </div>

                <div class="nhomnut-nd">
                    <input type="submit" name="themsp" value="Thêm sản phẩm">
                    <input type="button" id="nd-trove" onclick="goBack()" value="Trở về">
                </div>
            </form>
        </div>
    </div>

    <script>
        function goBack() {
            window.top.location.href = "../trangchu.php";
        }

        function handleImage(event = null) {
            let fileInput = document.getElementById('anh');
            let preview = document.getElementById('preview');
            let previewContainer = document.getElementById('previewContainer');
            let errorElement = document.getElementById('anhError');

            if (event && event.target.files[0]) {
                let file = event.target.files[0];
                let allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                let maxSize = 5 * 1024 * 1024; // 5MB

                if (!allowedTypes.includes(file.type)) {
                    fileInput.classList.add('is-invalid');
                    errorElement.textContent = 'Ảnh phải là định dạng JPG, PNG hoặc GIF.';
                    errorElement.style.display = 'block';
                    fileInput.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                if (file.size > maxSize) {
                    fileInput.classList.add('is-invalid');
                    errorElement.textContent = 'Ảnh không được vượt quá 5MB.';
                    errorElement.style.display = 'block';
                    fileInput.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                let reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    fileInput.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                fileInput.value = '';
                previewContainer.style.display = 'none';
                fileInput.classList.remove('is-invalid');
                errorElement.style.display = 'none';
            }
        }

        // Kiểm tra định dạng client-side
        const form = document.getElementById('productForm');
        const inputs = form.querySelectorAll('input, textarea');
        const select = document.getElementById('iddm');

        function validateInput(input) {
            const errorElement = document.getElementById(`${input.id}Error`);
            if (input.value.trim() !== '') {
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

        function validateSelect() {
            const errorElement = document.getElementById('iddmError');
            if (select.value === '') {
                select.classList.add('is-invalid');
                errorElement.style.display = 'block';
            } else {
                select.classList.remove('is-invalid');
                errorElement.style.display = 'none';
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', () => validateInput(input));
            input.addEventListener('blur', () => validateInput(input));
        });

        select.addEventListener('change', validateSelect);
        select.addEventListener('blur', validateSelect);

        form.addEventListener('submit', (e) => {
            let hasError = false;
            inputs.forEach(input => {
                if (input.value.trim() === '') {
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

            if (select.value === '') {
                validateSelect();
                hasError = true;
            }

            if (!document.getElementById('anh').files.length) {
                const errorElement = document.getElementById('anhError');
                errorElement.textContent = 'Vui lòng chọn ảnh sản phẩm.';
                document.getElementById('anh').classList.add('is-invalid');
                errorElement.style.display = 'block';
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>