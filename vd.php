<?php
    require_once '../config.php';
    $pdo = connectDatabase();
    $sql = "SELECT `idmgg`, `code`, `phantram`, `ngayhieuluc`, `ngayketthuc`, 
    `giaapdung`, `iddm`, `soluong`, `thoigian` 
    FROM `magiamgia`";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $mgg = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
?>