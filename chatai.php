<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat AI - OpenRouter</title>
  <link rel="stylesheet" href="tailwind/dist/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Hiệu ứng chuyển màu cho nền tin nhắn bot */
    .bot-msg {
      animation: fadeIn 0.5s ease-in-out;
      background: linear-gradient(90deg, #e0f7fa, #f1f8e9);
    }
    .user-msg {
      animation: fadeIn 0.5s ease-in-out;
      background: linear-gradient(90deg, #e3f2fd, #e8eaf6);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
      /* Scrollbar tùy chỉnh cho chatBox */
  #chatBox::-webkit-scrollbar {
    width: 8px;
  }

  #chatBox::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 9999px;
  }

  #chatBox::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #3b82f6, #2563eb); /* blue-500 to blue-600 */
    border-radius: 9999px;
  }

  #chatBox::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #2563eb, #1e40af); /* darker on hover */
  }

  /* Firefox support */
  #chatBox {
    scrollbar-width: thin;
    scrollbar-color: #3b82f6 #f1f1f1;
  }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-blue-100 min-h-screen flex flex-col items-center justify-center px-4 py-6 font-sans">
 <!-- Nút trở về -->
 <a href="trangchu.php" 
     class="mb-4 self-start text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-full shadow transition duration-300 flex items-center gap-2">
    <i class="fas fa-arrow-left"></i> Trở về Trang chủ
  </a>
  <h1 class="text-3xl font-bold text-blue-700 mb-4 animate-pulse">💬 Chat với AI qua OpenRouter</h1>

  <div id="chatBox"
       class="w-full max-w-xl h-96 overflow-y-scroll border border-gray-300 p-4 bg-white rounded-2xl shadow-lg space-y-3 text-sm mb-4 scroll-smooth">
    <!-- Tin nhắn sẽ xuất hiện ở đây -->
  </div>

  <div class="flex justify-center w-full max-w-xl gap-2">
    <input
      id="userInput"
      type="text"
      placeholder="Nhập tin nhắn..."
      class="flex-grow px-4 py-2 border border-gray-300 rounded-lg shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
    />
    <button
      onclick="sendMessage()"
      class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition duration-300 shadow-md"
    ><i class="fas fa-paper-plane"></i> Gửi</button>
  </div>

  <script>
    const OPENROUTER_API_KEY = '';
    const MODEL_ID = 'openai/gpt-4o';

    function appendMessage(role, content) {
      const chatBox = document.getElementById("chatBox");
      const messageDiv = document.createElement("div");
      const icon = role === "user"
        ? `<i class="fas fa-user text-blue-600 mr-1"></i>`
        : `<i class="fas fa-robot text-green-600 mr-1"></i>`;
      const roleLabel = role === "user"
        ? `<span class="text-blue-600 font-semibold">Bạn</span>`
        : `<span class="text-green-600 font-semibold">Bot</span>`;

      const bubbleClass = role === "user" ? "user-msg px-3 py-2 rounded-xl" : "bot-msg px-3 py-2 rounded-xl";

      messageDiv.innerHTML = `<div class="${bubbleClass} shadow-md">${icon}${roleLabel}: ${content}</div>`;
      chatBox.appendChild(messageDiv);
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    function sendMessage() {
      const input = document.getElementById("userInput");
      const message = input.value.trim();
      if (!message) return;

      appendMessage("user", message);
      input.value = "";
      appendMessage("bot", "Đang phản hồi...");

      fetch("https://openrouter.ai/api/v1/chat/completions", {
        method: "POST",
        headers: {
          "Authorization": `Bearer ${OPENROUTER_API_KEY}`,
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          model: MODEL_ID,
          max_tokens: 1000,
          messages: [
            { role: "system", content: "Bạn là một trợ lý AI thân thiện, nói tiếng Việt." },
            { role: "user", content: message }
          ]
        })
      })
        .then(res => res.json())
        .then(data => {
          const reply = (data.choices && data.choices[0]?.message?.content)
            ? data.choices[0].message.content
            : data.error ? `❌ Lỗi API: ${data.error.message}` : "❌ Không có phản hồi từ AI.";

          const chatBox = document.getElementById("chatBox");
          const lastBot = [...chatBox.children].reverse().find(el => el.textContent.includes("Đang phản hồi..."));
          if (lastBot) chatBox.removeChild(lastBot);
          appendMessage("bot", reply);
        })
        .catch(err => {
          console.error(err);
          appendMessage("bot", "❌ Lỗi kết nối đến OpenRouter.");
        });
    }

    document.getElementById("userInput").addEventListener("keypress", function (e) {
      if (e.key === "Enter") sendMessage();
    });
  </script>

</body>
</html>
