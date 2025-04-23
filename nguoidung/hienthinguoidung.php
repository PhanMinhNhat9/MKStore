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

if ($query != '') {
    $conditions[] = "(email LIKE :searchTerm OR sdt LIKE :searchTerm1)";
    $params['searchTerm'] = "%{$query}%";
    $params['searchTerm1'] = "%{$query}%";
}
if ($quyen != '') {
    $conditions[] = "quyen = :quyen";
    $params['quyen'] = $quyen;
}

$sql = "SELECT iduser, hoten, tendn, anh, email, sdt, diachi, quyen, thoigian FROM user";
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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Người Dùng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="hienthinguoidung.css?v=<?= time(); ?>">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-controls">
            <button class="hamburger" aria-label="Collapse Sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <h1>Hệ Thống Bán Hàng</h1>
        <form method="GET" class="filter-form">
            <label for="quyen">Lọc theo quyền:</label>
            <select name="quyen" id="quyen">
                <option value="">-- Tất cả quyền --</option>
                <option value="0" <?= $quyen === "0" ? "selected" : "" ?>>Admin</option>
                <option value="1" <?= $quyen === "1" ? "selected" : "" ?>>User</option>
            </select>
            <button type="submit" class="btn btn-filter"><i class="fas fa-filter"></i> Lọc</button>
        </form>
        <?php if ($_SESSION['user']['quyen'] == 2589) { ?>
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
                        <p><i class="fas fa-lock"></i> Quyền: <?= htmlspecialchars($user['quyen'] === '0' ? 'Admin' : 'User') ?></p>
                        <div class="btn-group">
                            <?php if ($_SESSION['user']['quyen'] == 2589 || $user['iduser'] == $_SESSION['user']['iduser']): ?>
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
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <!-- Modal for Image Update -->
    <form action="#" method="POST" enctype="multipart/form-data" class="modal-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div id="imageModal" class="modal" aria-hidden="true">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()" aria-label="Đóng modal">×</span>
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
                echo "<script>showCustomAlert('🐳 Đổi Avt Thành Công!', 'Ảnh người dùng đã được đổi!', '../picture/success.png'); setTimeout(goBack, 3000);</script>";
            } else {
                echo "<script>showCustomAlert('🐳 Cài Đặt Avt Thất Bại!', '$kq', '../picture/error.png');</script>";
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

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.getElementById('imageModal').style.display === 'flex') {
                closeModal();
            }
        });
    </script>
</body>
</html>
