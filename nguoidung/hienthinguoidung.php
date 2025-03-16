<?php
require_once '../config.php';
$pdo = connectDatabase();
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

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
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthinguoidung.css">
    <script src="../trangchuadmin.js"></script>
    <style>

        /* Modal hiển thị ảnh */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    position: relative;
    width: 320px; /* Giảm chiều rộng modal */
    max-width: 90%;
}
.modal img {
    width: 200px; /* Giảm kích thước ảnh */
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}
.close-btn {
    position: absolute;
    top: 5px;
    right: 10px;
    font-size: 18px;
    cursor: pointer;
    color: red;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="user-list">
            <?php foreach ($users as $user): ?>
                <div class="card">
                    <img src="../<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh người dùng" onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)">
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

    <!-- Modal hiển thị ảnh -->
     <form action="config.php" method="POST" enctype="multipart/form-data">
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Ảnh người dùng">
                <br>
                <input type="hidden" name="iduser" id="iduser" value="">
                <input type="file" id="fileInput" name="fileInput" accept="image/*" onchange="loadanh()">
                <br><br>
                <input type="submit" value="Cập nhật ảnh" id="capnhatanhuser" name="capnhatanhuser" class="btn btn-update">
            </div>
        </div>
     </form>
    

</body>
</html>
