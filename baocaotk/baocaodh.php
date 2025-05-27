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

// Truy vấn danh sách đơn hàng
$sql = "
    SELECT dh.iddh, dh.sdt, dh.tenkh, dh.tongtien, dh.hientrang, dh.trangthai, dh.phuongthuctt, dh.thoigian,
           COALESCE(SUM(ct.soluong), 0) AS so_luong_sp
    FROM donhang dh
    LEFT JOIN chitietdonhang ct ON dh.iddh = ct.iddh
    GROUP BY dh.iddh, dh.sdt, dh.tenkh, dh.tongtien, dh.hientrang, dh.trangthai, dh.phuongthuctt, dh.thoigian
    ORDER BY dh.thoigian DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit;
}

// Xử lý xuất Word
if (isset($_GET['export']) && $_GET['export'] === 'word') {
    if (empty($orders)) {
        echo "Không có dữ liệu để xuất Word.";
        exit;
    }

    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="bao_cao_don_hang_' . date('Ymd') . '.doc"');

    $html = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Báo Cáo Đơn Hàng</title>
        <style>
            body { font-family: "Times New Roman", Times, serif; font-size: 13pt; margin: 2cm; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid black; padding: 8px; text-align: left; font-size: 13pt; }
            th { background-color: #d3d3d3; font-weight: bold; }
            h1 { font-size: 16pt; font-weight: bold; text-align: center; text-transform: uppercase; }
            .signature { margin-top: 50px; text-align: right; }
            .signature p { margin: 10px 0; }
            .right { text-align: right; }
            .center { text-align: center; }
        </style>
    </head>
    <body>
        <h1>BÁO CÁO ĐƠN HÀNG</h1>
        <p style="text-align: center;">Ngày lập báo cáo: Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y') . '</p>
        <table>
            <thead>
                <tr>
                    <th width="10%">ID Đơn</th>
                    <th width="10%">SĐT</th>
                    <th width="15%">Tên khách</th>
                    <th width="10%">Tổng tiền</th>
                    <th width="10%">Hiện trạng</th>
                    <th width="10%">Trạng thái</th>
                    <th width="15%">Phương thức TT</th>
                    <th width="15%">Thời gian</th>
                    <th width="10%">Số lượng SP</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($orders as $order) {
        $html .= '<tr>';
        $html .= '<td class="center">' . htmlspecialchars($order['iddh']) . '</td>';
        $html .= '<td class="center">' . htmlspecialchars($order['sdt']) . '</td>';
        $html .= '<td>' . htmlspecialchars($order['tenkh']) . '</td>';
        $html .= '<td class="right">' . number_format($order['tongtien'], 0, ',', '.') . ' VNĐ</td>';
        $html .= '<td class="center">' . htmlspecialchars($order['hientrang']) . '</td>';
        $html .= '<td class="center">' . htmlspecialchars($order['trangthai']) . '</td>';
        $html .= '<td class="center">' . htmlspecialchars($order['phuongthuctt']) . '</td>';
        $html .= '<td class="center">' . htmlspecialchars($order['thoigian']) . '</td>';
        $html .= '<td class="center">' . htmlspecialchars($order['so_luong_sp']) . '</td>';
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

// Xử lý yêu cầu chi tiết đơn hàng (AJAX)
if (isset($_GET['get_details']) && isset($_GET['iddh'])) {
    $iddh = (int)$_GET['iddh'];
    $sql_details = "
        SELECT idsp, soluong, gia, giagoc, giagiam, danhgia
        FROM chitietdonhang
        WHERE iddh = :iddh
    ";
    try {
        $stmt = $pdo->prepare($sql_details);
        $stmt->execute([':iddh' => $iddh]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($details);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Đơn Hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 50; }
        .modal-content { background-color: white; margin: 10% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; }
        .modal.show { display: block; }
    </style>
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold text-gray-800">Báo Cáo Đơn Hàng</h1>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">ID Đơn</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">SĐT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tên khách</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Tổng tiền</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Hiện trạng</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Phương thức TT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Thời gian</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Số lượng SP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center">
                                    <a href="#" class="text-blue-600 hover:underline view-details" data-iddh="<?php echo htmlspecialchars($order['iddh']); ?>">
                                        <?php echo htmlspecialchars($order['iddh']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($order['sdt']); ?></td>
                                <td class="px-2"><?php echo htmlspecialchars($order['tenkh']); ?></td>
                                <td class="px-6 text-right"><?php echo number_format($order['tongtien'], 0, ',', '.'); ?> VNĐ</td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($order['hientrang']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($order['trangthai']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($order['phuongthuctt']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($order['thoigian']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo htmlspecialchars($order['so_luong_sp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal chi tiết đơn hàng -->
        <div>
            <div id="orderModal" class="modal">
                <div class="modal-content">
                    <h2 class="text-lg font-semibold mb-4">Chi tiết đơn hàng</h2>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2 text-sm font-medium">ID Sản phẩm</th>
                                <th class="border border-gray-300 p-2 text-sm font-medium">Số lượng</th>
                                <th class="border border-gray-300 p-2 text-sm font-medium">Giá bán</th>
                                <th class="border border-gray-300 p-2 text-sm font-medium">Giá gốc</th>
                                <th class="border border-gray-300 p-2 text-sm font-medium">Giá giảm</th>
                                <th class="border border-gray-300 p-2 text-sm font-medium">Đánh giá</th>
                            </tr>
                        </thead>
                        <tbody id="modalBody"></tbody>
                    </table>
                    <div class="mt-4 text-right">
                        <button onclick="document.getElementById('orderModal').class='px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-sm">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

<script>
    document.querySelectorAll('.view-details').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const iddh = this.getAttribute('data-iddh');
            fetch(`?get_details=1&iddh=${iddh}`)
                .then(response => res.json())
                .then(data => {
                    if (data.error) {
                        alert('Lỗi: ' + data.error);
                        return;
                    }
                    const tbody = document.getElementById('modalBody');
                    tbody.innerHTML = '';
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="border border-gray-300 p-2 text-center">${item.idsp}</td>
                            <td class="border border-gray-300 p-2 text-center">${item.soluong}</td>
                            <td class="border border-gray-300 p-2 text-right">${item.gia.toLocaleString('vi-VN')} VNĐ</td>
                            <td class="border border-gray-300 p-2 text-right">${item.giagoc.toLocaleString('vi-VN')} VNĐ</td>
                            <td class="border border-gray-300 p-2 text-right">${item.giagiam ? item.giagiam.toLocaleString('vi-VN') : '0'} VNĐ</td>
                            <td class="border border-gray-300 p-2 text-center">${item.danhgia || '-'}</td>
                        `;
                        tbody.appendChild(row);
                    });
                    document.getElementById('orderModal').classList.add('show');
                })
                .catch(error => alert('Lỗi khi tải chi tiết: ' + error));
        });
    });

    document.querySelector('.modal-close').addEventListener('click', () => {
        document.getElementById('orderModal').classList.remove('show');
    });
</script>
</body>
</html>