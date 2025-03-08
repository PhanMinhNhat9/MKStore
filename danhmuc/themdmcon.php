<?php
require_once '../config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $loaidm = $_POST['id'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2><i class="fas fa-plus-circle"></i> Thêm danh mục con</h2>
        <form action="../config.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Cột trái -->
                <div class="col-md-6">
                    <div class="mb-2">
                        <label for="tendm" class="form-label"><i class="fas fa-tag"></i> Tên danh mục</label>
                        <input type="text" id="tendm" name="tendm" class="form-control" placeholder="Nhập tên danh mục" required>
                    </div>

                    <div class="mb-2">
                        <label for="loaidm" class="form-label"><i class="fas fa-list"></i> Loại danh mục</label>
                        <input type="text" id="loaidm" name="loaidm" class="form-control" required value="<?php echo htmlspecialchars($loaidm ?? ''); ?>">
                    </div>
                </div>

                <!-- Cột phải -->
                <div class="col-md-6">
                    <div class="mb-2">
                        <label for="icon" class="form-label"><i class="fas fa-image"></i> Icon</label>
                        <input type="file" id="icon" name="icon" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label for="mota" class="form-label"><i class="fas fa-edit"></i> Mô tả</label>
                        <textarea id="mota" name="mota" class="form-control" rows="2" placeholder="Nhập mô tả"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" name="themdmcon" class="btn btn-custom">
                <i class="fas fa-plus-circle"></i> Thêm
            </button>
        </form>
    </div>
</div>


</body>
</html>
