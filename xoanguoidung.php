<?php
    require_once 'config.php';
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        if (xoaNguoiDung($id)) {
            echo "<script> 
                    alert('Thêm thành công!');
                    window.location.href = 'trangchuadmin.html';
                  </script>";
        } else {
            echo "Lỗi khi xóa: ";
        }
    }
?>
