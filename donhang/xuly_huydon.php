<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Hủy Đơn Hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
        <?php
        require_once '../config.php';
        $pdo = connectDatabase();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $iddh = $_POST['iddh'] ?? '';
            $idkh = $_POST['idkh'] ?? '';
            $lydoArray = $_POST['lydo'] ?? [];
            $lydoKhac = $_POST['lydo_khac'] ?? '';

            // Gom tất cả lý do thành 1 chuỗi
            $lydoText = '';
            if (!empty($lydoArray)) {
                $lydoText = implode(', ', $lydoArray);
                // Nếu có chọn "Khác", kèm thêm lý do khác
                if (in_array('Khác', $lydoArray) && !empty($lydoKhac)) {
                    $lydoText .= ' - ' . $lydoKhac;
                }
            }

            try {
                // Insert vào bảng yeucaudonhang
                $stmt = $pdo->prepare("INSERT INTO yeucaudonhang (idkh, iddh, lydo) VALUES (:idkh, :iddh, :lydo)");
                $stmt->execute([
                    ':idkh' => $idkh,
                    ':iddh' => $iddh,
                    ':lydo' => $lydoText
                ]);

                // Success message
                echo '<div class="text-center mb-6">';
                echo '<h3 class="text-2xl font-semibold text-green-600">Gửi yêu cầu hủy đơn thành công!</h3>';
                echo '</div>';

                // Lấy danh sách yêu cầu đã gửi
                $stmt = $pdo->prepare("SELECT idkh, lydo, iddh FROM yeucaudonhang WHERE idkh = :idkh ORDER BY thoigian DESC LIMIT 1");
                $stmt->execute([':idkh' => $_SESSION['user']['iduser']]);
                $yeucauList = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display the list of cancellation requests
                echo '<h4 class="text-lg font-medium text-gray-700 mb-4">Danh sách yêu cầu đã gửi:</h4>';
                echo '<div class="overflow-x-auto">';
                echo '<table class="w-full border-collapse bg-white rounded-lg shadow">';
                echo '<thead>';
                echo '<tr class="bg-gray-200">';
                echo '<th class="px-6 py-3 text-left text-sm font-medium text-gray-700">ID Khách</th>';
                echo '<th class="px-6 py-3 text-left text-sm font-medium text-gray-700">ID Đơn</th>';
                echo '<th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Lý do</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($yeucauList as $row) {
                    echo '<tr class="border-t">';
                    echo '<td class="px-6 py-4 text-gray-600">' . htmlspecialchars($row['idkh']) . '</td>';
                    echo '<td class="px-6 py-4 text-gray-600">' . htmlspecialchars($row['iddh']) . '</td>';
                    echo '<td class="px-6 py-4 text-gray-600">' . htmlspecialchars($row['lydo']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                // Back button
                echo '<div class="mt-6 flex justify-center">';
                echo '<button onclick="window.top.location.href=\'../trangchu.php\'" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">';
                echo 'Trở về';
                echo '</button>';
                echo '</div>';
            } catch (PDOException $e) {
                echo '<div class="text-center text-red-600">';
                echo '<p class="text-lg font-medium">Lỗi: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="text-center text-red-600">';
            echo '<p class="text-lg font-medium">Phương thức gửi không hợp lệ.</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>