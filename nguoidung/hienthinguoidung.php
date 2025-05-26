<?php
require_once '../config.php';
$pdo = connectDatabase();

// --- XỬ LÝ LỌC ---
$quyen = isset($_GET['quyen']) ? trim($_GET['quyen']) : '';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$params = [];
$conditions = [];

// Nếu là user thường, chỉ được xem chính mình
if ($_SESSION['user']['quyen'] == 1) {
    $conditions[] = "iduser = :iduser";
    $params['iduser'] = $_SESSION['user']['iduser'];
}

if ($quyen != '') {
    $conditions[] = "quyen = :quyen";
    $params['quyen'] = $quyen;
}

if ($query != '') {
    $conditions[] = "(hoten LIKE :searchTerm OR sdt LIKE :searchTerm1)";
    $params['searchTerm'] = "%{$query}%";
    $params['searchTerm1'] = "%{$query}%";
}

$iduser = $_SESSION['user']['iduser'];
$sql = "SELECT iduser, hoten, tendn, matkhau, anh, email, sdt, diachi, quyen, thoigian FROM user";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- KIỂM TRA TRẠNG THÁI KHÓA TÀI KHOẢN ---
$lockedUsers = [];
$lockStmt = $pdo->prepare("SELECT iduser FROM khxoatk WHERE trangthai = 1");
$lockStmt->execute();
$lockedUsersResult = $lockStmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($lockedUsersResult as $lockedUser) {
    $lockedUsers[$lockedUser['iduser']] = true;
}

