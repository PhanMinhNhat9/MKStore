<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giới thiệu cửa hàng</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  </head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col items-center justify-center px-4">
<style>
  .container {
    margin-top: 5px;
    margin-bottom: 5px;
  }
</style>
  <div class="container max-w-3xl mx-4 bg-white shadow-md rounded-lg p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-center space-x-3">
      <i class="fas fa-store text-3xl text-blue-600"></i>
      <h1 class="text-2xl md:text-3xl font-bold text-center">Giới Thiệu Cửa Hàng</h1>
    </div>

    <!-- Clock -->
    <div class="flex justify-center">
      <div id="clock" class="text-lg font-mono text-gray-700 bg-gray-200 rounded px-4 py-1"></div>
    </div>

    <!-- Content -->
    <div class="space-y-4 text-justify leading-relaxed text-base md:text-lg">
      <p>
        Cửa hàng phụ kiện thời trang của chúng tôi chuyên cung cấp các sản phẩm phong cách và đa dạng như hoa tai, vòng cổ, túi xách, kính râm, mũ nón và nhiều phụ kiện cá nhân khác. Với mong muốn giúp khách hàng thể hiện cá tính riêng qua từng chi tiết nhỏ, chúng tôi luôn cập nhật xu hướng mới nhất, đảm bảo chất lượng sản phẩm và mức giá hợp lý.
      </p>
      <p>
        Không chỉ là nơi mua sắm, cửa hàng còn là điểm đến dành cho những ai yêu thích thời trang và muốn làm mới bản thân mỗi ngày. Dù bạn đang tìm kiếm sự tinh tế, năng động hay cá tính, chúng tôi đều có những món phụ kiện phù hợp để bạn hoàn thiện phong cách của mình.
      </p>
    </div>
  </div>

  <!-- Chat AI Button -->
  <button onclick="chatai()" class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-full shadow-lg flex items-center space-x-2">
    <i class="fas fa-robot"></i>
    <span>Chat AI</span>
  </button>

  <script>
    function chatai() {
      window.top.location.href = "chatai.php";
    }
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
