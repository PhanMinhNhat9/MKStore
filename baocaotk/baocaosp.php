<?php
// Kết nối cơ sở dữ liệu
require_once '../config.php';
$pdo = connectDatabase();

// Kiểm tra session user
if (!isset($_SESSION['user']['hoten'])) {
    echo "Không tìm thấy thông tin người dùng. Vui lòng đăng nhập.";
    exit;
}
$nguoi_lap = $_SESSION['user']['hoten'];

// Xử lý bộ lọc
$where_conditions = ["dh.trangthai = :trangthai"];
$params = [':trangthai' => 'Đã thanh toán'];

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : '';

if ($start_date) {
    $where_conditions[] = "dh.thoigian >= :start_date";
    $params[':start_date'] = $start_date;
}
if ($end_date) {
    $where_conditions[] = "dh.thoigian <= :end_date";
    $params[':end_date'] = $end_date . ' 23:59:59';
}
if ($min_price !== '') {
    $where_conditions[] = "sp.giaban >= :min_price";
    $params[':min_price'] = $min_price;
}
if ($max_price !== '') {
    $where_conditions[] = "sp.giaban <= :max_price";
    $params[':max_price'] = $max_price;
}

// Tạo câu truy vấn
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
$sql = "
    SELECT sp.idsp, sp.tensp, sp.giaban, sp.soluong AS soluong_tonkho, sp.anh, sp.iddm, sp.thoigianthemsp,
           SUM(ctdh.soluong) AS soluongban, MAX(dh.thoigian) AS thoigian, dg.sosao, dg.noidung,
           (sp.soluong + SUM(ctdh.soluong)) AS soluong_tongbandau
    FROM sanpham sp
    INNER JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
    INNER JOIN donhang dh ON ctdh.iddh = dh.iddh
    LEFT JOIN danhgia dg ON sp.idsp = dg.idsp AND ctdh.idctdh = dg.idctdh
    $where_clause
    GROUP BY sp.idsp, sp.tensp, sp.giaban, sp.soluong, sp.anh, sp.iddm, sp.thoigianthemsp, dg.sosao, dg.noidung
    ORDER BY MAX(dh.thoigian) DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit;
}

