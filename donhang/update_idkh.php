<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../trangchuadmin.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<?php
require_once '../config.php';
$pdo = connectDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iddh = $_POST['iddh'] ?? '';
    $idkh = $_POST['idkh'] ?? '';

    if ($iddh && $idkh) {
        $sql = "UPDATE donhang SET idkh = :idkh WHERE iddh = :iddh";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['idkh' => $idkh, 'iddh' => $iddh])) {
            echo "<script> 
                    goBack();
                </script>";
        }
    }
}
?>
