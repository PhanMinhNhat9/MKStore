<?php
include '../config.php';
$pdo = connectDatabase();

// Lấy danh sách danh mục và danh mục cha
$sql = "SELECT dm1.iddm, dm1.tendm, dm1.loaidm, dm1.icon, dm1.mota, 
               dm2.tendm AS tencha 
        FROM danhmucsp dm1 
        LEFT JOIN danhmucsp dm2 ON dm1.loaidm = dm2.iddm";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$danhmuc = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tạo danh sách danh mục cha
$danhmucCha = array_filter($danhmuc, fn($dm) => $dm['loaidm'] == 0);
$countCha = count($danhmucCha);
$countCon = count($danhmuc) - $countCha;

// Xử lý POST
$errors = [];
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['capnhatdm'])) {
        // Lấy dữ liệu từ form
        $iddm = intval($_POST['iddm']);
        $tendm = trim($_POST['tendm']);
        $loaidm = isset($_POST['loaidm']) ? intval($_POST['loaidm']) : 0;
        $mota = trim($_POST['mota']);
        $icon_old = $_POST['icon'];
        $icon_new = '';

        // Kiểm tra tendm
        if (empty($tendm)) {
            $errors['tendm'] = "Vui lòng nhập tên danh mục.";
        } elseif (!preg_match('/^[\p{L}0-9\s\-\/\(\)\.]+$/u', $tendm)) {
            $errors['tendm'] = "Tên danh mục chỉ được chứa chữ, số, dấu cách, -, /, (), .";
        } elseif (mb_strlen($tendm, 'UTF-8') > 100) {
            $errors['tendm'] = "Tên danh mục không được vượt quá 100 ký tự.";
        }

        // Kiểm tra loaidm
        if ($loaidm < 0) {
            $errors['loaidm'] = "Loại danh mục phải là số không âm.";
        } elseif ($loaidm > 0) {
            $stmt = $pdo->prepare("SELECT iddm FROM danhmucsp WHERE iddm = :loaidm");
            $stmt->execute(['loaidm' => $loaidm]);
            if (!$stmt->fetch()) {
                $errors['loaidm'] = "Loại danh mục không tồn tại.";
            }
        }

        // Kiểm tra mota
        if (!empty($mota)) {
            if (mb_strlen($mota, 'UTF-8') > 500) {
                $errors['mota'] = "Mô tả không được vượt quá 500 ký tự.";
            }
        }

        // Kiểm tra icon_new
        if (!empty($_FILES['icon_new']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $file_type = $_FILES['icon_new']['type'];
            $file_size = $_FILES['icon_new']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $errors['icon_new'] = "Icon phải là định dạng JPG, PNG hoặc GIF.";
            } elseif ($file_size > $max_size) {
                $errors['icon_new'] = "Icon không được vượt quá 2MB.";
            } else {
                $target_dir = "../icon/";
                $file_extension = pathinfo($_FILES['icon_new']['name'], PATHINFO_EXTENSION);
                $icon_new = $target_dir . uniqid() . '.' . $file_extension;
                if (!move_uploaded_file($_FILES['icon_new']['tmp_name'], $icon_new)) {
                    $errors['icon_new'] = "Lỗi khi upload icon.";
                }
            }
        }

        // Nếu không có lỗi, cập nhật danh mục
        if (empty($errors)) {
            $kqup = capnhatDanhMuc($iddm, $tendm, $loaidm, $mota, $icon_new ?: $icon_old);
            if ($kqup['success']) {
                $success_message = "Cập nhật danh mục thành công!";
            } else {
                $error_message = $kqup['message'];
            }
        } else {
            $error_message = implode(', ', $errors);
        }
    }

    if (isset($_POST['xoadm'])) {
        $iddm = intval($_POST['iddm']);
        $kq = xoaDanhMuc($iddm);
        if ($kq['success']) {
            $success_message = "Xóa danh mục thành công!";
        } else {
            $error_message = $kq['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="capnhatdanhmuc.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-list-ul me-2"></i>Quản lý danh mục</h4>
                <a href="#" onclick="trove()"class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> Trở về</a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-info">Tổng danh mục: <?= count($danhmuc) ?></span>
                    <span class="badge bg-success">Danh mục cha: <?= $countCha ?></span>
                    <span class="badge bg-warning">Danh mục con: <?= $countCon ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên danh mục</th>
                                <th>Danh mục cha</th>
                                <th>Icon hiện tại</th>
                                <th>Icon mới</th>
                                <th>Mô tả</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhmuc as $dm): ?>
                            <tr>
                                <form action="#" method="POST" enctype="multipart/form-data">
                                    <td data-label="ID"><?= htmlspecialchars($dm['iddm']) ?></td>
                                    <td data-label="Tên danh mục">
                                        <input type="text" name="tendm" class="form-control form-control-sm" value="<?= htmlspecialchars($dm['tendm']) ?>" required>
                                    </td>
                                    <td data-label="Danh mục cha">
                                        <?php if ($dm['loaidm'] > 0): ?>
                                            <select name="loaidm" class="form-select form-select-sm">
                                                <option value="">Chọn danh mục cha</option>
                                                <?php foreach ($danhmucCha as $cha): ?>
                                                    <option value="<?= $cha['iddm'] ?>" <?= ($cha['iddm'] == $dm['loaidm']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cha['tendm']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <span class="text-primary"><i class="fas fa-folder me-1"></i>Danh mục cha</span>
                                            <input type="hidden" name="loaidm" value="0">
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Icon hiện tại">
                                        <img class="icon-preview" src="../<?= htmlspecialchars($dm['icon'] ?: 'icon/default.png') ?>" alt="Icon">
                                        <input type="hidden" name="icon" value="<?= htmlspecialchars($dm['icon']) ?>">
                                    </td>
                                    <td data-label="Icon mới">
                                        <input type="file" name="icon_new" accept="image/jpeg,image/png,image/gif" class="form-control form-control-sm" onchange="previewImage(this, 'preview-<?= $dm['iddm'] ?>')">
                                        <img id="preview-<?= $dm['iddm'] ?>" src="../<?= htmlspecialchars($dm['icon'] ?: 'icon/default.png') ?>" class="icon-preview mt-2" alt="Preview">
                                    </td>
                                    <td data-label="Mô tả">
                                        <input type="text" name="mota" class="form-control form-control-sm" value="<?= htmlspecialchars($dm['mota']) ?>">
                                    </td>
                                    <td data-label="Hành động">
                                        <input type="hidden" name="iddm" value="<?= htmlspecialchars($dm['iddm']) ?>">
                                        <button type="submit" name="capnhatdm" class="btn btn-success btn-sm me-1" title="Lưu">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <button type="submit" name="xoadm" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script>
        function trove(){
            window.top.location.href = "../trangchu.php";
        }
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        }

        <?php if ($success_message): ?>
            window.top.location.href = '../trangchu.php?status=cndmT';
        <?php endif; ?>
        <?php if ($error_message || !empty($errors)): ?>
            window.top.location.href = '../trangchu.php?status=cndmF';
        <?php endif; ?>
    </script>
</body>
</html>