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

    $sql = "INSERT INTO chattructuyen (idgui, idnhan, noidung, trangthai, daxem, thoigian) 
            VALUES (:id_gui, :id_nhan, :noidung, 1, 0, NOW())"; // Mặc định tin chưa đọc (daxem = 0)

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

// // Cập nhật trạng thái tin nhắn chưa đọc thành đã xem
$update_sql = "UPDATE chattructuyen SET daxem = 1 WHERE idgui = :id_nhan AND idnhan = :id_gui AND daxem = 0";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->execute([
    'id_nhan' => $id_nhan,
    'id_gui' => $id_gui
]);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="chat.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <!-- Nút trở về -->
            <button class="back-btn" onclick="goBack()" onmouseover="changeIcon(this)" onmouseout="resetIcon(this)">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            📨 Chat với: <?= $id_nhan ?>
        </div>

        <div class="chat-messages" id="chat-box">
            <?php foreach ($messages as $message): ?>
                <div class="message <?= ($message['idgui'] == $id_gui) ? 'sent' : 'received' ?>">
                    <?= htmlspecialchars($message['noidung']) ?>
                    <div class="message-time"><?= date('H:i', strtotime($message['thoigian'])) ?></div>
                    <?php if ($message['idgui'] == $id_gui): ?>
                        <div class="status">
                            <?= ($message['daxem'] == 1) ? 'Đã xem <i class="fa fa-check-double"></i>' : 'Đã gửi <i class="fa fa-check"></i>' ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form gửi tin nhắn -->
        <form action="chat.php?id=<?= $id_nhan ?>" method="POST" class="message-form">
            <input type="text" name="noidung" class="message-input" placeholder="Nhập tin nhắn..." required>
            <button type="submit" class="send-btn"><i class="fa fa-paper-plane"></i></button>
        </form>
    </div>

    <script>
        // Cuộn xuống tin nhắn mới nhất khi tải trang
        var chatBox = document.getElementById("chat-box");
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>

