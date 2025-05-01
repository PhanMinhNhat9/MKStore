<?php   
require_once '../config.php';

// Kết nối database
try {
    $pdo = connectDatabase();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Kết nối cơ sở dữ liệu thất bại: ' . htmlspecialchars($e->getMessage()));
}

// ID đơn hàng
$iddh = 2;

// Truy vấn chi tiết đánh giá
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.iduser,
            u.hoten,
            s.idsp,
            s.tensp,
            dg.sosao,
            dg.noidung,
            dg.thoigian,
            ctdh.soluong,
            ctdh.gia
        FROM danhgia dg
        JOIN chitietdonhang ctdh ON dg.idctdh = ctdh.idctdh
        JOIN sanpham s ON dg.idsp = s.idsp
        JOIN user u ON dg.idkh = u.iduser
        WHERE ctdh.iddh = :iddh
        ORDER BY dg.thoigian DESC
    ");
    $stmt->execute(['iddh' => $iddh]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Truy vấn thất bại: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đánh Giá Đơn Hàng #<?php echo htmlspecialchars($iddh); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --success-color: #10b981;
            --warning-color: #facc15;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0e7ff, #f3f4f6);
            font-family: 'Inter', sans-serif;
        }

        .container {
            max-width: 960px;
            max-height: 85vh;
            overflow-y: auto;
            padding: 1rem;
            scrollbar-width: thin;
            scrollbar-color: #4f46e5 transparent;
        }

        .review-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .star {
            color: #d1d5db;
        }

        .star.filled {
            color: var(--warning-color);
        }

        .header {
            background: linear-gradient(to elements;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .time {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .icon {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-start bg-gradient-to-br from-indigo-100 to-gray-100 px-4">
        <!-- Header -->
        <header class="w-full max-w-4xl py-8">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 flex items-center justify-center">
                <i class="fas fa-star text-yellow-400 mr-2"></i>
                Chi Tiết Đánh Giá Đơn Hàng #<?php echo htmlspecialchars($iddh); ?>
            </h2>
        </header>

        <!-- Reviews -->
        <main class="container">
            <?php if (empty($reviews)): ?>
                <div class="text-center py-8">
                    <p class="text-xl text-gray-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Chưa có đánh giá nào cho đơn hàng này.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <!-- User Info -->
                        <div class="flex items-center mb-4">
                            <i class="fas fa-user-circle text-3xl text-indigo-500 mr-3"></i>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <?php echo htmlspecialchars($review['hoten']); ?>
                                    <span class="text-sm text-gray-500">(ID: <?php echo htmlspecialchars($review['iduser']); ?>)</span>
                                </h3>
                                <p class="time">
                                    <i class="fas fa-clock icon"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($review['thoigian'])); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="mb-4">
                            <h4 class="text-base font-medium text-gray-700">
                                <i class="fas fa-box icon"></i>
                                Sản phẩm: <?php echo htmlspecialchars($review['tensp']); ?>
                                <span class="text-sm text-gray-500">(ID: <?php echo htmlspecialchars($review['idsp']); ?>)</span>
                            </h4>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-shopping-cart icon"></i>
                                Số lượng: <?php echo htmlspecialchars($review['soluong']); ?> | 
                                Giá: <?php echo number_format($review['gia']); ?>₫
                            </p>
                        </div>

                        <!-- Rating -->
                        <div class="mb-4">
                            <div class="flex">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star star <?php echo $i <= $review['sosao'] ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Review Content -->
                        <div>
                            <p class="text-gray-600">
                                <i class="fas fa-comment-alt icon"></i>
                                <?php echo nl2br(htmlspecialchars($review['noidung'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>