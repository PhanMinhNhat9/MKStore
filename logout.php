<?php
    session_start();
    session_destroy(); // Destroy the session
    header("Location: GUI&dangnhap.php"); // Redirect to login page
    exit();
?>