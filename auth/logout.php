<?php
session_start();
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng xuất</title>
    <script>
        window.location.href = '../huongdan.php';
    </script>
</head>
<body>
</body>
</html>
<?php
exit();
?>