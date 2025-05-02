<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gửi hỗ trợ Telegram</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 2rem;
      max-width: 500px;
      margin: auto;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background-color: #1d4ed8;
    }
    #sendStatus {
      margin-top: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<h3>Gửi hỗ trợ đến Telegram</h3>
<form id="supportForm">
  <input type="text" id="contactInfo" placeholder="Nhập email hoặc số điện thoại..." required />
  <textarea id="supportMessage" placeholder="Nhập câu hỏi hoặc yêu cầu của bạn..." required></textarea>
  <button type="submit">Gửi</button>
</form>
<p id="sendStatus"></p>

<script>
document.getElementById('supportForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const contactInfo = document.getElementById('contactInfo').value.trim();
  const message = document.getElementById('supportMessage').value.trim();
  const status = document.getElementById('sendStatus');

  // Kiểm tra hợp lệ: email hoặc số điện thoại
  const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactInfo);
  const isPhone = /^[0-9+\-().\s]{8,15}$/.test(contactInfo);
  if (!contactInfo || (!isEmail && !isPhone)) {
    status.textContent = "⚠️ Vui lòng nhập email hoặc số điện thoại hợp lệ.";
    return;
  }

  if (!message) {
    status.textContent = "⚠️ Vui lòng nhập nội dung hỗ trợ.";
    return;
  }

  // === Gửi đến Telegram ===
  const botToken = '7786877837:AAGvwHUTou7QoHEjwdV4c9Mi5dQhzR1HcQU';
  const chatId = '7674548260'; // Thay bằng chat_id đúng của bạn
  const text = encodeURIComponent(
    `📩 Yêu cầu hỗ trợ mới:\n📞 Liên hệ: ${contactInfo}\n💬 Nội dung: ${message}`
  );

  const url = `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${text}`;

  fetch(url)
    .then(res => res.json())
    .then(data => {
      if (data.ok) {
        status.textContent = "✅ Tin nhắn đã được gửi!";
        document.getElementById('supportForm').reset();
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
