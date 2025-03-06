<?php
require_once 'config.php';
header('Content-Type: application/json'); 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        if (xoaTK($id)) {
            echo "Xóa tài khoản thành công!";
        } else {
            echo "Lỗi khi xóa: ";
        }
    }
}
?>
