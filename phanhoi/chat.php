<?php
require_once '../config.php';
$pdo = connectDatabase();

// Check if user is logged in
if (!isset($_SESSION['user']['iduser'])) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Please log in']);
    exit();
}

// Get recipient ID from URL
$id_nhan = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;
$id_gui = $_SESSION['user']['iduser'];

if ($id_nhan <= 0) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid recipient ID']);
    exit();
}

$sql = "SELECT iduser, hoten FROM user u WHERE iduser=:id_nhan";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_nhan' => $id_nhan]);
$kq = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kq) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode(['error' => 'Recipient not found']);
    exit();
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['noidung'])) {
    $noidung = trim($_POST['noidung']);

    $sql = "INSERT INTO chattructuyen (idgui, idnhan, noidung, trangthai, daxem, thoigian) 
            VALUES (:id_gui, :id_nhan, :noidung, 1, 0, NOW())";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'id_gui' => $id_gui,
            'id_nhan' => $id_nhan,
            'noidung' => $noidung
        ]);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// Update message read status
$update_sql = "UPDATE chattructuyen SET daxem = 1 WHERE idgui = :id_nhan AND idnhan = :id_gui AND daxem = 0";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->execute([
    'id_nhan' => $id_nhan,
    'id_gui' => $id_gui
]);

