<?php 
require_once '../config.php';
$pdo = connectDatabase();

// --- XỬ LÝ LỌC ---
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$quyen = isset($_GET['quyen']) ? trim($_GET['quyen']) : '';

// --- PHÂN TRANG ---
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$params = [];
$conditions = [];

if ($query != '') {
    $conditions[] = "(email LIKE :searchTerm OR sdt LIKE :searchTerm1)";
    $params['searchTerm'] = "%{$query}%";
    $params['searchTerm1'] = "%{$query}%";
}
if ($quyen != '') {
    $conditions[] = "quyen = :quyen";
    $params['quyen'] = $quyen;
}

$sql = "SELECT iduser, hoten, tendn, anh, email, sdt, diachi, quyen, thoigian FROM user";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- ĐẾM TỔNG USER ---
$countSql = "SELECT COUNT(*) FROM user";
if (!empty($conditions)) {
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}
$countStmt = $pdo->prepare($countSql);
foreach ($params as $key => $val) {
    if ($key !== 'limit' && $key !== 'offset') {
        $countStmt->bindValue(":$key", $val);
    }
}
$countStmt->execute();
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Người Dùng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="hienthinguoidung.css?v=<?= time(); ?>">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    
</head>
<body>

<div class="sidebar">
    <h3>Lọc người dùng</h3>
    <form method="GET">
        <select name="quyen">
            <option value="" style="text-align: center">-- Tất cả quyền --</option>
            <option value="0" <?= $quyen === "0" ? "selected" : "" ?>>Admin</option>
            <option value="1" <?= $quyen === "1" ? "selected" : "" ?>>User</option>
        </select>
        <button type="submit" class="btn-update"><i class="fas fa-filter"></i> Lọc</button>
    </form>
    <center><button class="floating-btn" onclick="themnguoidung()"><i class="fas fa-plus"></i> Thêm người dùng</button></center>
</div>

<div class="container">
    <div class="user-grid">
        <?php foreach ($users as $user): ?>
            <div class="card">
                <img src="../<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh người dùng"
                     onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)">
                <h3>🧑 <?= htmlspecialchars($user['hoten']) ?></h3>
                <p>📧 <?= htmlspecialchars($user['tendn']) ?></p>
                <p>✉️ <?= htmlspecialchars($user['email']) ?></p>
                <p>📞 <?= htmlspecialchars($user['sdt']) ?></p>
                <p>📍 <?= htmlspecialchars($user['diachi']) ?></p>
                <p>🔒 Quyền: <?= htmlspecialchars($user['quyen']) ?></p>
                <div class="btn-group">
                    <button onclick="capnhatnguoidung(<?= $user['iduser'] ?>)" class="btn btn-update"><i class="fas fa-edit"></i></button>
                    <button onclick="xoanguoidung(<?= $user['iduser'] ?>)" class="btn btn-delete"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?query=<?= urlencode($query) ?>&quyen=<?= $quyen ?>&page=<?= $page - 1 ?>" class="btn-update">← Trước</a>
            <?php endif; ?>
            <span>Trang <?= $page ?> / <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?query=<?= urlencode($query) ?>&quyen=<?= $quyen ?>&page=<?= $page + 1 ?>" class="btn-update">Sau →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal cập nhật ảnh -->
<form action="#" method="POST" enctype="multipart/form-data">
    <div id="imageModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="Ảnh người dùng"><br><br>
            <input type="hidden" name="iduser" id="iduser" value="">
            <input type="file" id="fileInput" name="fileInput" accept="image/*" onchange="loadanh()"><br><br>
            <input type="submit" value="Cập nhật ảnh" name="capnhatanhuser" class="btn btn-update">
        </div>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['capnhatanhuser'])) {
            $kq = capnhatAnhUser();
            if ($kq) {
                echo "<script>showCustomAlert('🐳 Đổi Avt Thành Công!', 'Ảnh người dùng đã được đổi!', '../picture/success.png'); setTimeout(goBack, 3000);</script>";
            } else {
                echo "<script>showCustomAlert('🐳 Cài Đặt Avt Thất Bại!', '$kq', '../picture/error.png');</script>";
            }
        }
    }
    ?>
</form>

<script>
    function themnguoidung() {
        window.location.href = "themnguoidung.php";
    }
</script>
</body>
</html>
