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
    <link rel="stylesheet" href="hienthiphanhoi.css?v=<?= time(); ?>">
</head>
<body>
    <div class="product-scroll-container"> <!-- Thêm div này để cuộn -->
        <table class="user-table" border="1" cellspacing="0" cellpadding="10">
            <thead>
                <tr>
                    <th>Ảnh đại diện</th>
                    <th>Họ tên</th>
                    <th>Tên đăng nhập</th>
                    <th>Số tin nhắn</th>
                    <th>Trạng thái tin nhắn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <img src="../<?= htmlspecialchars($user['anh']) ?>" alt="Ảnh đại diện" width="60" height="60" style="border-radius: 50%;">
                    </td>
                    <td>🧑 <?= htmlspecialchars($user['hoten']) ?></td>
                    <td>📛 <?= htmlspecialchars($user['tendn']) ?></td>
                    <td>✉️ <?= $user['so_tin_nhan'] ?> tin nhắn</td>
                    <td class="<?= $user['chua_doc'] > 0 ? 'unread' : 'message-count' ?>">
                        🔔 <?= $user['chua_doc'] > 0 ? $user['chua_doc'] . ' tin chưa đọc' : 'Không có tin mới' ?>
                    </td>
                    <td>
                        <a href="#" onclick="chat(<?= $user['iduser'] ?>)" class="btn btn-chat">
                            <i class="fa-solid fa-comment"></i> Nhắn Tin
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
