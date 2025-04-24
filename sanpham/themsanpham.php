<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="themsanpham.css?v=<?php time();?>">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h4 class="text-center mb-3">Thêm Sản Phẩm</h4>
            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="giaban" placeholder="Giá bán" required>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="soluong" placeholder="Số lượng" required>
                    </div>
                    <div class="col-6">
                        <select class="form-select form-select-sm" name="iddm" required>
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

                <textarea class="form-control form-control-sm mb-2" name="tensp" rows="2" placeholder="Tên sản phẩm" required></textarea>

                <div class="mb-2">
                    <input type="file" class="form-control form-control-sm" id="anh" name="anh" accept="image/*" onchange="handleImage(event)" required>
                </div>

                <div id="previewContainer">
                    <img id="preview" src="#" alt="Xem trước ảnh">
                    <button type="button" class="btn btn-danger btn-sm" onclick="handleImage()">Xóa ảnh</button>
                </div>

                <input type="submit" name="themsp" class="btn btn-primary w-100 btn-sm" value="Thêm sản phẩm">
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST['themsp'])) {
                            $kq = themSanPham();
                            if ($kq) {
                                echo "
                                <script>
                                    window.top.location.href = '../trangchuadmin.php?status=themspT';
                                </script>";                                
                            } else {
                                echo "
                                <script>
                                    window.top.location.href = '../trangchuadmin.php?status=themspT';
                                </script>";                                
                            }
                        }
                    }
                ?>
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
