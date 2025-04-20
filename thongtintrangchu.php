<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu cửa hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .clock {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.5rem;
            color: #ffffff;
            background: linear-gradient(135deg, #3c8dbc, #1a4971);
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: inline-block;
        }
        @media (max-width: 640px) {
            .clock {
                font-size: 1.2rem;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
    <div class="container mx-auto max-w-4xl my-12 p-8 bg-white rounded-2xl shadow-xl">
        <div class="flex items-center justify-center mb-8">
            <i class="fas fa-store text-3xl text-blue-600 mr-3"></i>
            <h1 class="text-4xl font-bold text-blue-600">Giới Thiệu Cửa Hàng</h1>
        </div>
        <div class="flex justify-center mb-8">
            <div id="clock" class="clock"></div>
        </div>
        <div class="space-y-6 text-gray-600 text-lg leading-relaxed">
            <p>
                Cửa hàng phụ kiện thời trang của chúng tôi chuyên cung cấp các sản phẩm phong cách và đa dạng như hoa tai, vòng cổ, túi xách, kính râm, mũ nón và nhiều phụ kiện cá nhân khác. Với mong muốn giúp khách hàng thể hiện cá tính riêng qua từng chi tiết nhỏ, chúng tôi luôn cập nhật xu hướng mới nhất, đảm bảo chất lượng sản phẩm và mức giá hợp lý.
            </p>
            <p>
                Không chỉ là nơi mua sắm, cửa hàng còn là điểm đến dành cho những ai yêu thích thời trang và muốn làm mới bản thân mỗi ngày. Dù bạn đang tìm kiếm sự tinh tế, năng động hay cá tính, chúng tôi đều có những món phụ kiện phù hợp để bạn hoàn thiện phong cách của mình.
            </p>
        </div>
    </div>

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