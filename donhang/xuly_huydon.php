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

        echo "<h3>Gửi yêu cầu hủy đơn thành công!</h3>";

        // Lấy danh sách yêu cầu đã gửi
        $stmt = $pdo->prepare("SELECT idkh, lydo, iddh FROM yeucaudonhang WHERE idkh = :idkh ORDER BY thoigian DESC LIMIT 1");
        $stmt->execute([':idkh' => $_SESSION['user']['iduser']]);
        $yeucauList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h4>Danh sách yêu cầu đã gửi:</h4>";
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr><th>ID Khách</th><th>ID Đơn</th><th>Lý do</th></tr>";
        foreach ($yeucauList as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['idkh']) . "</td>";
            echo "<td>" . htmlspecialchars($row['iddh']) . "</td>";
            echo "<td>" . htmlspecialchars($row['lydo']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    echo "Phương thức gửi không hợp lệ.";
}
?>
