<?php
require_once '../config.php';
$pdo = connectDatabase();

// --- XỬ LÝ LỌC ---
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$quyen = isset($_GET['quyen']) ? trim($_GET['quyen']) : '';

// --- PHÂN TRANG ---
$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$params = [];
$conditions = [];

// Nếu là user thường, chỉ được xem chính mình
if ($_SESSION['user']['quyen'] == 1) {
    $conditions[] = "iduser = :iduser";
    $params['iduser'] = $_SESSION['user']['iduser'];
}

if ($query != '') {
    $conditions[] = "(email LIKE :searchTerm OR sdt LIKE :searchTerm1)";
    $params['searchTerm'] = "%{$query}%";
    $params['searchTerm1'] = "%{$query}%";
}
if ($quyen != '') {
    $conditions[] = "quyen = :quyen";
    $params['quyen'] = $quyen;
}
$iduser = $_SESSION['user']['iduser'];
$sql = "SELECT iduser, hoten, tendn, matkhau, anh, email, sdt, diachi, quyen, thoigian FROM user WHERE iduser <> '$iduser'";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
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

// --- ĐẾM TỔNG USER ---
$countSql = "SELECT COUNT(*) FROM user";
if (!empty($conditions)) {
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}
$countStmt = $pdo->prepare($countSql);
foreach ($params as $key => $val) {
    if ($key !== 'limit' && $key !== 'offset') {
        $countStmt->bindValue(":$key", $val);
    }
}
$countStmt->execute();
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

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
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthinguoidung.css?v=<?= time(); ?>">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
</head>
<body>
    <?php if ($_SESSION['user']['quyen'] !=1): ?>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-controls">
                <button class="hamburger" aria-label="Collapse Sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <h1>Hệ Thống Bán Hàng</h1>
            <form method="GET" class="filter-form" id="filter-form" action="">
                <label for="quyen">Lọc theo quyền:</label>
                <select name="quyen" id="quyen">
                    <option value="">-- Tất cả quyền --</option>
                    <option value="2589" <?= $quyen === "2589" ? "selected" : "" ?>>Quản trị</option>
                    <option value="0" <?= $quyen === "0" ? "selected" : "" ?>>Nhân viên</option>
                    <option value="1" <?= $quyen === "1" ? "selected" : "" ?>>Người dùng</option>
                </select>
            </form>
            <?php if ($_SESSION['user']['quyen'] == 2589 || $_SESSION['user']['quyen'] == 0) { ?>
                <button class="btn btn-add" onclick="themnguoidung()"><i class="fas fa-plus"></i> Thêm người dùng</button>
            <?php } ?>
            <?php if ($totalPages > 1): ?>
                <nav class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?query=<?= urlencode($query) ?>&quyen=<?= $quyen ?>&page=<?= $page - 1 ?>" class="btn btn-nav">← Trước</a>
                    <?php endif; ?>
                    <span>Trang <?= $page ?> / <?= $totalPages ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?query=<?= urlencode($query) ?>&quyen=<?= $quyen ?>&page=<?= $page + 1 ?>" class="btn btn-nav">Sau →</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </aside>
    <?php endif; ?>
 
 <?php if ($_SESSION['user']['quyen'] ==1): ?>
    <div class="profile-grid-wrapper"> 
        <main class="profile-container">
            <?php foreach ($users as $user): ?>
                <!-- Ảnh đại diện -->
                <section class="profile-section">
                    <h3>Ảnh đại diện</h3>
                    <div class="profile-avatar">
                        <img 
                            src="../<?= htmlspecialchars($user['anh']) ?>" 
                            alt="Ảnh đại diện" 
                        >
                    </div>

                    <!-- Input file để chọn ảnh mới -->
                    <div class="custom-file-upload">
                        <label for="anh_moi" 
                            onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)"
                        >
                            <i class="fas fa-image"></i> Chọn ảnh mới
                        </label>
                    </div>

                    <!-- Nút hành động -->
                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                        <button type="submit" name="capnhat_anh" class="KHbtn KHbtn-update">
                            <i class="fas fa-upload"></i> Cập nhật ảnh
                        </button>
                        <button type="submit" name="xoa_anh" class="KHbtn KHbtn-delete" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                            <i class="fas fa-trash-alt"></i> Xóa ảnh
                        </button>
                    </div>
                </section>
                <!-- Thông tin cá nhân -->
                <section class="profile-section">
                    <h3>Thông tin cá nhân             
                        <button class="edit-btn" onclick="capnhatnguoidung(<?= $user['iduser'] ?>)">
                            <i class="fas fa-pen"></i>
                        </button>
                    </h3>
                    <div class="profile-grid">
                        <div><label>ID User</label><input type="text" value="<?= htmlspecialchars($user['iduser']) ?>" disabled></div>
                        <div><label>Họ tên</label><input type="text" value="<?= htmlspecialchars($user['hoten']) ?>" disabled></div>
                        <div><label>Địa chỉ</label><input type="text" value="<?= htmlspecialchars($user['diachi']) ?>" disabled></div>
                        <div><label>Email</label><input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
                        <div><label>SDT</label><input type="text" value="<?= htmlspecialchars($user['sdt']) ?>" disabled></div>
                        <?php if ($ktstmt->rowCount() === 0): ?>
                        <div><label>Trạng thái</label><input type="text" value="Đang hoạt động" disabled></div>
                        <?php else: ?>
                            <div><label>Trạng thái</label><input type="text" value="Tạm ngừng hoạt động" disabled></div>
                            <?php endif; ?>
                    </div>
                </section>

                <!-- Thông tin lớp chuyên ngành -->
                <section class="profile-section">
                    <h3>Thông tin tài khoản</h3>
                    <div class="profile-grid">
                        <div><label>Tên đăng nhập:</label><input type="text" value="<?= htmlspecialchars($user['tendn']) ?>" disabled></div>
                        <div><label>Mật khẩu:</label><input type="password" value="<?=$user['matkhau']?>" disabled></div>
                    </div>
                </section>

            <?php endforeach; ?>
        </main>
    </div>
