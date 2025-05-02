<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update["message"])) {
    $chat_id = $update["message"]["chat"]["id"];
    $user_msg = $update["message"]["text"];

    // Lưu tin nhắn vào file (hoặc DB)
    file_put_contents("messages.txt", "From $chat_id: $user_msg\n", FILE_APPEND);

    // Trả lời lại user nếu muốn
    $reply = "✅ Bot đã nhận được tin nhắn của bạn!";
    $botToken = "BOT_TOKEN";
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    file_get_contents($url . "?chat_id=$chat_id&text=" . urlencode($reply));
}
?>