// Xuất Word nếu có tham số export=word
if (isset($_GET['export']) && $_GET['export'] === 'word') {
    // Kiểm tra dữ liệu
    if (empty($products)) {
        echo "<script>
                alert('Không có dữ liệu để xuất Word.');
                window.location.href = '../baocaotk/baocaosp.php';
            </script>";
        exit;
    }


    // Thiết lập header cho file Word
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="bao_cao_san_pham_' . date('Ymd') . '.doc"');

    // Nội dung HTML cho file Word
    $html = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Báo Cáo Sản Phẩm</title>
        <style>
            body { font-family: "Times New Roman", Times, serif; font-size: 13pt; margin: 2cm; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid black; padding: 8px; text-align: left; font-size: 13pt; }
            th { background-color: #d3d3d3; font-weight: bold; }
            h1 { font-size: 16pt; font-weight: bold; text-align: center; text-transform: uppercase; }
            .signature { margin-top: 50px; text-align: right; }
            .signature p { margin: 10px 0; }
        </style>
    </head>
    <body>
        <h1>BÁO CÁO SẢN PHẨM</h1>
        <p style="text-align: center;">Ngày lập báo cáo: Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y') . '</p>
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Tên sản phẩm</th>
                    <th width="10%">Giá bán</th>
                    <th width="10%">Tồn kho</th>
                    <th width="10%">Bán ra</th>
                    <th width="10%">Tổng ban đầu</th>
                    <th width="10%">Danh mục</th>
                    <th width="15%">Thời gian bán</th>
                    <th width="20%">Đánh giá</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($products as $product) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($product['idsp']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['tensp']) . '</td>';
        $html .= '<td>' . number_format($product['giaban'], 0, ',', '.') . ' VNĐ</td>';
        $html .= '<td>' . htmlspecialchars($product['soluong_tonkho']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['soluongban']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['soluong_tongbandau']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['iddm']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['thoigian']) . '</td>';
        $html .= '<td>';
        if ($product['sosao']) {
            $html .= str_repeat('⭐', $product['sosao']) . '<br>' . htmlspecialchars($product['noidung']);
        } else {
            $html .= 'Chưa có đánh giá';
        }
        $html .= '</td>';
        $html .= '</tr>';
    }

    $html .= '
            </tbody>
        </table>
        <div class="signature">
            <p>Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y') . '</p>
            <p><strong>Người lập báo cáo</strong></p>
            <p style="margin-top: 50px;">' . htmlspecialchars($nguoi_lap) . '</p>
        </div>
    </body>
    </html>';

    // Xuất nội dung
    echo $html;
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Sản Phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-7xl mx-auto">
        <style>
            .container-boloc {
                width: 500px;
                margin: 20px auto;
                margin-top: 0;
            }
            .boloc {
                margin-left: 60px;
            }
        </style>
        <!-- Bộ lọc -->
        <div class="container-boloc bg-white p-3 rounded-lg shadow-sm mb-3">
            <div class="boloc space-y-2">
                <!-- Bộ lọc thời gian -->
                <form method="GET" class="grid grid-cols-[150px_150px_40px] gap-2 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Từ ngày</label>
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" class="mt-1 block w-[150px] border border-gray-300 rounded-lg p-2 text-sm bg-gray-50 focus:ring-blue-200 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Đến ngày</label>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" class="mt-1 block w-[150px] border border-gray-300 rounded-lg p-2 text-sm bg-gray-50 focus:ring-blue-200 focus:border-blue-500">
                    </div>
                    <div>
                        <button type="submit" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm transition-colors w-[40px]">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Bộ lọc giá cả -->
                <form method="GET" class="grid grid-cols-[150px_150px_40px] gap-2 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giá min</label>
                        <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="VD: 100000" class="mt-1 block w-[150px] border border-gray-300 rounded-lg p-2 text-sm bg-gray-50 focus:ring-blue-200 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giá max</label>
                        <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="VD: 1000000" class="mt-1 block w-[150px] border border-gray-300 rounded-lg p-2 text-sm bg-gray-50 focus:ring-blue-200 focus:border-blue-500">
                    </div>
                    <div>
                        <button type="submit" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm transition-colors w-[40px]">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Nút điều khiển -->
                <div class="flex gap-2">
                    <a href="baocaosp.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm transition-colors">Xóa bộ lọc</a>
                    <a href="?start_date=<?php echo htmlspecialchars($start_date); ?>&end_date=<?php echo htmlspecialchars($end_date); ?>&min_price=<?php echo htmlspecialchars($min_price); ?>&max_price=<?php echo htmlspecialchars($max_price); ?>&export=word" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm transition-colors">Xuất Word</a>
                    <a href="#" onclick="trove()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-sm transition-colors">Trở về</a>
                </div>
            </div>
        </div>
        <script>
            function trove() {
                window.top.location.href = "../trangchu.php";
            }
        </script>
        <!-- Bảng dữ liệu -->
        <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tên sản phẩm</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Giá bán</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Tồn kho</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Bán ra</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Tổng ban đầu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Ảnh</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Danh mục</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Thời gian bán</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Đánh giá</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($product['idsp']); ?></td>
                                <td class="px-2"><?php echo htmlspecialchars($product['tensp']); ?></td>
                                <td class="px-6 py-4 text-right"><?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($product['soluong_tonkho']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($product['soluongban']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($product['soluong_tongbandau']); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <img src="../<?php echo htmlspecialchars($product['anh']); ?>" alt="Ảnh sản phẩm" class="h-12 w-12 object-cover rounded mx-auto">
                                </td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($product['iddm']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($product['thoigian']); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($product['sosao']): ?>
                                        <?php echo str_repeat('⭐', $product['sosao']); ?>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($product['noidung']); ?></p>
                                    <?php else: ?>
                                        Chưa có đánh giá
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>