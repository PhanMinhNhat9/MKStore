<?php
require_once '../config.php';
$pdo = connectDatabase();

// Lấy ID người nhận từ URL
$id_nhan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_gui = 1; // Giả sử admin có ID 1

if ($id_nhan <= 0) {
    die("ID người nhận không hợp lệ!");
}

// Nếu có dữ liệu gửi từ form, thực hiện lưu tin nhắn vào database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['noidung'])) {
    $noidung = trim($_POST['noidung']);

    $sql = "INSERT INTO chattructuyen (idgui, idnhan, noidung, trangthai, thoigian) 
            VALUES (:id_gui, :id_nhan, :noidung, 0, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id_gui' => $id_gui,
        'id_nhan' => $id_nhan,
        'noidung' => $noidung
    ]);

    // Chuyển hướng để tránh gửi lại form khi reload trang
    header("Location: chat.php?id=$id_nhan");
    exit();
}

// Truy vấn danh sách tin nhắn
$sql = "SELECT * FROM chattructuyen 
        WHERE (idgui = :id_gui AND idnhan = :id_nhan) 
           OR (idgui = :id_nhan_2 AND idnhan = :id_gui_2) 
        ORDER BY thoigian ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_gui' => $id_gui, 
    ':id_nhan' => $id_nhan,
    ':id_nhan_2' => $id_nhan, 
    ':id_gui_2' => $id_gui
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat Trực Tuyến</title>
    <style>
        .chat-container {
            width: 500px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            padding: 5px;
            margin: 5px 0;
        }
        .sent {
            text-align: right;
            color: blue;
        }
        .received {
            text-align: left;
            color: green;
        }
        .message-form {
            display: flex;
            margin-top: 10px;
        }
        .message-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .send-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <h3>Chat với ID <?= $id_nhan ?></h3>
        <div>
            <?php foreach ($messages as $message): ?>
                <div class="message <?= ($message['idgui'] == $id_gui) ? 'sent' : 'received' ?>">
                    <?= htmlspecialchars($message['noidung']) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form gửi tin nhắn -->
        <form action="chat.php?id=<?= $id_nhan ?>" method="POST" class="message-form">
            <input type="text" name="noidung" class="message-input" placeholder="Nhập tin nhắn..." required>
            <button type="submit" class="send-btn">Gửi</button>
        </form>
    </div>
</body>
</html>

