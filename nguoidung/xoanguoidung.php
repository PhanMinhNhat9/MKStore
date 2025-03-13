<?php
    require_once '../config.php';
    if (isset($_GET['id'])) {
        $id = intval(base64_decode($_GET['id']));
        if (xoaNguoiDung($id)) {
            echo "<script> 
                    alert('Xóa thành công!');
                    window.location.href = '../trangchuadmin.php';
                  </script>";
        } else {
            echo "Lỗi khi xóa: ";
        }
    }
?>
