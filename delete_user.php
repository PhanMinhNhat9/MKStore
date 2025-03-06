<?php
require_once 'config.php';
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        if (xoaTK($id)) {
            echo "<script> alert('Xóa thành công!');
        window.location.replace('trangchuadmin.html?id=1');</script>";
        } else {
            echo "Lỗi khi xóa: ";
        }
    }

?>
