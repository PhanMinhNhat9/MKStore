<?php
require_once '../config.php';

// Kết nối CSDL
try {
    $pdo = connectDatabase();
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Truy vấn danh sách danh mục
try {
    $sql = "SELECT iddm, tendm, loaidm, icon, mota, thoigian FROM danhmucsp ORDER BY loaidm, iddm";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn CSDL: " . $e->getMessage());
}

// Xây dựng cây danh mục
$categoryTree = [];
foreach ($categories as $cat) {
    if ($cat['loaidm'] == 0) {
        $categoryTree[$cat['iddm']] = $cat + ['children' => []];
    } else {
        if (isset($categoryTree[$cat['loaidm']])) {
            $categoryTree[$cat['loaidm']]['children'][] = $cat;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Danh Mục - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="hienthidanhmuc.css?v=<?= time(); ?>">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
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
        <form class="filter-form">
            <label for="parentFilter">Lọc theo danh mục cha:</label>
            <select id="parentFilter" name="parentFilter">
                <option value="all">Tất cả</option>
                <?php foreach ($categoryTree as $parent): ?>
                    <option value="<?= htmlspecialchars($parent['iddm']) ?>">
                        <?= htmlspecialchars($parent['tendm']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <button class="btn btn-add" onclick="themdmcha(0)" aria-label="Thêm danh mục cha">
            <i class="fas fa-plus-circle"></i> Thêm Danh Mục Cha
        </button>
    </aside>

    <!-- Main Content -->
    <main class="container">
        <section class="table-container">
            <table class="category-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tên Danh Mục</th>
                        <th scope="col" style="width: 5%">Icon</th>
                        <th scope="col" style="width: 50%">Mô Tả</th>
                        <th scope="col">Thời Gian</th>
                        <th scope="col">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categoryTree)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Không có danh mục nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categoryTree as $parent): ?>
                            <tr class="parent" data-parent-id="<?= htmlspecialchars($parent['iddm']) ?>">
                                <td><?= htmlspecialchars($parent['iddm']) ?></td>
                                <td><?= htmlspecialchars($parent['tendm']) ?></td>
                                <td>
                                    <img 
                                        src="../<?= htmlspecialchars($parent['icon']) ?>" 
                                        class="icon" 
                                        alt="Icon danh mục <?= htmlspecialchars($parent['tendm']) ?>"
                                        loading="lazy"
                                        onerror="this.src='../images/default-icon.png'"
                                    >
                                </td>
                                <td><?= htmlspecialchars($parent['mota']) ?></td>
                                <td><?= htmlspecialchars($parent['thoigian']) ?></td>
                                <td>
                                    <button 
                                        class="btn btn-update" 
                                        onclick="themdmcon(<?= htmlspecialchars($parent['iddm']) ?>)"
                                        aria-label="Thêm danh mục con"
                                        title="Thêm con"
                                    >
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                    <button 
                                        class="btn btn-edit" 
                                        onclick="capnhatdanhmuc(<?= htmlspecialchars($parent['iddm']) ?>)"
                                        aria-label="Cập nhật danh mục"
                                        title="Sửa"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php foreach ($parent['children'] as $child): ?>
                                <tr class="child" data-parent-id="<?= htmlspecialchars($parent['iddm']) ?>">
                                    <td><?= htmlspecialchars($child['iddm']) ?></td>
                                    <td>
                                        <span class="child-indent">↳</span> 
                                        <?= htmlspecialchars($child['tendm']) ?>
                                    </td>
                                    <td>
                                        <img 
                                            src="../<?= htmlspecialchars($child['icon']) ?>" 
                                            class="icon" 
                                            alt="Icon danh mục con <?= htmlspecialchars($child['tendm']) ?>"
                                            loading="lazy"
                                            onerror="this.src='../images/default-icon.png'"
                                        >
                                    </td>
                                    <td><?= htmlspecialchars($child['mota']) ?></td>
                                    <td><?= htmlspecialchars($child['thoigian']) ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- JavaScript -->
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                const isExpanded = !sidebar.classList.contains('collapsed');
                const hamburger = document.querySelector('.hamburger');
                if (hamburger) {
                    hamburger.setAttribute('aria-expanded', isExpanded);
                }
            }
        }

        // Filter by Parent Category
        document.addEventListener('DOMContentLoaded', () => {
            const parentFilter = document.getElementById('parentFilter');
            if (parentFilter) {
                parentFilter.addEventListener('change', function () {
                    const selectedParentId = this.value;
                    const rows = document.querySelectorAll('.category-table tbody tr');
                    rows.forEach(row => {
                        const rowParentId = row.getAttribute('data-parent-id');
                        row.style.display = selectedParentId === 'all' || rowParentId === selectedParentId ? '' : 'none';
                    });
                });
            }
        });

        // Kiểm tra hướng màn hình
        window.addEventListener('resize', () => {
            if (window.innerHeight > window.innerWidth) {
                Swal.fire({
                    icon: 'info',
                    title: 'Xoay thiết bị',
                    text: 'Vui lòng xoay thiết bị sang chế độ ngang để xem danh mục tốt hơn.',
                });
            }
        });
    </script>
</body>
</html>