<?php 
require_once '../config.php';
$pdo = connectDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['iddh']) && isset($_POST['idkh'])) {
    $iddh = $_POST['iddh'];
    $idkh = $_POST['idkh'];
}
?>

<!-- Form hủy đơn từ khách hàng -->
<form id="formHuyDon" method="post" action="xuly_huydon.php" onsubmit="return xacNhanHuyDon()">
    <input type="hidden" name="iddh" value="<?= htmlspecialchars($iddh) ?>">
    <input type="hidden" name="idkh" value="<?= htmlspecialchars($idkh) ?>">

    <h3>Lý do hủy đơn</h3>
    <div style="margin-bottom: 10px;">
        <label><input type="checkbox" name="lydo[]" value="Đổi ý, không muốn mua nữa"> Đổi ý, không muốn mua nữa</label><br>
        <label><input type="checkbox" name="lydo[]" value="Đặt nhầm sản phẩm"> Đặt nhầm sản phẩm</label><br>
        <label><input type="checkbox" name="lydo[]" value="Thời gian giao hàng lâu"> Thời gian giao hàng lâu</label><br>
        <label><input type="checkbox" name="lydo[]" value="Tìm được giá tốt hơn"> Tìm được giá tốt hơn</label><br>
        <label><input type="checkbox" id="khacCheckbox" name="lydo[]" value="Khác" onchange="toggleLyDoKhac()"> Khác</label>
    </div>

    <!-- Ô nhập lý do khác -->
    <div id="lyDoKhacInput" style="display: none; margin-bottom: 15px;">
        <input type="text" name="lydo_khac" placeholder="Nhập lý do khác..." style="width: 100%; padding: 8px;">
    </div>

    <button type="submit" style="padding: 8px 15px;">Gửi yêu cầu hủy đơn</button>
</form>

<script>
function toggleLyDoKhac() {
    const khacCheckbox = document.getElementById('khacCheckbox');
    const lyDoKhacInput = document.getElementById('lyDoKhacInput');
    lyDoKhacInput.style.display = khacCheckbox.checked ? "block" : "none";
}

function xacNhanHuyDon() {
    return confirm("Bạn có chắc chắn muốn hủy đơn hàng này?");
}
</script>
