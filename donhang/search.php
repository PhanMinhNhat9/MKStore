<?php
require_once '../config.php'; // Đảm bảo đường dẫn đúng
$pdo = connectDatabase();      // Kết nối database

$query = isset($_POST['query']) ? trim($_POST['query']) : '';

if ($query != '') {
    $sql = "SELECT `idsp`, `tensp`, `mota`, `giaban`, `anh` FROM `sanpham` WHERE `idsp` LIKE :searchTerm";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['searchTerm' => "{$query}"]);
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='product'>";
            echo "<img src='" . htmlspecialchars($row["anh"]) . "' alt='" . htmlspecialchars($row["tensp"]) . "'>";
            echo "<h3>" . htmlspecialchars($row["tensp"]) . "</h3>";
            echo "<p>" . htmlspecialchars($row["mota"]) . "</p>";
            echo "<p>Giá: " . number_format($row["giaban"], 0, ',', '.') . " VND</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Không tìm thấy sản phẩm nào.</p>";
    }
}
?>
