<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>HelpDesk - Câu hỏi & Hướng dẫn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
  <link rel="icon" href="picture/logoTD.png" type="image/png">
  <style>
    @import url('https://cdn.tailwindcss.com'); 
    .store-name {
      font-family: 'Poppins', sans-serif;
      animation: colorShift 5s infinite linear, glowEffect 3s infinite ease-in-out;
    }
    @keyframes colorShift {
      0% { color: #007BFF; }
      25% { color: #00A6FF; }
      50% { color: #ffffff; }
      75% { color: #00A6FF; }
      100% { color: #007BFF; }
    }
    @keyframes glowEffect {
      0% { text-shadow: 0 0 5px rgba(0, 123, 255, 0.3); }
      50% { text-shadow: 0 0 15px rgba(0, 166, 255, 0.6); }
      100% { text-shadow: 0 0 5px rgba(0, 123, 255, 0.3); }
    }
  </style>
</head>
<body class="bg-white text-gray-800">

  <!-- Header -->
  <header class="bg-white shadow flex justify-between items-center px-5 py-0">
    <div class="flex items-center cursor-pointer space-x-2">
      <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="h-16 md:h-20 transition-transform duration-300 hover:rotate-6" />
      <span class="text-2xl md:text-3xl font-bold uppercase tracking-wide store-name">M'K STORE</span>
    </div>
    <a href="auth/dangnhap.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Đăng nhập</a>
  </header>

  <!-- Hero -->
  <section class="bg-blue-600 text-white text-center py-10 px-4">
    <h1 class="text-3xl md:text-4xl font-bold">Xin chào! Tôi có thể giúp gì cho bạn?</h1>
  </section>

  <!-- Main Content -->
  <main class="max-w-5xl mx-auto px-4 py-10 text-center">
    <h2 class="text-2xl md:text-3xl font-semibold">Khám phá câu hỏi và hướng dẫn phổ biến</h2>
    <p class="mt-2 text-lg">Bạn đang gặp vấn đề gì? Dưới đây là những câu hỏi thường gặp và hướng dẫn được quan tâm nhiều nhất.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10 text-left">
      <!-- FAQs -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-blue-600 text-lg font-semibold mb-4">Câu hỏi thường gặp? - Hướng dẫn</h3>
        <ul class="list-disc pl-5 space-y-2">
          <li onclick="showGuide(0)" class="cursor-pointer hover:text-blue-600">Làm sao để khôi phục mật khẩu đăng nhập?</li>
          <li onclick="showGuide(1)" class="cursor-pointer hover:text-blue-600">Tôi không nhận được email xác nhận, phải làm gì?</li>
          <li onclick="showGuide(2)" class="cursor-pointer hover:text-blue-600">Làm sao để thay đổi đơn hàng của tôi?</li>
          <li onclick="showGuide(3)" class="cursor-pointer hover:text-blue-600">Sản phẩm tôi nhận bị lỗi, tôi có thể đổi/trả không?</li>
          <li onclick="showGuide(4)" class="cursor-pointer hover:text-blue-600">Làm sao để hủy đơn hàng?</li>
          <li onclick="showGuide(5)" class="cursor-pointer hover:text-blue-600">Thay đổi thông tin cá nhân như thế nào?</li>
          <li onclick="showGuide(6)" class="cursor-pointer hover:text-blue-600">Làm sao chọn và thanh toán phụ kiện trên website</li>
          <li onclick="showGuide(7)" class="cursor-pointer hover:text-blue-600">Đăng ký tài khoản như thế nào?</li>
        </ul>
      </div>

      <!-- Guides -->
      <div class="bg-white p-6 rounded-lg shadow-md" id="guideBox">
        <h3 class="text-blue-600 text-lg font-semibold mb-4">📘 Hướng dẫn sử dụng</h3>
        <div id="guideContent" class="text-gray-700">
          <p>Chọn một câu hỏi bên trái để xem hướng dẫn chi tiết tại đây.</p>
        </div>
      </div>
    </div>
  </main>

  <!-- Support Button -->
  <button id="supportBtn" class="fixed bottom-5 right-5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-full shadow-lg flex items-center space-x-2 z-50">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
      <path d="M18 10c0 4.418-3.582 8-8 8a8 8 0 110-16c4.418 0 8 3.582 8 8zm-8-6a6 6 0 100 12 6 6 0 000-12zm1 9H9v-1h2v1zm0-2H9V7h2v4z"/>
    </svg>
    <span>Hãy phản hồi đến tôi!</span>
  </button>

  <!-- Support Popup -->
  <div id="supportPopup" class="fixed bottom-20 right-5 w-80 bg-white shadow-xl rounded-lg p-4 hidden z-50">
    <div class="flex justify-between items-center mb-2">
      <h4 class="font-semibold text-blue-600">Gửi hỗ trợ đến Telegram</h4>
      <button onclick="supportPopup.classList.add('hidden'); clearInterval(pollingInterval);" class="text-gray-500 hover:text-gray-700">×</button>
    </div>
    <form id="supportForm" class="text-sm">
      <input type="text" id="contactInfo" placeholder="Nhập email hoặc số điện thoại..." required class="w-full p-2 border rounded mb-2">
      <button type="button" id="confirmContact" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 mb-2 w-full">
        ✅ Xác nhận số điện thoại / email
      </button>
      <textarea id="supportMessage" placeholder="Nhập câu hỏi hoặc yêu cầu của bạn..." required class="w-full p-2 border rounded mb-2"></textarea>
      <div class="flex space-x-2">
        <button type="submit" class="flex-1 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Gửi</button>
        <button type="button" id="viewMessagesBtn" class="flex-1 bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Xem lịch sử</button>
      </div>
      <p id="sendStatus" class="mt-2 text-sm font-semibold"></p>
    </form>
    <div id="botReply" class="mt-2 text-sm text-gray-700 max-h-40 overflow-y-auto"></div>
  </div>

  <!-- Footer -->
  <footer class="bg-blue-100 text-center text-sm py-6 mt-16">
    <p>Bản quyền thuộc về © 2025 M'K STORE - All rights reserved.</p>
  </footer>

  <!-- JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const input = document.getElementById('contactInfo');
      const confirmBtn = document.getElementById('confirmContact');
      const viewMessagesBtn = document.getElementById('viewMessagesBtn');

      // Load saved contactInfo
      const savedContact = localStorage.getItem('contactInfo');
      if (savedContact) input.value = savedContact;

      // Confirm button to save contactInfo
      confirmBtn.addEventListener('click', () => {
        const contactValue = input.value.trim();
        if (contactValue === '') {
          alert('❗ Vui lòng nhập email hoặc số điện thoại trước khi xác nhận.');
          return;
        }

        const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactValue);
        const isPhone = /^[0-9+\-().\s]{8,15}$/.test(contactValue);
        if (!isEmail && !isPhone) {
          alert('⚠️ Định dạng không hợp lệ. Vui lòng nhập email hoặc số điện thoại đúng.');
          return;
        }

        localStorage.setItem('contactInfo', contactValue);
        alert('✅ Đã lưu thông tin liên hệ vào trình duyệt!');
      });

      // View messages button - redirect to hienthilschat.php
      viewMessagesBtn.addEventListener('click', () => {
        const contactInfo = document.getElementById('contactInfo').value.trim();
        if (!contactInfo) {
          alert('⚠️ Vui lòng nhập và xác nhận email hoặc số điện thoại trước.');
          return;
        }
        // Redirect to hienthilschat.php with contactInfo as query parameter
        window.location.href = `hienthilschat.php?contactInfo=${encodeURIComponent(contactInfo)}`;
      });
    });

    const supportBtn = document.getElementById('supportBtn');
    const supportPopup = document.getElementById('supportPopup');
    let lastUpdateId = 0;
    let pollingInterval = null;
    let lastMessageId = null;

    const guides = [
      `<p>Để khôi phục mật khẩu, hãy nhấp vào <strong>'Quên mật khẩu'</strong> trên trang đăng nhập.</p><img src="/picture/laypwd.png" alt="Quên mật khẩu" class="mt-3 rounded shadow w-full max-w-sm">`,
      `<p>Vui lòng kiểm tra hộp thư <strong>Spam / Thư rác</strong>. Nếu không thấy email, bạn có thể yêu cầu gửi lại.</p><video controls class="mt-3 w-full max-w-md rounded shadow"><source src="videos/videohd.mp4" type="video/mp4"></video>`,
      `<p>Thay đổi đơn hàng bằng cách vào <strong>Lịch sử đơn hàng</strong> > nhấn <em>"Chỉnh sửa"</em>.</p><img src="picture/thaydoidh.png" alt="Chỉnh sửa đơn hàng" class="mt-3 rounded shadow w-full max-w-sm">`,
      `<p>Liên hệ hỗ trợ trong vòng 3 ngày nếu sản phẩm bị lỗi để đổi/trả miễn phí.</p><img src="picture/doitra.png" alt="Chính sách đổi trả" class="mt-3 rounded shadow w-full max-w-sm">`,
      `<p>Để hủy đơn hàng, vào <em>Lịch sử đơn hàng</em> và chọn <strong>"Hủy đơn"</strong>.</p>`,
      `<p>Bạn có thể chỉnh sửa thông tin tại trang <strong>Tài khoản của tôi</strong>.</p>`,
      `<p>Thêm sản phẩm vào giỏ, chọn <strong>thanh toán</strong>, sau đó chọn phương thức bạn muốn.</p><video controls class="mt-3 w-full max-w-md rounded shadow"><source src="videos/videohd.mp4" type="video/mp4"></video>`,
      `<p>Nhấn vào <strong>Đăng ký</strong>, nhập thông tin và xác nhận email để hoàn tất.</p><img src="picture/dangky.png" alt="Đăng ký tài khoản" class="mt-3 rounded shadow w-full max-w-sm">`
    ];

    function showGuide(index) {
      document.getElementById("guideContent").innerHTML = guides[index];
    }

    supportBtn.addEventListener('click', () => {
      supportPopup.classList.toggle('hidden');
      if (!supportPopup.classList.contains('hidden') && !pollingInterval) {
        pollingInterval = setInterval(fetchBotReply, 5000);
      }
    });

    // Save message to database
    function saveMessageToDB(ttgui, ttnhan, noidung) {
      fetch('save_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ttgui, ttnhan, noidung })
      })
        .then(res => res.text().then(text => {
          console.log('Raw response from save_message.php:', text);
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error('JSON parse error:', e, 'Raw text:', text);
            throw e;
          }
        }))
        .then(data => {
          if (!data.success) {
            console.error('Failed to save message:', data.message);
          }
        })
        .catch(err => console.error('Error saving message:', err));
    }

    // Fetch bot reply
    function fetchBotReply() {
      const botToken = '7786877837:AAGvwHUTou7QoHEjwdV4c9Mi5dQhzR1HcQU';
      const chatId = '7674548260';
      const url = `https://api.telegram.org/bot${botToken}/getUpdates?offset=${lastUpdateId + 1}`;

      fetch(url)
        .then(res => res.json())
        .then(data => {
          console.log("API Response:", data);
          if (data.ok && data.result.length > 0) {
            const relevantMessages = data.result.filter(update =>
              update.message && update.message.chat.id.toString() === chatId
            );
            if (relevantMessages.length > 0) {
              lastUpdateId = Math.max(...relevantMessages.map(update => update.update_id));
              const latestMessage = relevantMessages[relevantMessages.length - 1];
              const messageId = latestMessage.message.message_id;
              const messageText = latestMessage.message.text || 'Không có nội dung phản hồi';
              const contactInfo = document.getElementById('contactInfo').value.trim();
              const botReply = document.getElementById('botReply');

              let repliedText = '';
              if (latestMessage.message.reply_to_message && latestMessage.message.reply_to_message.text) {
                repliedText = latestMessage.message.reply_to_message.text;
                console.log("Bot đang trả lời nội dung:", repliedText);
              }

              if (repliedText.includes(contactInfo)) {
                console.log("✅ contactInfo nằm trong repliedText");
                if (messageId !== lastMessageId) {
                  lastMessageId = messageId;
                  botReply.textContent = `Phản hồi từ bot: ${messageText}`;
                  botReply.scrollTop = botReply.scrollHeight;
                  document.getElementById('sendStatus').textContent = "✅ Đã nhận phản hồi từ bot!";

                  // Save received message to database
                  saveMessageToDB(contactInfo, chatId, `Bot reply: ${messageText}`);
                }
              } else {
                console.log("❌ contactInfo KHÔNG nằm trong repliedText");
                document.getElementById('sendStatus').textContent = "⚠️ Đang chờ phản hồi từ bot...";
              }
            } else {
              document.getElementById('sendStatus').textContent = "⚠️ Đang chờ phản hồi từ bot...";
            }
          } else {
            document.getElementById('sendStatus').textContent = "⚠️ Đang chờ phản hồi từ bot...";
          }
        })
        .catch(err => {
          console.error("Lỗi lấy phản hồi:", err);
          document.getElementById('sendStatus').textContent = "❌ Lỗi khi lấy phản hồi từ bot.";
        });
    }

    // Handle form submission
    document.getElementById('supportForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const contactInfo = document.getElementById('contactInfo').value.trim();
      const message = document.getElementById('supportMessage').value.trim();
      const status = document.getElementById('sendStatus');
      const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactInfo);
      const isPhone = /^[0-9+\-().\s]{8,15}$/.test(contactInfo);
      const chatId = '7674548260';

      if (!contactInfo || (!isEmail && !isPhone)) {
        status.textContent = "⚠️ Vui lòng nhập email hoặc số điện thoại hợp lệ.";
        return;
      }
      if (!message) {
        status.textContent = "⚠️ Vui lòng nhập nội dung hỗ trợ.";
        return;
      }

      const botToken = '7786877837:AAGvwHUTou7QoHEjwdV4c9Mi5dQhzR1HcQU';
      const text = encodeURIComponent(`📩 Yêu cầu hỗ trợ mới:\n📞 Liên hệ: ${contactInfo}\n💬 Nội dung: ${message}`);
      const url = `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${text}`;

      fetch(url)
        .then(res => res.json())
        .then(data => {
          if (data.ok) {
            status.textContent = "✅ Tin nhắn đã được gửi! Đang chờ phản hồi...";
            document.getElementById('botReply').textContent = '';
            lastMessageId = null;

            // Save sent message to database
            saveMessageToDB(contactInfo, chatId, message);

            if (!pollingInterval) {
              pollingInterval = setInterval(fetchBotReply, 2000);
            }
          } else {
            status.textContent = `❌ Lỗi: ${data.description}`;
          }
        })
        .catch(err => {
          console.error("Lỗi gửi:", err);
          status.textContent = "❌ Lỗi hệ thống. Không thể gửi.";
        });
    });
  </script>
</body>
</html>