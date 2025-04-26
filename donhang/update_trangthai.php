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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['iddh']) && isset($_POST['trangthai'])) {
    $iddh = $_POST['iddh'];
    $trangthai = $_POST['trangthai'];
    $sql = "UPDATE donhang SET trangthai = :trangthai WHERE iddh = :iddh";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':trangthai', $trangthai, PDO::PARAM_STR);
    $stmt->bindParam(':iddh', $iddh, PDO::PARAM_INT);

    $idyc = $_POST['idyc'];
    $sql = "UPDATE yeucaudonhang SET trangthai = 1 WHERE idyc = :idyc";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idyc', $idyc, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "<script> 
                goBack();
            </script>";
    }
}
?>
