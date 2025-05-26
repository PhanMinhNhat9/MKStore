<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Order Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <?php 
    require_once '../config.php';
    $pdo = connectDatabase();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['iddh']) && isset($_POST['idkh'])) {
        $iddh = $_POST['iddh'];
        $idkh = $_POST['idkh'];
    }
    ?>
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Hủy Đơn Hàng</h3>
        <form id="formHuyDon" method="post" action="xuly_huydon.php" onsubmit="return xacNhanHuyDon()" class="space-y-6">
            <input type="hidden" name="iddh" value="<?= htmlspecialchars($iddh) ?>">
            <input type="hidden" name="idkh" value="<?= htmlspecialchars($idkh) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lý do hủy đơn</label>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="lydo[]" value="Đổi ý, không muốn mua nữa" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-gray-600">Đổi ý, không muốn mua nữa</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="lydo[]" value="Đặt nhầm sản phẩm" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-gray-600">Đặt nhầm sản phẩm</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="lydo[]" value="Thời gian giao hàng lâu" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-gray-600">Thời gian giao hàng lâu</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="lydo[]" value="Tìm được giá tốt hơn" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-gray-600">Tìm được giá tốt hơn</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="khacCheckbox" name="lydo[]" value="Khác" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" onchange="toggleLyDoKhac()">
                        <span class="ml-2 text-gray-600">Khác</span>
                    </label>
                </div>
            </div>

            <div id="lyDoKhacInput" class="hidden">
                <label for="lydo_khac" class="block text-sm font-medium text-gray-700 mb-1">Lý do khác</label>
                <input type="text" name="lydo_khac" id="lydo_khac" placeholder="Nhập lý do khác..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex justify-center">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">
                    Gửi yêu cầu hủy đơn
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleLyDoKhac() {
            const khacCheckbox = document.getElementById('khacCheckbox');
            const lyDoKhacInput = document.getElementById('lyDoKhacInput');
            lyDoKhacInput.classList.toggle('hidden', !khacCheckbox.checked);
        }

        function xacNhanHuyDon() {
            return confirm("Bạn có chắc chắn muốn hủy đơn hàng này?");
        }
    </script>
</body>
</html>