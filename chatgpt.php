<?php
// Thay YOUR_API_KEY bằng API key thực tế từ OpenAI
$apiKey = '';

$data = json_decode(file_get_contents("php://input"), true);
$userMessage = $data['message'] ?? '';

$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "Bạn là một trợ lý AI thông minh."],
        ["role" => "user", "content" => $userMessage]
    ],
    "temperature" => 0.7
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  // Lấy mã HTTP trả về
curl_close($ch);

// Kiểm tra phản hồi và trạng thái HTTP
if ($response && $httpcode == 200) {
    $result = json_decode($response, true);
    $reply = $result['choices'][0]['message']['content'] ?? 'Không có phản hồi.';
    echo json_encode(["reply" => $reply]);
} else {
    // Xử lý các mã lỗi HTTP phổ biến
    switch ($httpcode) {
        case 400:
            $error_message = "❌ Yêu cầu không hợp lệ. Vui lòng kiểm tra lại dữ liệu gửi đi.";
            break;
        case 401:
            $error_message = "❌ Không có quyền truy cập. Kiểm tra API key hoặc quyền truy cập.";
            break;
        case 403:
            $error_message = "❌ Lỗi quyền truy cập. API key có thể bị khóa.";
            break;
        case 429:
            $error_message = "❌ API hết hạn hoặc vượt quá số lượng yêu cầu trong ngày. Vui lòng thử lại sau.";
            break;
        case 500:
            $error_message = "❌ Lỗi máy chủ nội bộ. Vui lòng thử lại sau.";
            break;
        case 502:
            $error_message = "❌ Lỗi cổng dịch vụ. Vui lòng thử lại sau.";
            break;
        default:
            $error_message = "❌ Lỗi khi gọi API. Mã lỗi: $httpcode.";
            break;
    }

    echo json_encode([
        "reply" => $error_message,
        "http_status" => $httpcode,
        "response_raw" => $response
    ]);
}

?>

