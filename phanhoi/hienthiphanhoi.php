<?php
require_once '../config.php';
$pdo = connectDatabase();

// ID của admin đang đăng nhập (Giả sử lấy từ session)
$current_user_id = 1; // Thay bằng ID thực tế

// Truy vấn danh sách người dùng đã nhắn tin với admin
$sql = "
    SELECT 
        u.iduser, 
        u.hoten, 
        u.tendn, 
        COUNT(c.idchat) AS so_tin_nhan,
        SUM(CASE WHEN c.daxem = 0 AND c.idnhan = :current_user_id THEN 1 ELSE 0 END) AS chua_doc
    FROM user u
    JOIN chattructuyen c ON (u.iduser = c.idgui OR u.iduser = c.idnhan)
    WHERE u.iduser <> :current_user_id2
    GROUP BY u.iduser, u.hoten, u.tendn
    ORDER BY chua_doc DESC, u.hoten ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':current_user_id' => $current_user_id, ':current_user_id2' => $current_user_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Người Dùng Đã Nhắn Tin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);

        }
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background: #f1f1f1;
            transition: 0.3s;
        }
        .unread {
            color: red;
            font-weight: bold;
            animation: blink 1s infinite alternate;
        }
        @keyframes blink {
            from { opacity: 1; }
            to { opacity: 0.5; }
        }
        .action-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .action-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }
        .icon {
            font-size: 18px;
            margin-right: 5px;
        }
        .new-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="table-container">
        <h2 style="text-align: center;"><i class="fa-solid fa-comments icon"></i> Danh Sách Người Dùng Đã Nhắn Tin</h2>
        <table>
            <thead>
                <tr>
                    <th><i class="fa-solid fa-user"></i> Họ Tên</th>
                    <th><i class="fa-solid fa-user-tag"></i> Tên Đăng Nhập</th>
                    <th><i class="fa-solid fa-envelope"></i> Số Tin Nhắn</th>
                    <th><i class="fa-solid fa-bell"></i> Thông Báo</th>
                    <th><i class="fa-solid fa-comment-dots"></i> Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><i class="fa-solid fa-user icon"></i> <?= htmlspecialchars($user['hoten']) ?></td>
                        <td><i class="fa-solid fa-user-tag icon"></i> <?= htmlspecialchars($user['tendn']) ?></td>
                        <td><i class="fa-solid fa-envelope-open icon"></i> <?= $user['so_tin_nhan'] ?></td>
                        <td>
                            <?php if ($user['chua_doc'] > 0): ?>
                                <span class="unread"><i class="fa-solid fa-bell icon"></i> <?= $user['chua_doc'] ?> tin chưa đọc</span>
                            <?php else: ?>
                                <span class="new-message"><i class="fa-regular fa-circle-check"></i> Không có tin mới</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="phanhoi/chat.php?id=<?= $user['iduser'] ?>" class="action-btn">
                                <i class="fa-solid fa-paper-plane icon"></i> Nhắn Tin
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
