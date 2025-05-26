<?php
require_once '../config.php';

// Kết nối CSDL
try {
    $pdo = connectDatabase();
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Kiểm tra phương thức POST và CSRF token
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $iddh = $_POST['iddh'] ?? '';
    $idkh = $_POST['idkh'] ?? '';
    $lydoArray = $_POST['lydo'] ?? [];
    $lydoKhac = $_POST['lydo_khac'] ?? '';

    // Gom tất cả lý do thành 1 chuỗi
    $lydoText = '';
    if (!empty($lydoArray)) {
        $lydoText = implode(', ', $lydoArray);
        if (in_array('Khác', $lydoArray) && !empty($lydoKhac)) {
            $lydoText .= ' - ' . $lydoKhac;
        }
    }

    try {
        // Kiểm tra trạng thái đơn hàng
        $stmt = $pdo->prepare("SELECT hientrang, trangthai FROM donhang WHERE iddh = :iddh AND sdt = :sdt");
        $stmt->execute([':iddh' => $iddh, ':sdt' => $_SESSION['user']['sdt']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception("Đơn hàng không tồn tại hoặc không thuộc về bạn!");
        }

        if (!in_array($order['hientrang'], ['Chờ xác nhận', 'Đã xác nhận', 'Đang đóng gói'])) {
            throw new Exception("Đơn hàng không thể hủy do trạng thái hiện tại là {$order['hientrang']}!");
        }

        if ($order['trangthai'] !== 'Chưa thanh toán') {
            throw new Exception("Đơn hàng đã thanh toán, không thể hủy!");
        }

        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Insert vào bảng yeucaudonhang
        $stmt = $pdo->prepare("INSERT INTO yeucaudonhang (idkh, iddh, lydo, trangthai) VALUES (:idkh, :iddh, :lydo, 'Đã hủy')");
        $stmt->execute([
            ':idkh' => $idkh,
            ':iddh' => $iddh,
            ':lydo' => $lydoText
        ]);

        // Cập nhật trạng thái đơn hàng thành Hủy đơn
        $stmt = $pdo->prepare("UPDATE donhang SET trangthai = 'Hủy đơn' WHERE iddh = :iddh");
        $stmt->execute([':iddh' => $iddh]);

        // Commit transaction
        $pdo->commit();
        echo "<script>
            
            window.top.location.href = '../trangchu.php';
        </script>";
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Hiển thị thông báo lỗi
        echo "<script>
            alert('Lỗi: " . addslashes($e->getMessage()) . "');
            window.top.location.href = '../trangchu.php';
        </script>";
    }
} else {
    // Hiển thị thông báo lỗi CSRF
    echo "<script>
        alert('Lỗi: Phương thức gửi không hợp lệ!');
        window.top.location.href = '../trangchu.php';
    </script>";
}
?>