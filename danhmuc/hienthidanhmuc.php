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

// Lọc theo danh mục cha
$parentFilter = isset($_GET['parentFilter']) ? $_GET['parentFilter'] : 'all';
$filteredTree = $categoryTree;
if ($parentFilter !== 'all') {
    $filteredTree = [];
    if (isset($categoryTree[$parentFilter])) {
        $filteredTree[$parentFilter] = $categoryTree[$parentFilter];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f4f6f9;
        }

        /* Sidebar */
        #sidebar {
            height: 100vh;
            transition: width 0.3s ease;
            background-color: #2d3748;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: rgba(30, 41, 59, 0.4); /* Nền có độ trong suốt */
    backdrop-filter: blur(2px); /* Nền mờ */
    -webkit-backdrop-filter: blur(2px);
    color: white;
    transition: width 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); /* Đổ bóng bên phải */
        }

        .sidebar-expanded {
            width: 220px;
        }

        .sidebar-collapsed {
            width: 60px;
        }

        .sidebar-header {
            padding: 1rem;
            text-align: center;
        }

        .sidebar-header h1 {
            font-size: 1.2rem;
            margin: 0;
            transition: opacity 0.3s ease;
        }

        .hamburger {
            width: 24px;
            height: 18px;
            position: relative;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .hamburger span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: white;
            border-radius: 2px;
            opacity: 1;
            left: 0;
            transition: all 0.3s ease;
        }

        .hamburger span:nth-child(1) { top: 0px; }
        .hamburger span:nth-child(2) { top: 8px; }
        .hamburger span:nth-child(3) { top: 16px; }

        .hamburger.active span:nth-child(1) {
            top: 8px;
            transform: rotate(45deg);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
            left: -50px;
        }

        .hamburger.active span:nth-child(3) {
            top: 8px;
            transform: rotate(-45deg);
        }

        /* Main Content */
        main {
            transition: margin-left 0.3s ease;
            padding: 1rem;
        }
        main {
            margin-left: 50px;
        }
        .category-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            background-color: white;
        }

        .category-card-header {
            background-color: #e9ecef;
            padding: 0.75rem;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
        }

        .category-card-body {
            padding: 0.75rem;
        }

        .child-category {
            margin-left: 1rem;
            border-left: 2px solid #d1d5db;
            padding-left: 0.5rem;
        }

        .icon {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        /* Buttons */
        .action-button {
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: none;
            background: #ffffff;
        }

        .action-button:hover {
            background-color:rgb(255, 255, 255);
            transform: translateY(-1px);

        }

        /* Tooltip */
        .tooltip-custom {
            position: relative;
        }

        .tooltip-custom:hover:after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1e3a8a;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            z-index: 10;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .category-card-header {
                font-size: 0.85rem;
            }
            .category-card-body {
                font-size: 0.8rem;
            }
            main {
            margin-left: 50px;
        }
        }
        .btn-add {
    background-color: #3b82f6; /* xanh nổi bật */
    color: #ffffff;
    padding: 8px 14px; /* nhỏ hơn một chút */
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13.5px;
    box-shadow: 0 3px 10px rgba(59, 130, 246, 0.35); /* tinh chỉnh bóng */
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-add:hover {
    background-color: #1d4ed8;
    box-shadow: 0 5px 14px rgba(59, 130, 246, 0.5);
    transform: translateY(-1px); /* nhẹ nhàng nổi lên */
}

.btn-add i {
    font-size: 15px;
}
.custom-select-sm {
    padding: 8px 14px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
    color: #1e293b;
    font-size: 14px;
    font-weight: 500;
    appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%231e293b' height='16' width='16' xmlns='http://www.w3.org/2000/svg'><polygon points='0,0 16,0 8,10'/></svg>");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px;
    transition: border-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.custom-select-sm:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
    outline: none;
    background-color: #ffffff;
}


    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar-collapsed">
        <div class="sidebar-header">
            <button class="hamburger float-end" aria-label="Toggle Sidebar" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
                <span class="visually-hidden">Toggle Sidebar</span>
            </button>
            <h1 id="sidebar-title" class="d-none">Hệ Thống Bán Hàng</h1>
        </div>
        <form id="filter-form" class="filter-form px-3 d-none" method="GET" action="" aria-label="Filter categories by parent">
            <label for="parentFilter" class="form-label text-white small">Lọc danh mục cha:</label>
            <select id="parentFilter" name="parentFilter" class="form-select form-select-sm" aria-describedby="parentFilterDescription">
                <option value="all" <?= $parentFilter === 'all' ? 'selected' : '' ?>>Tất cả</option>
                <?php foreach ($categoryTree as $parent): ?>
                    <option value="<?= htmlspecialchars($parent['iddm']) ?>" <?= $parentFilter === $parent['iddm'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($parent['tendm']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="parentFilterDescription" class="visually-hidden">Chọn danh mục cha để lọc danh sách danh mục</span>
        </form>
        <button class="btn btn-add btn-sm btn-primary mx-3 mt-3 d-none" onclick="themdmcha(0)" aria-label="Thêm danh mục cha">
            <i class="fas fa-plus-circle me-1"></i> Thêm Danh Mục Cha
        </button>
    </aside>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">
            <h2 class="h5 mb-3 fw-bold text-primary">Danh Mục Sản Phẩm</h2>
            <?php if (empty($filteredTree)): ?>
                <div class="alert alert-info text-center" role="alert">
                    Không có danh mục nào phù hợp.
                </div>
            <?php else: ?>
                <?php foreach ($filteredTree as $parent): ?>
                    <div class="category-card">
                        <div class="category-card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#parent-<?= htmlspecialchars($parent['iddm']) ?>">
                            <span class="fw-bold"><?= htmlspecialchars($parent['tendm']) ?> (ID: <?= htmlspecialchars($parent['iddm']) ?>)</span>
                            <div>
                                <button 
                                    onclick="themdmcon(<?= htmlspecialchars($parent['iddm']) ?>)"
                                    class="action-button text-success tooltip-custom me-2"
                                    data-tooltip="Thêm danh mục con"
                                    aria-label="Thêm danh mục con"
                                >
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button 
                                    onclick="capnhatdanhmuc(<?= htmlspecialchars($parent['iddm']) ?>)"
                                    class="action-button text-primary tooltip-custom"
                                    data-tooltip="Cập nhật danh mục"
                                    aria-label="Cập nhật danh mục"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        <div id="parent-<?= htmlspecialchars($parent['iddm']) ?>" class="collapse show category-card-body">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <img 
                                        src="../<?= htmlspecialchars($parent['icon']) ?>" 
                                        class="icon" 
                                        alt="Icon danh mục <?= htmlspecialchars($parent['tendm']) ?>"
                                        loading="lazy"
                                        onerror="this.src='../images/default-icon.png'"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>Mô Tả:</strong> <?= htmlspecialchars($parent['mota']) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-0"><strong>Thời Gian:</strong> <?= htmlspecialchars($parent['thoigian']) ?></p>
                                </div>
                            </div>
                            <?php foreach ($parent['children'] as $child): ?>
                                <div class="child-category mt-2">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            <img 
                                                src="../<?= htmlspecialchars($child['icon']) ?>" 
                                                class="icon" 
                                                alt="Icon danh mục con <?= htmlspecialchars($child['tendm']) ?>"
                                                loading="lazy"
                                                onerror="this.src='../images/default-icon.png'"
                                            >
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong><?= htmlspecialchars($child['tendm']) ?> (ID: <?= htmlspecialchars($child['iddm']) ?>)</strong></p>
                                            <p class="mb-0 small">Mô Tả: <?= htmlspecialchars($child['mota']) ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0 small">Thời Gian: <?= htmlspecialchars($child['thoigian']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const title = document.getElementById('sidebar-title');
            const filterForm = document.getElementById('filter-form');
            const addButton = document.querySelector('.btn-add');
            const hamburger = document.querySelector('.hamburger');

            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
            const isExpanded = sidebar.classList.contains('sidebar-expanded');

            if (isExpanded) {
                title.classList.remove('d-none');
                filterForm.classList.remove('d-none');
                addButton.classList.remove('d-none');
                hamburger.classList.add('active');
            } else {
                title.classList.add('d-none');
                filterForm.classList.add('d-none');
                addButton.classList.add('d-none');
                hamburger.classList.remove('active');
            }

            hamburger.setAttribute('aria-expanded', isExpanded);
            localStorage.setItem('sidebarState', isExpanded ? 'expanded' : 'collapsed');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const title = document.getElementById('sidebar-title');
            const filterForm = document.getElementById('filter-form');
            const addButton = document.querySelector('.btn-add');
            const hamburger = document.querySelector('.hamburger');
            const savedState = localStorage.getItem('sidebarState');

            if (savedState === 'expanded') {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                title.classList.remove('d-none');
                filterForm.classList.remove('d-none');
                addButton.classList.remove('d-none');
                hamburger.classList.add('active');
            } else {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.classList.remove('sidebar-expanded');
                title.classList.add('d-none');
                filterForm.classList.add('d-none');
                addButton.classList.add('d-none');
                hamburger.classList.remove('active');
            }

            hamburger.setAttribute('aria-expanded', savedState === 'expanded');
        });

        document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.querySelector(".hamburger");
    const sidebar = document.getElementById("sidebar");

    hamburger.addEventListener("mouseenter", function () {
        if (sidebar.classList.contains("sidebar-collapsed")) {
            toggleSidebar();
        }
    });

    
    sidebar.addEventListener("mouseleave", function () {
        if (sidebar.classList.contains("sidebar-expanded")) {
            toggleSidebar();
        }
    });
});

        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            document.getElementById('parentFilter').addEventListener('change', () => {
                filterForm.submit();
            });
        }

        function themdmcha(id) {
            Swal.fire({
                title: 'Thêm danh mục cha',
                html: 'Chuyển hướng đến trang thêm danh mục cha...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = `themdm.php?id=${id}`;
            });
        }

        function themdmcon(id) {
            Swal.fire({
                title: 'Thêm danh mục con',
                html: 'Chuyển hướng đến trang thêm danh mục con...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = `themdm.php?id=${id}`;
            });
        }

        function capnhatdanhmuc(id) {
            Swal.fire({
                title: 'Cập nhật danh mục',
                html: 'Chuyển hướng đến trang cập nhật danh mục...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = `capnhatdanhmuc.php?id=${id}`;
            });
        }
    </script>
</body>
</html>