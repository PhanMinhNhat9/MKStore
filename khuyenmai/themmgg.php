<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Mã Giảm Giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../trangchuadmin.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="themmgg.css?v=<?= time(); ?>">
</head>
<body>
    <div class="form-container">
        <h2><i class="fas fa-tag"></i> Thêm Mã Giảm Giá</h2>
        <form action="#" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code"><i class="fas fa-barcode"></i> Mã giảm giá</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Nhập mã giảm giá" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phantram"><i class="fas fa-percent"></i> Phần trăm giảm</label>
                    <input type="number" id="phantram" name="phantram" class="form-control" placeholder="Nhập phần trăm giảm" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ngayhieuluc"><i class="fas fa-calendar-alt"></i> Ngày hiệu lực</label>
                    <input type="date" id="ngayhieuluc" name="ngayhieuluc" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ngayketthuc"><i class="fas fa-calendar-times"></i> Ngày kết thúc</label>
                    <input type="date" id="ngayketthuc" name="ngayketthuc" class="form-control" required>
                </div>
            </div>

            <div class="text-center">
                <input type="submit" name="themmgg" class="btn btn-custom" value="Thêm Mã">
            </div>
            <?php
                require_once '../config.php';
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['themmgg'])) {
                        $kq = themMGG();
                        if ($kq) {
                            echo "
                                <script>
                                    showCustomAlert('🐳 Thêm Thành Công!', 'MGG đã được thêm vào danh sách!', '../picture/success.png');
                                    setTimeout(function() {
                                        goBack();
                                    }, 3000); 
                                </script>";
                        } else {
                            echo "
                                <script>
                                    showCustomAlert('🐳 Thêm Không Thành Công!', '', '../picture/error.png');
                                </script>";
                            }
                        }
                    }
            ?>
        </form>
    </div>
</body>
</html>
