<?php
    session_start();
    session_unset(); 
    session_destroy(); // Destroy the session
    setcookie(session_name(), '', time() - 3600, '/'); // Xóa cookie session nếu có

    // Chuyển hướng về trang đăng nhập với thông báo timeout
    header("Location: ../auth/dangnhap.php");
    exit();
?>