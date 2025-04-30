<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đánh giá đơn hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .fade-in {
      animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .star {
      cursor: pointer;
      transition: transform 0.2s ease, color 0.2s ease;
    }
    .star:hover {
      transform: scale(1.2);
      color: #facc15;
    }
    .submit-btn {
      transition: all 0.3s ease;
    }
    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md fade-in">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Đánh giá sản phẩm</h2>

    <!-- Sản phẩm -->
    <div class="flex items-center gap-4 mb-6">
      <img src="https://via.placeholder.com/80" alt="Sản phẩm" class="rounded-xl w-20 h-20 object-cover border">
      <div>
        <p class="text-base text-gray-700 font-semibold">Tên sản phẩm: <span id="productName">Tên sản phẩm mẫu</span></p>
        <p class="text-sm text-gray-500">Mã SP: <span id="productId">SP123</span></p>
      </div>
    </div>

    <!-- Đánh giá sao -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">Chọn số sao</label>
      <div class="flex gap-2">
        <button class="star text-3xl text-gray-300" data-value="1">★</button>
        <button class="star text-3xl text-gray-300" data-value="2">★</button>
        <button class="star text-3xl text-gray-300" data-value="3">★</button>
        <button class="star text-3xl text-gray-300" data-value="4">★</button>
        <button class="star text-3xl text-gray-300" data-value="5">★</button>
      </div>
    </div>

    <!-- Nhận xét -->
    <div class="mb-6">
      <label for="reviewContent" class="block text-sm font-medium text-gray-700 mb-2">Nhận xét của bạn</label>
      <textarea id="reviewContent" rows="4" placeholder="Hãy chia sẻ cảm nhận của bạn..."
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"></textarea>
    </div>

    <!-- Nút gửi -->
    <div class="text-center">
      <button id="submitReview"
        class="submit-btn bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md w-full">Gửi đánh giá</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script>
    const stars = document.querySelectorAll('.star');
    let selectedRating = 0;

    stars.forEach(star => {
      star.addEventListener('click', () => {
        selectedRating = star.getAttribute('data-value');
        stars.forEach(s => {
          s.classList.remove('text-yellow-400');
          s.classList.add('text-gray-300');
          if (s.getAttribute('data-value') <= selectedRating) {
            s.classList.remove('text-gray-300');
            s.classList.add('text-yellow-400');
          }
        });
      });
    });

    document.getElementById('submitReview').addEventListener('click', async () => {
      const reviewContent = document.getElementById('reviewContent').value;
      const productId = document.getElementById('productId').textContent;
      const submitBtn = document.getElementById('submitReview');

      if (selectedRating === 0) {
        Toastify({ text: "Vui lòng chọn số sao!", duration: 3000, backgroundColor: "#ef4444" }).showToast();
        return;
      }

      if (!reviewContent.trim()) {
        Toastify({ text: "Vui lòng nhập nhận xét!", duration: 3000, backgroundColor: "#ef4444" }).showToast();
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = "Đang gửi...";

      const reviewData = {
        idsp: productId,
        sosao: selectedRating,
        noidung: reviewContent,
        idkh: "KH123",
        thoigian: new Date().toISOString()
      };

      await new Promise(resolve => setTimeout(resolve, 1000));

      console.log('Dữ liệu đánh giá:', reviewData);

      Toastify({ text: "Đánh giá đã được gửi!", duration: 3000, backgroundColor: "#10b981" }).showToast();

      submitBtn.disabled = false;
      submitBtn.textContent = "Gửi đánh giá";
      document.getElementById('reviewContent').value = '';
      stars.forEach(s => s.classList.add('text-gray-300'));
      selectedRating = 0;
    });
  </script>
</body>
</html>
