<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['quyen'] != '0') {
    header("Location: GUI&dangnhap.php");
    exit();
}

$pdo = connectDatabase();
$stmt = $pdo->query("SELECT iduser, hoten FROM user WHERE quyen != '0'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Chat</title>
    <link rel="stylesheet" href="trangchucss.css">
    <style>
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .message strong {
            color: #007BFF;
        }

        .message small {
            color: #888;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="user-list">
        <h2>Người dùng</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li><a href="admin_chat.php?user_id=<?= $user['iduser'] ?>"><?= htmlspecialchars($user['hoten']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="chat-container">
        <h2>Trò chuyện</h2>
        <div class="chat-messages" id="chatMessages">
            <!-- Tin nhắn sẽ được tải ở đây -->
        </div>
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Nhập tin nhắn...">
            <button onclick="sendMessage()">Gửi</button>
        </div>
    </div>

    <script>
        const userId = <?= isset($_GET['user_id']) ? json_encode((int)$_GET['user_id']) : 'null' ?>;

        function loadMessages() {
            if (userId) {
                fetch('load_messages.php?user_id=' + userId)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Lỗi mạng: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Phản hồi từ API:', data); // Ghi log phản hồi từ API
                        if (data.status === 'error') {
                            console.error('Lỗi từ máy chủ:', data.message);
                            return;
                        }

                        const chatMessages = document.getElementById('chatMessages');
                        chatMessages.innerHTML = '';
                        data.data.forEach(message => {
                            const messageElement = document.createElement('div');
                            messageElement.classList.add('message');
                            messageElement.innerHTML = `
                                <strong>${message.sender_name}:</strong> ${message.message}
                                <br><small>${new Date(message.timestamp).toLocaleString()}</small>
                            `;
                            chatMessages.appendChild(messageElement);
                        });
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải tin nhắn:', error);
                    });
            }
        }

        function sendMessage() {
            const message = document.getElementById('chatInput').value;
            fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'message=' + encodeURIComponent(message) + '&receiver_id=' + userId
                })
                .then(response => response.text())
                .then(() => {
                    document.getElementById('chatInput').value = '';
                    loadMessages();
                });
        }

        setInterval(loadMessages, 3000); // Tải lại tin nhắn mỗi 3 giây
    </script>
</body>

</html>