$sql = "SELECT iduser FROM khxoatk WHERE iduser = :iduser";
$ktstmt = $pdo->prepare($sql);
$ktstmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
$ktstmt->execute();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Người Dùng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../script.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .table-container {
            overflow-x: auto;
            height: 320px;
            overflow-y: auto;
        }
        .table th, .table td {
            transition: background-color 0.2s ease;
        }
        .table tr:hover {
            background-color: #f1f5f9;
        }
        .btn {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .control-bar {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }
        .container {
            margin-top:0;
        }
        .modal {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php if ($_SESSION['user']['quyen'] != 1): ?>
        <!-- Main Content: Control Bar and Table Layout -->
        <main class="container max-w-6xl mx-auto mt-8 p-6">
            <!-- Control Bar -->
            <div class="control-bar mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- <h1 class="text-2xl font-bold text-gray-800">Danh Sách Người Dùng</h1> -->
                <form method="GET" class="filter-form flex flex-col md:flex-row gap-4" id="filter-form" action="">
                    <div>
                        <select name="quyen" id="quyen" class="w-full md:w-48 p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Tất cả quyền --</option>
                            <option value="2589" <?= $quyen === "2589" ? "selected" : "" ?>>Quản trị</option>
                            <option value="0" <?= $quyen === "0" ? "selected" : "" ?>>Nhân viên</option>
                            <option value="1" <?= $quyen === "1" ? "selected" : "" ?>>Người dùng</option>
                        </select>
                    </div>
                    <div>
                    <input type="text" name="query" id="query" value="<?= htmlspecialchars($query) ?>" placeholder="Tên hoặc số điện thoại" 
                        class="w-full md:w-64 p-2 border rounded-md focus:ring-2 focus:ring-blue-500" style="outline: none;">                    </div>
                    <button type="submit" class="btn bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 self-end">
                        <i class="fas fa-search mr-2"></i> Tìm Kiếm 
                    </button>
                </form>
                <div class="flex flex-col md:flex-row gap-4">
                    <?php if ($_SESSION['user']['quyen'] == 2589 || $_SESSION['user']['quyen'] == 0) { ?>
                        <button class="btn bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700" onclick="themnguoidung()">
                            <i class="fas fa-plus mr-2"></i> Thêm người dùng
                        </button>
                    <?php } ?>
                </div>
            </div>
            <!-- Table -->
            <section class="table-container">
                <?php if (empty($users)): ?>
                    <p class="text-center text-gray-500 text-lg">Không tìm thấy người dùng.</p>
                <?php else: ?>
                    <table class="table w-full bg-white shadow-lg rounded-lg">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th class="py-3 px-4 text-left">Avatar</th>
                                <th class="py-3 px-4 text-left">Họ tên</th>
                                <th class="py-3 px-4 text-left">Tên đăng nhập</th>
                                <th class="py-3 px-4 text-left">Email</th>
                                <th class="py-3 px-4 text-left">SĐT</th>
                                <th class="py-3 px-4 text-left">Địa chỉ</th>
                                <th class="py-3 px-4 text-left">Quyền</th>
                                <th class="py-3 px-4 text-left">Trạng thái</th>
                                <th class="py-3 px-4 text-left">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4">
                                        <img 
                                            src="../<?= htmlspecialchars($user['anh']) ?>" 
                                            alt="Avatar của <?= htmlspecialchars($user['hoten']) ?>" 
                                            class="w-10 h-10 rounded-full object-cover cursor-pointer"
                                            onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)"
                                        >
                                    </td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($user['hoten']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($user['tendn']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($user['sdt']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($user['diachi']) ?></td>
                                    <td class="py-3 px-4">
                                        <?= htmlspecialchars(
                                            $user['quyen'] === '0' ? 'Nhân viên' :
                                            ($user['quyen'] === '2589' ? 'Quản trị' : 'Người dùng')
                                        ) ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if (isset($lockedUsers[$user['iduser']])): ?>
                                            <span class="text-red-600"><i class="fas fa-lock mr-1"></i>Đang khóa</span>
                                        <?php else: ?>
                                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 flex gap-2">
                                        <?php if ($_SESSION['user']['quyen'] == 2589 || $user['iduser'] == $_SESSION['user']['iduser']): ?>
                                            <?php if (isset($lockedUsers[$user['iduser']])): ?>
                                                <button 
                                                    onclick="laylaiTK(<?= $user['iduser'] ?>)"
                                                    class="btn bg-green-600 text-white py-1 px-2 rounded-md hover:bg-green-700"
                                                    aria-label="Cấp lại tài khoản"
                                                >
                                                    <i class="fas fa-check"></i> Cấp lại TK
                                                </button>
                                            <?php else: ?>
                                                <button 
                                                    onclick="capnhatnguoidung(<?= $user['iduser'] ?>)"
                                                    class="btn bg-blue-600 text-white py-1 px-2 rounded-md hover:bg-blue-700"
                                                    aria-label="Cập nhật người dùng"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button 
                                                    onclick="xoanguoidung(<?= $user['iduser'] ?>)"
                                                    class="btn bg-red-600 text-white py-1 px-2 rounded-md hover:bg-red-700"
                                                    aria-label="Xóa người dùng"
                                                >
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif ($_SESSION['user']['quyen'] == 0): ?>
                                            <?php if ($user['quyen'] != 2589): ?>
                                                <button 
                                                    onclick="capnhatnguoidung(<?= $user['iduser'] ?>)"
                                                    class="btn bg-blue-600 text-white py-1 px-2 rounded-md hover:bg-blue-700"
                                                    aria-label="Cập nhật người dùng"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button 
                                                    onclick="xoanguoidung(<?= $user['iduser'] ?>)"
                                                    class="btn bg-red-600 text-white py-1 px-2 rounded-md hover:bg-red-700"
                                                    aria-label="Xóa người dùng"
                                                >
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </main>
    <?php endif; ?>

    <?php if ($_SESSION['user']['quyen'] == 1): ?>
        <div class="profile-grid-wrapper max-w-4xl mx-auto mt-8 p-6">
            <main class="profile-container bg-white shadow-lg rounded-lg p-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Hồ Sơ Người Dùng</h1>
                <?php foreach ($users as $user): ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Ảnh đại diện -->
                        <section class="profile-section">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ảnh đại diện</h3>
                            <div class="profile-avatar flex justify-center">
                                <img 
                                    src="../<?= htmlspecialchars($user['anh']) ?>" 
                                    alt="Ảnh đại diện" 
                                    class="w-32 h-32 rounded-full object-cover border-2 border-gray-200"
                                >
                            </div>
                            <div class="mt-4 text-center">
                                <label 
                                    onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)"
                                    class="cursor-pointer text-blue-600 hover:underline"
                                >
                                    <i class="fas fa-image mr-2"></i> Chọn ảnh mới
                                </label>
                            </div>
                        </section>
                        <!-- Thông tin cá nhân -->
                        <section class="profile-section col-span-2">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                Thông tin cá nhân
                                <button class="ml-auto text-blue-600 hover:text-blue-800" onclick="capnhatnguoidung(<?= $user['iduser'] ?>)">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">ID User</label>
                                    <input type="text" value="<?= htmlspecialchars($user['iduser']) ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Họ tên</label>
                                    <input type="text" value="<?= htmlspecialchars($user['hoten']) ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Địa chỉ</label>
                                    <input type="text" value="<?= htmlspecialchars($user['diachi']) ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Email</label>
                                    <input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">SĐT</label>
                                    <input type="text" value="<?= htmlspecialchars($user['sdt']) ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Trạng thái</label>
                                    <input type="text" value="<?= $ktstmt->rowCount() === 0 ? 'Đang hoạt động' : 'Tạm ngừng hoạt động' ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- Thông tin tài khoản -->
                    <section class="profile-section mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông tin tài khoản</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Tên đăng nhập</label>
                                <input type="text" value="<?= htmlspecialchars($user['tendn']) ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Mật khẩu</label>
                                <input type="password" value="<?= $user['matkhau'] ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
                            </div>
                        </div>
                    </section>
                <?php endforeach; ?>
            </main>
        </div>
    <?php endif; ?>

    <!-- Modal for Image Update -->
    <form action="#" method="POST" enctype="multipart/form-data" class="modal-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div id="imageModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" aria-hidden="true">
            <div class="modal-content bg-white rounded-lg p-6 w-full max-w-md">
                <span class="close-btn float-right text-gray-600 hover:text-gray-800 cursor-pointer" onclick="closeModal()" aria-label="Đóng modal"><i class="fas fa-times text-xl"></i></span>
                <img id="modalImage" src="" alt="Ảnh người dùng" class="w-full h-64 object-cover rounded-md mb-4">
                <input type="hidden" name="iduser" id="iduser" value="">
                <input 
                    type="file" 
                    id="fileInput" 
                    name="fileInput" 
                    accept="image/*" 
                    onchange="loadanh()"
                    class="w-full p-2 border rounded-md mb-4"
                    aria-label="Chọn ảnh mới"
                >
                <button type="submit" name="capnhatanhuser" class="btn w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Cập nhật ảnh</button>
            </div>
        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['capnhatanhuser'])) {
            $kq = capnhatAnhUser();
            if ($kq) {
                echo "<script>window.top.location.href = '../trangchu.php?status=cnanhuserT';</script>";
            } else {
                echo "<script>window.top.location.href = '../trangchu.php?status=cnanhuserF';</script>";
            }
        }
        ?>
    </form>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const quyenSelect = document.getElementById('quyen');
            if (quyenSelect) {
                quyenSelect.addEventListener('change', () => {
                    document.getElementById('filter-form').submit();
                });
            }
        });

        function themnguoidung() {
            window.location.href = "themnguoidung.php";
        }

        function openModal(src, id) {
            const modal = document.getElementById('imageModal');
            const imageInput = document.getElementById('fileInput');
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            document.getElementById('modalImage').src = src;
            document.getElementById('iduser').value = id;
            modal.querySelector('.modal-content').focus();
            const currentUserId = <?= $_SESSION['user']['iduser'] ?>;
            const currentUserQuyen = <?= $_SESSION['user']['quyen'] ?>;
            imageInput.disabled = (currentUserQuyen != 2589 && id != currentUserId);
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }

        function loadanh() {
            const fileInput = document.getElementById('fileInput');
            const modalImage = document.getElementById('modalImage');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    modalImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function laylaiTK(iduser) {
            if (confirm('Bạn có chắc chắn muốn cấp lại tài khoản này?')) {
                fetch('laylaitk.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ iduser: iduser })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.top.location.href = '../trangchu.php?status=laylaispT';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi khi xử lý.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        }

        function capnhatnguoidung(iduser) {
            window.location.href = `capnhatnguoidung.php?iduser=${iduser}`;
        }
    </script>
</body>
</html>