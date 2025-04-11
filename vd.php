<?php 
require_once 'config.php';
$pdo = connectDatabase();

// --- XỬ LÝ LỌC ---
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$quyen = isset($_GET['quyen']) ? trim($_GET['quyen']) : '';

// --- PHÂN TRANG ---
$limit = 4;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$params = [];
$conditions = [];

if ($query != '') {
    $conditions[] = "(email LIKE :searchTerm OR sdt LIKE :searchTerm1)";
    $params['searchTerm'] = "%{$query}%";
    $params['searchTerm1'] = "%{$query}%";
}
if ($quyen !== '') {
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
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="sweetalert2/sweetalert2.min.css">
    <script src="./trangchuadmin.js"></script>
    <!-- <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css"> -->
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            padding: 20px;
            background-color: #f0f0f0;
            height: 100vh;
        }
        .sidebar h3 {
            margin-top: 0;
        }
        .sidebar form {
            display: flex;
            flex-direction: column;
        }
        .sidebar input, .sidebar select, .sidebar button {
            margin-bottom: 10px;
            padding: 8px;
        }
        .container {
            flex: 1;
            padding: 20px;
        }
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            background-color: #fff;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            height: 100%;
        }
        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .card h3 {
            margin: 5px 0;
            font-size: 16px;
        }
        .card p {
            margin: 4px 0;
            font-size: 14px;
        }
        .btn-group {
            margin-top: 10px;
        }
        .btn {
            padding: 6px 10px;
            margin: 0 3px;
            cursor: pointer;
        }
        .btn-update {
            background-color: #4CAF50; color: white;
            border: none; border-radius: 5px;
        }
        .btn-delete {
            background-color: #f44336; color: white;
            border: none; border-radius: 5px;
        }
        .floating-btn {
            margin-top: 10px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            margin: 0 5px;
            text-decoration: none;
        }
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
        .modal-content img {
            max-width: 200px;
            border-radius: 10px;
        }
        .modal img {
            width: 200px; /* Giảm kích thước ảnh */
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .close-btn {
            position: absolute;
            top: 20px; right: 30px;
            font-size: 30px;
            color: #333;
            cursor: pointer;
        }
        .close-btn {
    position: absolute;
    top: 5px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
    color: red;
}
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Lọc người dùng</h3>
    <form method="GET">
        <select name="quyen">
            <option value="">-- Tất cả quyền --</option>
            <option value="0" <?= $quyen === "0" ? "selected" : "" ?>>Admin</option>
            <option value="1" <?= $quyen === "1" ? "selected" : "" ?>>User</option>
        </select>
        <button type="submit" class="btn-update">Lọc</button>
    </form>
    <button class="floating-btn" onclick="themnguoidung()"><i class="fas fa-plus"></i> Thêm người dùng</button>
</div>

<div class="container">
    <div class="user-grid">
        <?php foreach ($users as $user): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh người dùng"
                     onclick="openModal('<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)">
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
