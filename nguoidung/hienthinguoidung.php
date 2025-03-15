<?php
require_once '../config.php';
$pdo = connectDatabase();
$query = isset($_POST['query']) ? trim($_POST['query']) : '';

if ($query != '') {
    $sql = "SELECT iduser, hoten, tendn, anh, email, sdt, diachi, quyen, thoigian 
            FROM user
            WHERE email LIKE :searchTerm OR sdt LIKE :searchTerm1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['searchTerm' => "%{$query}%", 'searchTerm1' => "%{$query}%"]);
} else {
    $sql = "SELECT iduser, hoten, tendn, anh, email, sdt, diachi, quyen, thoigian FROM user";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Người Dùng</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="user-list">
            <?php foreach ($users as $user): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh người dùng">
                    <span> 📧 <?= htmlspecialchars($user['tendn']) ?></span>
                    <h3> 🧑 <?= htmlspecialchars($user['hoten']) ?></h3>
                    <p class="info">✉️ <?= htmlspecialchars($user['email']) ?></p>
                    <p class="info"> 📍 <?= htmlspecialchars($user['diachi']) ?></p>
                    <p class="info">📞 <?= htmlspecialchars($user['sdt']) ?></p>
                    <p class="role"> 🔒 Quyền: <?= htmlspecialchars($user['quyen']) ?></p>

                    <div class="btn-group">
                        <button onclick="capnhatnguoidung(<?= $user['iduser'] ?>)" class="btn btn-update">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="xoanguoidung(<?= $user['iduser'] ?>)" class="btn btn-delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
