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

// Truy vấn khách hàng online
$sql_online = "
    SELECT 'Online' AS loai, u.iduser, u.hoten, u.tendn, u.email, u.sdt, u.diachi, u.thoigian,
           COUNT(dh.iddh) AS so_donhang
    FROM user u
    LEFT JOIN donhang dh ON u.sdt = dh.sdt
    WHERE u.quyen = 1
    GROUP BY u.iduser, u.hoten, u.tendn, u.email, u.sdt, u.diachi, u.thoigian
";

// Truy vấn khách hàng offline
$sql_offline = "
    SELECT 'Offline' AS loai, NULL AS iduser, dh.tenkh AS hoten, NULL AS tendn, NULL AS email,
           dh.sdt, NULL AS diachi, MAX(dh.thoigian) AS thoigian, COUNT(dh.iddh) AS so_donhang
    FROM donhang dh
    WHERE dh.sdt NOT IN (SELECT u.sdt FROM user u WHERE u.sdt IS NOT NULL)
    GROUP BY dh.sdt, dh.tenkh
";

// Kết hợp online và offline, sắp xếp theo số đơn hàng
$sql = "($sql_online) UNION ALL ($sql_offline) ORDER BY so_donhang DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit;
}

// Xuất Word nếu có tham số export=word
if (isset($_GET['export']) && $_GET['export'] === 'word') {
    if (empty($customers)) {
        echo "Không có dữ liệu để xuất Word.";
        exit;
    }

    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="bao_cao_khach_hang_' . date('Ymd') . '.doc"');

    $html = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Báo Cáo Khách Hàng</title>
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
        <h1>BÁO CÁO KHÁCH HÀNG</h1>
        <p style="text-align: center;">Ngày lập báo cáo: Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y') . '</p>
        <table>
            <thead>
                <tr>
                    <th width="10%">Loại</th>
                    <th width="10%">ID</th>
                    <th width="15%">Họ tên</th>
                    <th width="10%">Tên đăng nhập</th>
                    <th width="15%">Email</th>
                    <th width="10%">SĐT</th>
                    <th width="20%">Địa chỉ</th>
                    <th width="15%">Thời gian</th>
                    <th width="10%">Số đơn hàng</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($customers as $customer) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($customer['loai']) . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['iduser'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['hoten']) . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['tendn'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['email'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['sdt']) . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['diachi'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['thoigian']) . '</td>';
        $html .= '<td>' . htmlspecialchars($customer['so_donhang']) . '</td>';
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

    echo $html;
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Khách Hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold text-gray-800">Báo Cáo Khách Hàng</h1>
            <div class="flex gap-2">
                <a href="?export=word" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm transition-colors">
                    Xuất Word
                </a>
                <a href="#" onclick="trove()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-sm transition-colors">
                    Trở về
                </a>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Loại</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Họ tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tên đăng nhập</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">SĐT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Địa chỉ</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Thời gian</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Số đơn hàng</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($customer['loai']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($customer['iduser'] ?? ''); ?></td>
                                <td class="px-2"><?php echo htmlspecialchars($customer['hoten']); ?></td>
                                <td class="px-2"><?php echo htmlspecialchars($customer['tendn'] ?? ''); ?></td>
                                <td class="px-2"><?php echo htmlspecialchars($customer['email'] ?? ''); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($customer['sdt']); ?></td>
                                <td class="px-2"><?php echo htmlspecialchars($customer['diachi'] ?? ''); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($customer['thoigian']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($customer['so_donhang']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>