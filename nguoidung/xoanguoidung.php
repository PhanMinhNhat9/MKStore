<?php
    require_once '../config.php';
    if (isset($_GET['id'])) {
        $id = intval(base64_decode($_GET['id']));
        if (xoaNguoiDung($id)) {
            echo "<script src='../trangchuadmin.js'></script>";
            echo "<script> 
                    alert('Xóa thành công!');
                    goBack();
                  </script>";
        } else {
            echo "<script>alert('Lỗi khi xóa người dùng!');</script>";
        }
    }
?>