<?php endif; ?>

    <?php if ($_SESSION['user']['quyen'] !=1): ?>
    <!-- Main Content -->
    <main class="container">
        <section class="user-grid">
            <?php if (empty($users)): ?>
                <p class="no-users">Không tìm thấy người dùng.</p>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <article class="card">
                        <img 
                            src="../<?= htmlspecialchars($user['anh']) ?>" 
                            alt="Avatar của <?= htmlspecialchars($user['hoten']) ?>" 
                            loading="lazy"
                            onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)"
                        >
                        <h3><i class="fas fa-user"></i> <?= htmlspecialchars($user['hoten']) ?></h3>
                        <p><i class="fas fa-user-tag"></i> <?= htmlspecialchars($user['tendn']) ?></p>
                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($user['sdt']) ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($user['diachi']) ?></p>
                        <p><i class="fas fa-lock"></i> Quyền: <?= htmlspecialchars(
                            $user['quyen'] === '0' ? 'Nhân viên' :
                            ($user['quyen'] === '2589' ? 'Quản trị' : 'Người dùng')
                        ) ?></p>
                        <!-- Hiển thị nhãn nếu tài khoản đang lên lịch khóa -->
                        <?php if (isset($lockedUsers[$user['iduser']])): ?>
                            <p class="lock-label"><i class="fas fa-lock"></i> Đang lên lịch khóa</p>
                        <?php endif; ?>
                        <div class="btn-group">
                            <?php if ($_SESSION['user']['quyen'] == 2589 || $user['iduser'] == $_SESSION['user']['iduser']): ?>
                                <?php if (isset($lockedUsers[$user['iduser']])): ?>
                                    <!-- Nút Hoàn tất cho tài khoản đang lên lịch xóa -->
                                    <button 
                                        onclick="laylaiTK(<?= $user['iduser'] ?>)"
                                        class="btn btn-complete"
                                        aria-label="Hoàn tất xóa người dùng"
                                    >
                                        <i class="fas fa-check"></i> Cấp lại
                                    </button>
                                <?php else: ?>
                                    <!-- Nút Cập nhật và Xóa cho tài khoản bình thường -->
                                    <button 
                                        onclick="capnhatnguoidung(<?= $user['iduser'] ?>)"
                                        class="btn btn-update"
                                        aria-label="Cập nhật người dùng"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        onclick="xoanguoidung(<?= $user['iduser'] ?>)"
                                        class="btn btn-delete"
                                        aria-label="Xóa người dùng"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
    <?php endif; ?>

    <!-- Modal for Image Update -->
    <form action="#" method="POST" enctype="multipart/form-data" class="modal-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div id="imageModal" class="modal" aria-hidden="true">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()" aria-label="Đóng modal"><i class="fas fa-times"></i></span>
                <img id="modalImage" src="" alt="Ảnh người dùng">
                <input type="hidden" name="iduser" id="iduser" value="">
                <input 
                    type="file" 
                    id="fileInput" 
                    name="fileInput" 
                    accept="image/*" 
                    onchange="loadanh()"
                    aria-label="Chọn ảnh mới"
                >
                <button type="submit" name="capnhatanhuser" class="btn btn-update">Cập nhật ảnh</button>
            </div>
        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['capnhatanhuser'])) {
            $kq = capnhatAnhUser();
            if ($kq) {
                echo "
                <script>
                    window.top.location.href = '../trangchuadmin.php?status=cnanhuserT';
                </script>";
            } else {
                echo "
                <script>
                    window.top.location.href = '../trangchuadmin.php?status=cnanhuserF';
                </script>";
            }
        }
        ?>
    </form>

    <!-- JavaScript -->
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            const isExpanded = !sidebar.classList.contains('collapsed');
            document.querySelector('.hamburger').setAttribute('aria-expanded', isExpanded);
        }

        // Other functions
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

        // Hàm xử lý hoàn tất xóa tài khoản
        function laylaiTK(iduser) {
            if (confirm('Bạn có chắc chắn muốn hoàn tất xóa tài khoản này?')) {
                // Gửi yêu cầu AJAX để hoàn tất xóa
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
                        showCustomAlert('Thành công!', 'Tài khoản đã được xóa.', '../picture/success.png');
                        setTimeout(() => location.reload(), 3000);
                    } else {
                        showCustomAlert('Lỗi!', data.message, '../picture/error.png');
                    }
                })
                .catch(error => {
                    showCustomAlert('Lỗi!', 'Đã xảy ra lỗi khi xử lý.', '../picture/error.png');
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const quyenSelect = document.getElementById('quyen');
            if (quyenSelect) {
                quyenSelect.addEventListener('change', () => {
                    document.getElementById('filter-form').submit();
                });
            }
        });

    </script>
</body>
</html>