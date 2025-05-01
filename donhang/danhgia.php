<?php
require_once '../config.php';

// Kiểm tra CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

// Kết nối database
try {
    $pdo = connectDatabase();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

// Lấy và kiểm tra dữ liệu đầu vào
$iddh = filter_input(INPUT_POST, 'iddh', FILTER_VALIDATE_INT) ?: 0;
$idkh = filter_input(INPUT_POST, 'idkh', FILTER_VALIDATE_INT) ?: 0;

if (!$iddh || !$idkh) {
    die('Invalid order or customer ID');
}

// Truy vấn sản phẩm trong đơn hàng
try {
    $stmt = $pdo->prepare("
        SELECT 
            ctdh.idctdh,
            ctdh.idsp,
            sp.tensp,
            sp.anh,
            ctdh.soluong,
            ctdh.gia,
            ctdh.giagoc,
            ctdh.giagiam
        FROM chitietdonhang ctdh
        JOIN sanpham sp ON ctdh.idsp = sp.idsp
        WHERE ctdh.iddh = :iddh AND ctdh.danhgia <> 1
    ");
    $stmt->execute(['iddh' => $iddh]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Query failed: ' . htmlspecialchars($e->getMessage()));
}

// Tạo CSRF token mới cho form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="danhgia.css?v=<?= time(); ?>">
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-start bg-gradient-to-br from-indigo-100 to-gray-100 px-4">
        <!-- Header -->
        <header class="w-full max-w-4xl py-8 sticky top-0 z-10 bg-gradient-to-br from-indigo-100 to-gray-100 backdrop-blur-md shadow-sm">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 flex items-center justify-center">
                <i class="fas fa-star text-yellow-400 mr-2 animate-pulse"></i>
                Đánh Giá Đơn Hàng #<?php echo htmlspecialchars($iddh); ?>
            </h2>
        </header>

        <!-- Content container -->
        <main class="container">
            <?php if (empty($products)): ?>
                <div class="text-center py-8">
                    <p class="text-xl text-gray-600">Không tìm thấy sản phẩm trong đơn hàng này.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="review-card bg-white rounded-2xl shadow-lg p-6 mb-8">
                        <!-- Product Info -->
                        <div class="flex items-center space-x-4 mb-6">
                            <img src="../<?php echo htmlspecialchars($product['anh']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['tensp']); ?>" 
                                 class="w-24 h-24 rounded-lg object-cover shadow-md pulse">
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-box text-indigo-500 mr-2"></i>
                                    <?php echo htmlspecialchars($product['tensp']); ?>
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-tag text-gray-400 mr-1"></i>
                                    Mã sản phẩm: <?php echo htmlspecialchars($product['idsp']); ?>
                                </p>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-shopping-cart text-gray-400 mr-1"></i>
                                    Số lượng: <?php echo htmlspecialchars($product['soluong']); ?> | 
                                    Giá: <?php echo number_format($product['gia']); ?>₫
                                </p>
                            </div>
                        </div>

                        <!-- Rating Stars -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-star-half-alt text-yellow-400 mr-1"></i>
                                Đánh giá sao
                            </label>
                            <div class="flex space-x-2 rating" data-idsp="<?php echo htmlspecialchars($product['idsp']); ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button class="star text-2xl text-gray-300 focus:outline-none" 
                                            data-value="<?php echo $i; ?>">
                                        <i class="fas fa-star"></i>
                                    </button>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Review Content -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-comment-alt text-indigo-500 mr-1"></i>
                                Nhận xét
                            </label>
                            <textarea class="review-content w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                      rows="4" 
                                      placeholder="Viết nhận xét của bạn..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-right">
                            <button class="submit-btn bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center justify-center" 
                                    data-idsp="<?php echo htmlspecialchars($product['idsp']); ?>" 
                                    data-idctdh="<?php echo htmlspecialchars($product['idctdh']); ?>">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Gửi Đánh Giá
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Handle star rating
        document.querySelectorAll('.rating').forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.dataset.value);
                    stars.forEach(s => {
                        const isActive = parseInt(s.dataset.value) <= value;
                        s.classList.toggle('text-yellow-400', isActive);
                        s.classList.toggle('text-gray-300', !isActive);
                        if (isActive) {
                            s.classList.add('active');
                            setTimeout(() => s.classList.remove('active'), 400);
                        }
                    });
                    rating.dataset.rating = value;
                });
            });
        });

        // Handle review submission
        document.querySelectorAll('.submit-btn').forEach(button => {
            button.addEventListener('click', async () => {
                const container = button.closest('.review-card');
                const textarea = container.querySelector('.review-content');
                const ratingDiv = container.querySelector('.rating');

                const rating = parseInt(ratingDiv.dataset.rating) || 0;
                const idsp = button.dataset.idsp;
                const idctdh = button.dataset.idctdh;
                const content = textarea.value.trim();
                const idkh = <?php echo json_encode($idkh); ?>;
                const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';

                // Validation
                if (rating === 0) {
                    showToast('Vui lòng chọn số sao! <i class="fas fa-star"></i>', 'var(--error-color)');
                    return;
                }
                if (!content) {
                    showToast('Vui lòng nhập nhận xét! <i class="fas fa-comment-alt"></i>', 'var(--error-color)');
                    return;
                }
                if (content.length > 1000) {
                    showToast('Nhận xét không được vượt quá 1000 ký tự!', 'var(--error-color)');
                    return;
                }

                // Disable button
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang gửi...';

                try {
                    const response = await fetch('../donhang/luudanhgia.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrfToken
                        },
                        body: JSON.stringify({
                            idctdh,
                            idsp,
                            rating,
                            content,
                            idkh,
                            thoigian: new Date().toISOString()
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message + ' <i class="fas fa-check-circle"></i>', 'var(--success-color)');
                        resetForm(textarea, ratingDiv);
                        container.classList.add('opacity-50', 'pointer-events-none');
                    } else {
                        showToast(result.message + ' <i class="fas fa-exclamation-circle"></i>', 'var(--error-color)');
                    }
                } catch (error) {
                    console.error('Error submitting review:', error);
                    showToast('Có lỗi xảy ra, vui lòng thử lại! <i class="fas fa-exclamation-circle"></i>', 'var(--error-color)');
                } finally {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Gửi Đánh Giá';
                }
            });
        });

        // Utility functions
        function showToast(message, background) {
            Toastify({
                text: message,
                duration: 3000,
                style: { background },
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                escapeMarkup: false
            }).showToast();
        }

        function resetForm(textarea, ratingDiv) {
            textarea.value = '';
            ratingDiv.querySelectorAll('.star').forEach(star => {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            });
            ratingDiv.dataset.rating = 0;
        }
    </script>
</body>
</html>