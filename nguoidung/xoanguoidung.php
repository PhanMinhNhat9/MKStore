<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    
</body>
</html>
<?php
    require_once '../config.php';

    if (isset($_GET['id'])) {
        $id = intval(base64_decode($_GET['id']));
        try {
            if (xoaNguoiDung($id)) {
                echo "
                <script>
                    showCustomAlert('🐳 Xóa Thành Công!', 'Người dùng đã được xóa khỏi danh sách!', '../picture/success.png');
                    setTimeout(function() {
                        goBack();
                    }, 3000); 
                </script>";
            } else {
                throw new Exception('Lỗi khi xóa người dùng!');
            }
        } catch (Exception $e) {
            $errorMsg = addslashes($e->getMessage()); // Chống lỗi JS injection
            echo "
            <script>
                showCustomAlert('🐳 Xóa Không Thành Công!', '$errorMsg', '../picture/error.png');
            </script>";
        }
    }
?>