// Query message list
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Chibi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Emoji Picker -->
    <script src="https://unpkg.com/emoji-picker-element@^1.10.0/index.js" type="module"></script>
    <style>
        body {
            background: url('https://www.transparenttextures.com/patterns/cute-pattern.png') repeat, linear-gradient(to bottom, #e6f0fa, #a3bffa);
            font-family: 'Anime Ace', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .chat-container {
            max-width: 500px;
            width: 95%;
            background: #ffffff;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            height: 90vh;
            margin: 20px auto;
            overflow: hidden;
            position: relative;
        }
        .chat-header {
            background: linear-gradient(135deg, #4e73df, #a3bffa);
            color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 25px 25px 0 0;
            position: relative;
            overflow: hidden;
        }
        .chat-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 100px;
            height: 100px;
            background: url('https://img.icons8.com/?size=100&id=11211&format=png') no-repeat center/cover;
            opacity: 0.3;
        }
        .chat-header h3 {
            font-size: 1.3rem;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            margin: 0;
        }
        .back-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .back-btn:hover {
            transform: scale(1.2) rotate(-10deg);
        }
        .chat-messages {
            flex-grow: 1;
            padding: 25px;
            overflow-y: auto;
            background: #f8fbff;
            border-radius: 0 0 25px 25px;
        }
        .message-date {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.8);
            padding: 5px 10px;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .message {
            max-width: 70%;
            margin-bottom: 20px;
            padding: 15px 20px;
            border-radius: 20px;
            font-size: 1.1rem;
            line-height: 1.4;
            position: relative;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message.sent {
            background: linear-gradient(135deg, #4e73df, #a3bffa);
            color: #fff;
            margin-left: auto;
            border-bottom-right-radius: 5px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
        }
        .message.received {
            background: linear-gradient(135deg, #e6f0fa, #ffffff);
            color: #333;
            margin-right: auto;
            border-bottom-left-radius: 5px;
            box-shadow: -2px 2px 8px rgba(0, 0, 0, 0.1);
        }
        .message-time {
            font-size: 0.8rem;
            opacity: 0.7;
            text-align: right;
            margin-top: 8px;
        }
        .status {
            font-size: 0.8rem;
            opacity: 0.8;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
        }
        .message-form {
            padding: 20px;
            background: #ffffff;
            border-top: 2px solid #e6f0fa;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .message-input {
            flex-grow: 1;
            border-radius: 30px;
            font-size: 1.1rem;
            padding: 15px 20px;
            border: 2px solid #e6f0fa;
            outline: none;
            background: #fff;
            transition: border-color 0.3s, transform 0.2s;
        }
        .message-input:focus {
            border-color: #4e73df;
            transform: scale(1.02);
        }
        .emoji-btn, .send-btn {
            background: none;
            border: none;
            font-size: 2rem;
            color: #4e73df;
            cursor: pointer;
            transition: transform 0.2s, color 0.2s;
        }
        .emoji-btn:hover, .send-btn:hover {
            color: #2e59d9;
            transform: scale(1.2);
        }
        emoji-picker {
            position: absolute;
            bottom: 80px;
            width: 90%;
            max-width: 350px;
            height: 320px;
            display: none;
            z-index: 1000;
            border: 3px solid #4e73df;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        /* Scrollbar customization */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }
        .chat-messages::-webkit-scrollbar-thumb {
            background: #a3bffa;
            border-radius: 4px;
        }
        .chat-messages::-webkit-scrollbar-track {
            background: #f8fbff;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <button class="back-btn" onclick="goBack()">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h3><?= htmlspecialchars($kq['hoten']) ?></h3>
        </div>

        <div class="chat-messages" id="chat-box">
            <?php 
            $last_date = null; 
            foreach ($messages as $message): 
                $current_date = date('Y-m-d', strtotime($message['thoigian']));
                if ($current_date !== $last_date): 
                    $last_date = $current_date;
            ?>
                <div class="message-date" data-date="<?= $current_date ?>"><?= date('d/m/Y', strtotime($message['thoigian'])) ?></div>
            <?php endif; ?>
                <div class="message <?= ($message['idgui'] == $id_gui) ? 'sent' : 'received' ?>" data-message-id="<?= htmlspecialchars($message['thoigian']) ?>">
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

        <form id="message-form" class="message-form">
            <button type="button" class="emoji-btn" onclick="toggleEmojiPicker()">
                <i class="fa-solid fa-face-smile"></i>
            </button>
            <input type="text" name="noidung" class="message-input form-control" placeholder="Gửi lời nhắn dễ thương nè~!" required>
            <button type="submit" class="send-btn">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
            <emoji-picker id="emoji-picker"></emoji-picker>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const chatBox = document.getElementById("chat-box");
        chatBox.scrollTop = chatBox.scrollHeight;

        const form = document.getElementById("message-form");
        const input = form.querySelector(".message-input");
        const emojiPicker = document.getElementById("emoji-picker");
        let lastMessages = new Map(); // Store message IDs and their content
        let isFetching = false; // Prevent concurrent fetches

        emojiPicker.addEventListener("emoji-click", (event) => {
            input.value += event.detail.unicode;
            input.focus();
            emojiPicker.style.display = "none";
        });

        function toggleEmojiPicker() {
            emojiPicker.style.display = emojiPicker.style.display === "block" ? "none" : "block";
        }

        form.addEventListener("submit", function(e) {
            e.preventDefault();
            const noidung = input.value.trim();
            if (noidung === "") return;
            fetch("chat.php?id=<?= base64_encode($id_nhan) ?>", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: "noidung=" + encodeURIComponent(noidung)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        input.value = "";
                        fetchMessages(true); // Force scroll to bottom after sending
                    } else {
                        console.error("Server error:", data.error || "Unknown error");
                    }
                } catch (e) {
                    console.error("Lỗi phân tích JSON:", e, "Response:", text);
                }
            })
            .catch(error => console.error("Lỗi gửi tin nhắn:", error));
        });

        function fetchMessages(scrollToBottom = false) {
            if (isFetching) return; // Skip if already fetching
            isFetching = true;

            // Check if user is at the bottom
            const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 10;

            fetch("fetch_messages.php?id=<?= base64_encode($id_nhan) ?>")
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.error) {
                            console.error("Server error:", data.error);
                            return;
                        }
                        const fragment = document.createDocumentFragment();
                        const newMessages = new Map();
                        const existingDates = new Set(
                            Array.from(chatBox.querySelectorAll('.message-date')).map(div => div.getAttribute('data-date'))
                        );

                        let lastDate = null;
                        data.messages.forEach(message => {
                            const messageId = message.thoigian;
                            newMessages.set(messageId, message);

                            // Skip if message already exists
                            if (lastMessages.has(messageId)) return;

                            const currentDate = message.thoigian.split(" ")[0];
                            if (currentDate !== lastDate && !existingDates.has(currentDate)) {
                                lastDate = currentDate;
                                const dateDiv = document.createElement("div");
                                dateDiv.className = "message-date";
                                dateDiv.setAttribute("data-date", currentDate);
                                dateDiv.textContent = new Date(message.thoigian).toLocaleDateString("vi-VN");
                                fragment.appendChild(dateDiv);
                                existingDates.add(currentDate);
                            }

                            const messageDiv = document.createElement("div");
                            messageDiv.className = `message ${message.idgui == <?= $id_gui ?> ? 'sent' : 'received'}`;
                            messageDiv.setAttribute("data-message-id", messageId);
                            messageDiv.innerHTML = `
                                ${message.noidung}
                                <div class="message-time">${new Date(message.thoigian).toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" })}</div>
                                ${message.idgui == <?= $id_gui ?> ? `<div class="status">${message.daxem == 1 ? 'Đã xem <i class="fa fa-check-double"></i>' : 'Đã gửi <i class="fa fa-check"></i>'}</div>` : ''}
                            `;
                            fragment.appendChild(messageDiv);
                        });

                        // Append new messages
                        chatBox.appendChild(fragment);

                        // Update status for existing messages
                        lastMessages.forEach((_, messageId) => {
                            if (!newMessages.has(messageId)) {
                                const messageDiv = chatBox.querySelector(`[data-message-id="${messageId}"]`);
                                if (messageDiv) messageDiv.remove();
                            }
                        });

                        // Update read status for sent messages
                        newMessages.forEach((message, messageId) => {
                            if (message.idgui == <?= $id_gui ?>) {
                                const messageDiv = chatBox.querySelector(`[data-message-id="${messageId}"] .status`);
                                if (messageDiv) {
                                    const newStatus = message.daxem == 1 ? 'Đã xem <i class="fa fa-check-double"></i>' : 'Đã gửi <i class="fa fa-check"></i>';
                                    if (messageDiv.innerHTML !== newStatus) {
                                        messageDiv.innerHTML = newStatus;
                                    }
                                }
                            }
                        });

                        lastMessages = newMessages;

                        // Scroll to bottom if user was at bottom or after sending a message
                        if (isAtBottom || scrollToBottom) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    } catch (e) {
                        console.error("Lỗi phân tích JSON:", e, "Response:", text);
                    }
                    isFetching = false;
                })
                .catch(error => {
                    console.error("Lỗi fetchMessages:", error);
                    isFetching = false;
                });
        }

        // Initial fetch and set interval
        fetchMessages();
        setInterval(() => fetchMessages(), 2000);

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>