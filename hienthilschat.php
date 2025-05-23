<?php
include "config.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Lịch sử Chat - M'K STORE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="picture/logoTD.png" type="image/png">
  <style>
    body {
      background: url('https://core.telegram.org/file/464001172/1/tg_background.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Roboto', sans-serif;
    }
    .store-name {
      font-weight: 700;
      color: #0088cc;
      transition: transform 0.3s ease-in-out;
    }
    .store-name:hover {
      transform: scale(1.05);
    }
    .message-container {
      max-height: 70vh;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #0088cc #e6e6e6;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      backdrop-filter: blur(5px);
    }
    .message-container::-webkit-scrollbar {
      width: 6px;
    }
    .message-container::-webkit-scrollbar-track {
      background: #e6e6e6;
      border-radius: 3px;
    }
    .message-container::-webkit-scrollbar-thumb {
      background: #0088cc;
      border-radius: 3px;
    }
    .message {
      max-width: 70%;
      margin: 0.5rem 1rem;
      padding: 0.75rem 1rem;
      border-radius: 10px;
      position: relative;
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
      animation: fadeIn 0.3s ease-in;
    }
    .message.user {
      background: #e1f5c4;
      color: #000;
      margin-left: auto;
      border-bottom-right-radius: 3px;
    }
    .message.bot {
      background: #fff;
      color: #000;
      margin-right: auto;
      border-bottom-left-radius: 3px;
    }
    .message::after {
      content: '';
      position: absolute;
      bottom: 0;
      width: 10px;
      height: 10px;
      background: inherit;
      clip-path: polygon(0 0, 100% 0, 100% 100%);
    }
    .message.user::after {
      right: -6px;
      transform: rotate(45deg);
    }
    .message.bot::after {
      left: -6px;
      transform: rotate(-45deg);
    }
    .message-avatar {
      width: 1.75rem;
      height: 1.75rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 500;
      flex-shrink: 0;
    }
    .message.user .message-avatar {
      background-color: #0088cc;
      color: #fff;
    }
    .message.bot .message-avatar {
      background-color: #8696a0;
      color: #fff;
    }
    .message-content {
      flex: 1;
    }
    .message-content p {
      line-height: 1.4;
      font-size: 0.9rem;
    }
    .timestamp {
      font-size: 0.7rem;
      color: #8696a0;
      margin-top: 0.25rem;
      text-align: right;
    }
    .message.user .timestamp {
      color: #5e8e3e;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .header-shadow {
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
    }
    .btn {
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn:hover {
      transform: translateY(-1px);
    }
  </style>
</head>
<body class="text-gray-900">
  <!-- Header -->
  <header class="header-shadow flex justify-between items-center px-6 py-3">
    <div class="flex items-center space-x-3">
      <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="h-10 md:h-12 transition-transform duration-300 hover:scale-105" />
      <span class="text-xl md:text-2xl font-bold uppercase tracking-tight store-name">M'K STORE</span>
    </div>
    <a href="auth/dangnhap.php" class="bg-[#0088cc] text-white px-4 py-1.5 rounded-md text-sm font-medium btn hover:bg-[#0077b3]">Đăng nhập</a>
  </header>

  <!-- Main Content -->
  <main class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Lịch sử Chat</h1>
      <a href="huongdan.php" class="bg-gray-500 text-white px-4 py-1.5 rounded-md text-sm font-medium btn hover:bg-gray-600">Quay lại</a>
    </div>
    <div class="p-4 rounded-xl message-container">
      <?php
      // Get contactInfo from query parameter
      $contactInfo = isset($_GET['contactInfo']) ? htmlspecialchars($_GET['contactInfo']) : '';

      if (empty($contactInfo)) {
        echo '<p class="text-red-600 font-medium text-center">⚠️ Vui lòng cung cấp thông tin liên hệ.</p>';
      } else {
        try {
          // Establish database connection
          $pdo = connectDatabase();

          // Fetch messages where contactInfo is either ttgui or ttnhan
          $stmt = $pdo->prepare("
            SELECT idtn, ttgui, ttnhan, noidung, thoigian 
            FROM telegram 
            WHERE ttgui = ? OR ttnhan = ? 
            ORDER BY thoigian ASC
          ");
          $stmt->execute([$contactInfo, $contactInfo]);
          $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($messages)) {
            echo '<p class="text-gray-500 text-center font-medium">Không có tin nhắn nào để hiển thị.</p>';
          } else {
            $today = new DateTime('today');
            foreach ($messages as $message) {
              // Determine if the message is from the user or bot
              $isUser = '0'.$message['ttgui'] === $contactInfo;
              $class = $isUser ? 'user' : 'bot';
              $senderLabel = $isUser ? 'Bạn' : 'Bot';
              $messageTime = new DateTime($message['thoigian']);
              $isToday = $messageTime->format('Y-m-d') === $today->format('Y-m-d');
              $timestamp = $isToday ? 'Hôm nay, ' . $messageTime->format('H:i') : $messageTime->format('d/m/Y H:i');
              $content = htmlspecialchars($message['noidung']);

              echo "
                <div class='message $class'>
                  <div class='message-avatar'>".substr($senderLabel, 0, 1)."</div>
                  <div class='message-content'>
                    <p class='font-medium text-sm'>$senderLabel</p>
                    <p>$content</p>
                    <p class='timestamp'>$timestamp</p>
                  </div>
                </div>
              ";
            }
          }
        } catch (PDOException $e) {
          echo '<p class="text-red-600 font-medium text-center">❌ Lỗi khi tải tin nhắn: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
      }
      ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-transparent text-center text-sm py-4 mt-8 text-gray-600">
    <p>Bản quyền thuộc về © 2025 M'K STORE - All rights reserved.</p>
  </footer>
</body>
</html>