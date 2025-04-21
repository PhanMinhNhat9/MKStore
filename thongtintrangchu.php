<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu cửa hàng</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="thongtintrangchu.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container">
        <div class="flex-center mb-8">
            <i class="fas fa-store"></i>
            <h1>Giới Thiệu Cửa Hàng</h1>
        </div>
        <div class="flex-center mb-8">
            <div id="clock" class="clock"></div>
        </div>
        <div class="space-y-6 text-content">
            <p>
                Cửa hàng phụ kiện thời trang của chúng tôi chuyên cung cấp các sản phẩm phong cách và đa dạng như hoa tai, vòng cổ, túi xách, kính râm, mũ nón và nhiều phụ kiện cá nhân khác. Với mong muốn giúp khách hàng thể hiện cá tính riêng qua từng chi tiết nhỏ, chúng tôi luôn cập nhật xu hướng mới nhất, đảm bảo chất lượng sản phẩm và mức giá hợp lý.
            </p>
            <p>
                Không chỉ là nơi mua sắm, cửa hàng còn là điểm đến dành cho những ai yêu thích thời trang và muốn làm mới bản thân mỗi ngày. Dù bạn đang tìm kiếm sự tinh tế, năng động hay cá tính, chúng tôi đều có những món phụ kiện phù hợp để bạn hoàn thiện phong cách của mình.
            </p>
        </div>
    </div>
    <button class="btn chat-ai" onclick="toggleChatAI()">
        <i class="fas fa-robot"></i> Chat AI
    </button>
    <div id="chatAIBox" class="chat-box hidden">
        <div class="chat-header">
            <span><i class="fas fa-robot"></i> Trợ lý AI</span>
        </div>
        <div class="chat-body">
            <p>Xin chào! Tôi có thể giúp gì cho bạn?</p>
            <div id="chatMessages" class="chat-messages"></div>
            <div class="chat-input">
                <input type="text" id="userInput" placeholder="Nhập tin nhắn...">
                <button onclick="sendMessage()">Gửi</button>
            </div>
        </div>
    </div>
    <script>
        const chatBox = document.getElementById("chatAIBox");
        const chatButton = document.querySelector(".btn.chat-ai");

        function toggleChatAI() {
            chatBox.classList.toggle("hidden");
        }

        document.addEventListener("click", function(e) {
            if (!chatBox.classList.contains("hidden") && 
                !chatBox.contains(e.target) && 
                !chatButton.contains(e.target)) {
                chatBox.classList.add("hidden");
            }
        });

        function sendMessage() {
            const input = document.getElementById("userInput");
            const message = input.value.trim();
            if (!message) return;

            appendMessage("user", message);
            input.value = "";

            // Gửi đến server (gọi OpenAI)
            fetch("chatgpt.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                appendMessage("bot", data.reply);
            })
            .catch(err => {
                appendMessage("bot", "❌ Có lỗi khi gọi API.");
            });
        }

        function appendMessage(sender, text) {
            const chat = document.getElementById("chatMessages");
            const div = document.createElement("div");
            div.className = `message ${sender}`;
            div.textContent = text;
            chat.appendChild(div);
            chat.scrollTop = chat.scrollHeight;
        }

        // Xử lý nhấn Enter để gửi tin nhắn
        document.getElementById("userInput").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                sendMessage();
            }
        });
    </script>
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>