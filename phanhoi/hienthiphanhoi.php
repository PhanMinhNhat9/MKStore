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
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthiphanhoi.css?v=<?= time(); ?>">
    <script src="../trangchuadmin.js" defer></script>
</head>
<body>
    <div class="table-container">
        <table class="user-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Ảnh đại diện</th>
                    <th style="width: 20%;">Họ tên</th>
                    <th style="width: 20%;">Tên đăng nhập</th>
                    <th style="width: 15%;">Số tin nhắn</th>
                    <th style="width: 20%;">Trạng thái tin nhắn</th>
                    <th style="width: 15%;">Hành động</th>
                </tr>
            </thead>
        </table>
        <div class="scroll-container">
            <table class="user-table">
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Ảnh đại diện">
                               <center> <img src="../<?= htmlspecialchars($user['anh']) ?>" 
                                    alt="Ảnh đại diện" 
                                    width="40" 
                                    height="40"></center>
                            </td>
                            <td data-label="Họ tên">
                                <i class="fas fa-user"></i> 
                                <?= htmlspecialchars($user['hoten']) ?>
                            </td>
                            <td data-label="Tên đăng nhập">
                                <i class="fas fa-id-badge"></i> 
                                <?= htmlspecialchars($user['tendn']) ?>
                            </td>
                            <td data-label="Số tin nhắn">
                                <i class="fas fa-envelope"></i> 
                                <?= $user['so_tin_nhan'] ?> tin nhắn
                            </td>
                            <td data-label="Trạng thái" class="<?= $user['chua_doc'] > 0 ? 'unread' : 'message-count' ?>">
                                <i class="fas fa-bell"></i> 
                                <?= $user['chua_doc'] > 0 ? $user['chua_doc'] . ' tin chưa đọc' : 'Không có tin mới' ?>
                            </td>
                            <td data-label="Hành động">
                                <a href="#" 
                                   onclick="chat(<?= $user['iduser'] ?>)" 
                                   class="btn btn-chat">
                                    <i class="fas fa-comment"></i> Nhắn Tin
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>