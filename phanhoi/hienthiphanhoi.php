<?php
require_once '../config.php';
$pdo = connectDatabase();

$current_user_id = $_SESSION['user']['iduser']; // Thay bằng ID thực tế
$query = isset($_GET['query']) ? trim($_GET['query']) : ''; 
if ($query != '') {
    $sql = "
    SELECT 
        u.iduser, 
        u.hoten, 
        u.tendn, 
        u.anh, 
        COUNT(c.idchat) AS so_tin_nhan,
        SUM(CASE WHEN c.daxem = 0 AND c.idnhan = :current_user_id THEN 1 ELSE 0 END) AS chua_doc
    FROM user u
    LEFT JOIN chattructuyen c ON (u.iduser = c.idgui OR u.iduser = c.idnhan)
    WHERE u.iduser <> :current_user_id2
    AND (u.hoten LIKE :query_like OR u.tendn LIKE :query_like1)
    GROUP BY u.iduser, u.hoten, u.tendn, u.anh
    ORDER BY chua_doc DESC, u.hoten ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':current_user_id' => $current_user_id,
        ':current_user_id2' => $current_user_id,
        ':query_like' => "%{$query}%",
        'query_like1' => "%{$query}%"
    ]);
} else {
    $sql = "
    SELECT 
        u.iduser, 
        u.hoten, 
        u.tendn, 
        u.anh, 
        COUNT(c.idchat) AS so_tin_nhan,
        SUM(CASE WHEN c.daxem = 0 AND c.idnhan = :current_user_id THEN 1 ELSE 0 END) AS chua_doc
    FROM user u
    LEFT JOIN chattructuyen c ON (u.iduser = c.idgui OR u.iduser = c.idnhan)
    WHERE u.iduser <> :current_user_id2 AND u.quyen <> 0
    GROUP BY u.iduser, u.hoten, u.tendn, u.anh
    ORDER BY chua_doc DESC, u.hoten ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':current_user_id' => $current_user_id, ':current_user_id2' => $current_user_id]);
}
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Người Dùng</title>
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthiphanhoi.css">
</head>
<body>
<div class="product-wrapper">
    <div class="product-container">
        <?php foreach ($users as $user): ?>
            <div class="product-card">
                <div class="avatar-container">
                    <img src="../<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh đại diện">
                </div>
                <h3 class="user-name"> 🧑 <?= htmlspecialchars($user['hoten']) ?></h3>
                <h3 class="user-name"> 📛 <?= htmlspecialchars($user['tendn']) ?></h3>
                <p class="message-count"> ✉️ <?= $user['so_tin_nhan'] ?> tin nhắn </p>
                <p class="<?= $user['chua_doc'] > 0 ? 'unread' : 'message-count' ?>"> 🔔
                    <?= $user['chua_doc'] > 0 ? $user['chua_doc'] . ' tin chưa đọc' : 'Không có tin mới' ?>
                </p>
                <div class="btn-group">
                    <a href="#" onclick="chat(<?= $user['iduser'] ?>)" class="btn btn-chat">
                        <i class="fa-solid fa-comment"></i> Nhắn Tin
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
