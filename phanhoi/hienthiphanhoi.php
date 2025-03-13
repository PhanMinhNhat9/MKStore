<?php
require_once '../config.php';
$pdo = connectDatabase();

$current_user_id = 1; // Thay bằng ID thực tế

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
    <title></title>
    <style>
        .product-wrapper {
            height: 375px; /* Tăng chiều cao để tránh tràn nội dung */
            overflow-y: auto;
            padding: 10px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            
        }

        .product-wrapper::-webkit-scrollbar {
    display: none; /* Ẩn thanh cuộn trên Chrome, Edge, Safari */
}

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .product-card {
            width: 180px;
            background: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            text-align: center;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            min-height: 230px; /* Đảm bảo thẻ đồng đều */
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card i {
            font-size: 35px;
            color: #007bff;
            margin-bottom: 5px;
        }

        .product-card p {
            margin: 3px 0;
            font-size: 14px;
        }

        .product-card p:first-of-type {
            min-height: 40px; /* Đảm bảo chiều cao đồng đều cho tên */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: bold;
        }

        .message-count {
            font-size: 12px;
            font-weight: bold;
            color: gray;
        }

        .unread {
            color: red;
            font-weight: bold;
        }

        .btn-group {
            margin-top: 8px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .btn {
            border: none;
            padding: 6px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 12px;
            text-decoration: none;
            color: white;
        }

        .btn-chat {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
    display: inline-block;
}

.btn-chat:hover {
    background: linear-gradient(45deg, #0056b3, #003d80);
    transform: scale(1.1);
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.5);
    color: white;
}

    </style>
</head>
<body>

<div class="product-wrapper">
    <div class="product-container">
        <?php foreach ($users as $user): ?>
            <div class="product-card">
                <i class="fa-solid fa-user"></i>
                <p><?= htmlspecialchars($user['hoten']) ?></p>
                <p class="message-count"><i class="fa-solid fa-envelope"></i> <?= $user['so_tin_nhan'] ?> tin nhắn</p>
                <p class="<?= $user['chua_doc'] > 0 ? 'unread' : 'message-count' ?>">
                    <?= $user['chua_doc'] > 0 ? $user['chua_doc'] . ' tin chưa đọc' : 'Không có tin mới' ?>
                </p>
                <div class="btn-group">
                    <a href="phanhoi/chat.php?id=<?= $user['iduser'] ?>" class="btn btn-chat">Nhắn Tin</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
