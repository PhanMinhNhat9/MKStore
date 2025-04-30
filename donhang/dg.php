<?php
// Giả sử đã kết nối CSDL bằng PDO: $pdo
require_once '../config.php';
$pdo = connectDatabase();
$iddh = 2;

// Lấy sản phẩm trong đơn hàng
$stmt = $pdo->prepare("
    SELECT ctdh.idctdh, ctdh.idsp, sp.tensp, sp.anh, 
           ctdh.soluong, ctdh.gia, ctdh.giagoc, ctdh.giagiam
    FROM chitietdonhang AS ctdh
    JOIN sanpham AS sp ON ctdh.idsp = sp.idsp
    WHERE ctdh.iddh = ?
");
$stmt->execute([$iddh]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đánh giá đơn hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .star:hover { transform: scale(1.2); transition: transform 0.2s ease; }
        .star { transition: color 0.3s ease, transform 0.2s ease; }
        .submit-btn:hover { background: linear-gradient(to right, #4f46e5, #7c3aed); transform: translateY(-2px); }
        .submit-btn { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto space-y-8">
        <h2 class="text-3xl font-bold text-center text-gray-800">Đánh giá đơn hàng #<?= htmlspecialchars($iddh) ?></h2>

        <?php foreach ($products as $product): ?>
        <div class="bg-white p-6 rounded-xl shadow-md fade-in">
            <!-- Thông tin sản phẩm -->
            <div class="flex items-center space-x-4 mb-4">
                <img src="../<?= htmlspecialchars($product['anh']) ?>" alt="Ảnh sản phẩm" class="w-20 h-20 rounded-lg object-cover">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($product['tensp']) ?></h3>
                    <p class="text-sm text-gray-500">Mã sản phẩm: <?= htmlspecialchars($product['idsp']) ?></p>
                    <p class="text-sm text-gray-500">Số lượng: <?= htmlspecialchars($product['soluong']) ?> | Giá: <?= number_format($product['gia']) ?>₫</p>
                </div>
            </div>

            <!-- Đánh giá sao -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Đánh giá sao</label>
                <div class="flex space-x-2 rating" data-idsp="<?= $product['idsp'] ?>">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button class="star text-2xl text-gray-300" data-value="<?= $i ?>">★</button>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Nội dung đánh giá -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nhận xét</label>
                <textarea class="reviewContent block w-full border border-gray-300 rounded-md p-2" rows="3" placeholder="Nhận xét của bạn..."></textarea>
            </div>

            <!-- Gửi đánh giá -->
            <div class="text-right">
                <button class="submit-btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded reviewSubmit" 
                        data-idsp="<?= $product['idsp'] ?>"
                        data-idctdh="<?= $product['idctdh'] ?>">
                    Gửi đánh giá
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.querySelectorAll('.rating').forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    let value = parseInt(star.dataset.value);
                    stars.forEach(s => {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                        if (parseInt(s.dataset.value) <= value) {
                            s.classList.add('text-yellow-400');
                        }
                    });
                    rating.dataset.rating = value;
                });
            });
        });

        document.querySelectorAll('.reviewSubmit').forEach(btn => {
            btn.addEventListener('click', async () => {
                const container = btn.closest('.fade-in');
                const textarea = container.querySelector('.reviewContent');
                const ratingDiv = container.querySelector('.rating');
                const rating = ratingDiv.dataset.rating || 0;
                const idsp = btn.dataset.idsp;
                const idctdh = btn.dataset.idctdh;
                const content = textarea.value.trim();

                if (rating == 0) {
                    Toastify({ text: "Vui lòng chọn số sao!", duration: 3000, backgroundColor: "#ef4444" }).showToast();
                    return;
                }

                if (!content) {
                    Toastify({ text: "Vui lòng nhập nhận xét!", duration: 3000, backgroundColor: "#ef4444" }).showToast();
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Đang gửi...';

                // Gửi dữ liệu (giả lập)
                await new Promise(resolve => setTimeout(resolve, 1000));
                console.log('Gửi đánh giá:', {
                    idctdh,
                    idsp,
                    rating,
                    content,
                    idkh: 'KH123',
                    thoigian: new Date().toISOString()
                });

                Toastify({
                    text: "Đánh giá đã được gửi!",
                    duration: 3000,
                    backgroundColor: "#10b981"
                }).showToast();

                btn.disabled = false;
                btn.textContent = 'Gửi đánh giá';
                textarea.value = '';
                ratingDiv.querySelectorAll('.star').forEach(s => s.classList.remove('text-yellow-400'));
                ratingDiv.dataset.rating = 0;
            });
        });
    </script>
</body>
</html>
