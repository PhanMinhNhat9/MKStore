<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chat AI - OpenRouter</title>
  <style>
    #chatBox {
      width: 100%;
      max-width: 600px;
      height: 400px;
      overflow-y: auto;
      border: 1px solid #ccc;
      padding: 10px;
      margin: 20px auto;
      font-family: Arial, sans-serif;
      background: #f9f9f9;
    }
    .message { margin: 10px 0; }
    .user { color: blue; font-weight: bold; }
    .bot { color: green; font-weight: bold; }
    #userInput {
      width: 60%;
      padding: 8px;
      font-size: 16px;
    }
    button {
      padding: 8px 16px;
      font-size: 16px;
    }
  </style>
</head>
<body>

<div id="chatBox"></div>

<div style="text-align:center;">
  <input id="userInput" placeholder="Nhập tin nhắn..." />
  <button onclick="sendMessage()">Gửi</button>
</div>

<script>
const OPENROUTER_API_KEY = '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'; // ⚠️ Dán API key OpenRouter tại đây
const MODEL_ID = 'openai/gpt-4o'; // ✅ Model miễn phí phổ biến

function appendMessage(role, content) {
  const chatBox = document.getElementById("chatBox");
  const roleName = role === "user" ? "👤 Bạn" : "🤖 Bot";
  const className = role === "user" ? "user" : "bot";
  chatBox.innerHTML += `<div class="message"><span class="${className}">${roleName}:</span> ${content}</div>`;
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
    max_tokens: 1000, // ✅ hạn chế token để tránh lỗi quota
    messages: [
      { role: "system", content: "Bạn là một trợ lý AI thân thiện, nói tiếng Việt." },
      { role: "user", content: message }
    ]
  })
})

  .then(res => res.json())
  .then(data => {
  const choices = data.choices;
  let reply = "❌ Không có phản hồi từ AI.";

  if (Array.isArray(choices) && choices.length > 0 && choices[0].message) {
    reply = choices[0].message.content;
  } else if (data.error) {
    reply = `❌ Lỗi từ API: ${data.error.message || "Không rõ"}`;
  }

  const lastBotMsg = document.querySelectorAll(".bot:last-child")[0];
  if (lastBotMsg) {
    lastBotMsg.innerHTML = `🤖 Bot: ${reply}`;
  } else {
    appendMessage("bot", reply);
  }
})
  .catch(err => {
    console.error(err);
    const lastBotMsg = document.querySelectorAll(".bot:last-child")[0];
    if (lastBotMsg) {
      lastBotMsg.innerHTML = `❌ Lỗi gọi GPT từ OpenRouter.`;
    } else {
      appendMessage("bot", "❌ Lỗi kết nối.");
    }
  });
}

// Gửi khi nhấn Enter
document.getElementById("userInput").addEventListener("keypress", function(e) {
  if (e.key === "Enter") sendMessage();
});
</script>

</body>
</html>
