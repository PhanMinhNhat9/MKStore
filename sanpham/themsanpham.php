<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container { max-width: 400px; margin: 30px auto; padding: 15px; background: #f8f9fa; border-radius: 8px; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1); }
        #previewContainer { display: none; text-align: center; }
        #preview { max-width: 70px; max-height: 70px; border-radius: 6px; margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h4 class="text-center mb-3">Thêm Sản Phẩm</h4>
            <form action="../config.php" method="POST" enctype="multipart/form-data">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm" name="tensp" placeholder="Tên sản phẩm" required>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="giaban" placeholder="Giá bán" required>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="soluong" placeholder="Số lượng" required>
                    </div>
                    <div class="col-6">
                        <select class="form-select form-select-sm" name="iddm">
                            <option value="" disabled selected>Chọn danh mục</option>
                            <?php
                                require_once '../config.php';
                                $pdo = connectDatabase();
                                $query = "SELECT iddm, tendm FROM danhmucsp WHERE loaidm != '0'";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['iddm']}'>{$row['tendm']}</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <textarea class="form-control form-control-sm mb-2" name="mota" rows="2" placeholder="Mô tả sản phẩm"></textarea>

                <div class="mb-2">
                    <input type="file" class="form-control form-control-sm" id="anh" name="anh" accept="image/*" onchange="handleImage(event)">
                </div>

                <div id="previewContainer">
                    <img id="preview" src="#" alt="Xem trước ảnh">
                    <button type="button" class="btn btn-danger btn-sm" onclick="handleImage()">Xóa ảnh</button>
                </div>

                <input type="submit" name="themsp" class="btn btn-primary w-100 btn-sm" value="Thêm sản phẩm">
            </form>
        </div>
    </div>

    <script>
        function handleImage(event = null) {
            let fileInput = document.getElementById('anh');
            let preview = document.getElementById('preview');
            let previewContainer = document.getElementById('previewContainer');

            if (event && event.target.files[0]) {
                let reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            } else {
                fileInput.value = "";
                previewContainer.style.display = 'none';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
