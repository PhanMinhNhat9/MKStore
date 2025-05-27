<?php
    include "../config.php";
    $pdo = connectDatabase();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['iduser'];
        try {
            // Update trangthaidb to set trangthai = 0
            $stmt = $pdo->prepare("UPDATE trangthaidb SET trangthai = 0 WHERE iduser = :iduser");
            $stmt->execute(['iduser' => $user_id]);
        } catch (PDOException $e) {
            error_log("Logout error: " . $e->getMessage());
        }
    }

    // Clear session data
    session_unset();
    session_destroy();

    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Redirect to huongdan.php
    header("Location: ../huongdan.php");
    exit();
?>