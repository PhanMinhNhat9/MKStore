<?php
    require_once '../config.php';
    if (isset($_GET['id'])) {
        $loaidm = intval(base64_decode($_GET['id']));
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
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="themdmcon.css">
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-plus-circle"></i> Thêm danh mục con</h2>
    <form action="../config.php" method="POST" enctype="multipart/form-data">
        
        <!-- Dòng 1: Tên danh mục + Loại danh mục -->
        <div class="row">
            <div class="col-md-6 mb-2">
                <label for="tendm"><i class="fas fa-tag"></i> Tên danh mục</label>
                <input type="text" id="tendm" name="tendm" class="form-control" placeholder="Nhập tên danh mục" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="loaidm"><i class="fas fa-list"></i> Loại danh mục</label>
                <input type="text" id="loaidm" name="loaidm" class="form-control" required value="<?= htmlspecialchars($loaidm ?? '') ?>">
            </div>
        </div>

        <!-- Dòng 2: Chọn Icon + Mô tả -->
        <div class="row">
            <div class="col-md-6 mb-2">
                <label for="icon"><i class="fas fa-image"></i> Chọn Icon</label>
                <input type="file" id="icon" name="icon" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
                <label for="mota"><i class="fas fa-edit"></i> Mô tả</label>
                <textarea id="mota" name="mota" class="form-control" rows="2" placeholder="Nhập mô tả"></textarea>
            </div>
        </div>

        <!-- Nút Thêm -->
        <button type="submit" name="themdmcon" class="btn-custom">
            <i class="fas fa-plus-circle"></i> Thêm danh mục
        </button>
    </form>
</div>

</body>
</html>
