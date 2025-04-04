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
    <link rel="stylesheet" href="hienthinguoidung.css?v=<?= time(); ?>">
    <script src="../trangchuadmin.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
</head>
<body>
<div class="container">
    <table class="user-table">
        <tr>
            <?php foreach ($users as $index => $user): ?>
                <td>
                    <div class="card">
                        <img src="../<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh người dùng"
                             onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)">
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
                </td>

                <!-- Sau mỗi 3 card, xuống dòng mới -->
                <?php if (($index + 1) % 5 == 0): ?>
                    </tr><tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    </table>
</div>


    <!-- Modal hiển thị ảnh -->
     <form action="#" method="POST" enctype="multipart/form-data">
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
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['capnhatanhuser'])) {
                    $kq = capnhatAnhUser();
                    if ($kq) {
                        echo "
                        <script>
                            showCustomAlert('🐳 Đổi Avt Thành Công!', 'Ảnh người dùng đã được đổi!', '../picture/success.png');
                            setTimeout(function() {
                                goBack();
                            }, 3000); 
                        </script>";
                    } else {
                        echo "
                        <script>
                            showCustomAlert('🐳 Cài Đặt Avt Thất Bại!', '$kq', '../picture/error.png');
                        </script>";
                    }
                }
            }
        ?>
     </form>
     <button class="floating-btn" onclick="themnguoidung()">
        <i class="fas fa-plus"></i>
    </button>
</body>
</html>
