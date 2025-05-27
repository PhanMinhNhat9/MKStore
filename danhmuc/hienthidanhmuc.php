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

// Lọc theo danh mục cha và tìm kiếm
$parentFilter = isset($_GET['parentFilter']) ? $_GET['parentFilter'] : 'all';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Debug: Ghi log để kiểm tra
error_log("GET parentFilter: " . ($_GET['parentFilter'] ?? 'not set') . ", parentFilter: $parentFilter, categoryTree keys: " . implode(", ", array_keys($categoryTree)));

// Kiểm tra giá trị parentFilter hợp lệ
if ($parentFilter !== 'all' && !array_key_exists($parentFilter, $categoryTree)) {
    error_log("Invalid parentFilter: $parentFilter, resetting to 'all'");
    $parentFilter = 'all';
}

$filteredTree = $categoryTree;

if ($parentFilter !== 'all') {
    $filteredTree = [];
    if (isset($categoryTree[$parentFilter])) {
        $filteredTree[$parentFilter] = $categoryTree[$parentFilter];
    }
}

if ($searchQuery !== '') {
    $tempTree = [];
    foreach ($filteredTree as $iddm => $parent) {
        if (stripos($parent['tendm'], $searchQuery) !== false) {
            $tempTree[$iddm] = $parent;
        } else {
            $filteredChildren = [];
            foreach ($parent['children'] as $child) {
                if (stripos($child['tendm'], $searchQuery) !== false) {
                    $filteredChildren[] = $child;
                }
            }
            if (!empty($filteredChildren)) {
                $tempTree[$iddm] = $parent;
                $tempTree[$iddm]['children'] = $filteredChildren;
            }
        }
    }
    $filteredTree = $tempTree;
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        header {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            flex: 1;
            justify-content: flex-end;
        }

        .custom-select {
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            background-color: white;
            font-size: 0.9rem;
            max-width: 200px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .custom-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            outline: none;
        }

        .search-input {
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            font-size: 0.9rem;
            max-width: 200px;
        }

        .search-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            outline: none;
        }

        .btn-search, .btn-add, .btn-clear {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s ease;
        }

        .btn-search:hover, .btn-add:hover, .btn-clear:hover {
            background-color: #059669;
        }

        main {
            padding: 1rem;
        }

        .category-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            margin-bottom: 1rem;
        }

        .category-card-header {
            background-color: #f1f5f9;
            padding: 0.75rem;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-card-body {
            padding: 0.75rem;
        }

        .child-category {
            margin-left: 1rem;
            border-left: 2px solid #d1d5db;
            padding-left: 0.5rem;
            margin-top: 0.5rem;
        }

        .icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
            border-radius: 4px;
        }

        .action-button {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: none;
            background: transparent;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .action-button:hover {
            background-color: #e5e7eb;
            transform: translateY(-1px);
        }

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
            z-index: 1000;
        }

        .alert {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 0.75rem;
            border-radius: 4px;
            z-index: 1000;
            display: none;
        }

        .alert-success {
            background: #16a34a;
            color: white;
        }

        .alert-error {
            background: #dc2626;
            color: white;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                flex-direction: column;
                align-items: stretch;
                justify-content: center;
            }

            .custom-select, .search-input {
                max-width: none;
                width: 100%;
            }

            .btn-search, .btn-add, .btn-clear {
                width: 100%;
                justify-content: center;
            }

            .category-card-header {
                font-size: 0.85rem;
            }

            .category-card-body {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Alerts -->
    <div id="success-alert" class="alert alert-success"></div>
    <div id="error-alert" class="alert alert-error"></div>

    <!-- Header -->
    <header>
        <div class="container-fluid">
            <div class="header-content">
                <h1 class="header-title">Hệ Thống Bán Hàng</h1>
                <form id="filter-form" class="filter-group" method="GET" action="" aria-label="Lọc danh mục">
                    <input 
                        type="text" 
                        class="search-input" 
                        name="search" 
                        placeholder="Tìm kiếm danh mục..." 
                        value="<?= htmlspecialchars($searchQuery) ?>" 
                        aria-label="Tìm kiếm danh mục"
                    >
                    <button type="submit" class="btn btn-search" aria-label="Tìm kiếm">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <select id="parentFilter" name="parentFilter" class="custom-select" aria-describedby="parentFilterDescription">
                        <option value="all" <?= $parentFilter === 'all' ? 'selected' : '' ?>>Tất cả danh mục cha</option>
                        <?php foreach ($categoryTree as $parent): ?>
                            <option value="<?= htmlspecialchars($parent['iddm']) ?>" <?= $parentFilter === (string)$parent['iddm'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($parent['tendm']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-add" onclick="themdmcha(0)" aria-label="Thêm danh mục cha">
                        <i class="fas fa-plus-circle"></i> Thêm Danh Mục Cha
                    </button>
                    <button type="button" class="btn btn-clear" onclick="clearFilters()" aria-label="Xóa bộ lọc">
                        <i class="fas fa-times-circle"></i> Xóa Bộ Lọc
                    </button>
                    <span id="parentFilterDescription" class="visually-hidden">Chọn danh mục cha để lọc danh sách danh mục</span>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container-fluid">
            <h2 class="h5 mb-3 fw-bold text-primary">Danh Mục Sản Phẩm</h2>
            <?php if (empty($filteredTree)): ?>
                <div class="alert alert-info text-center" role="alert">
                    Không có danh mục nào phù hợp.
                </div>
            <?php else: ?>
                <?php foreach ($filteredTree as $parent): ?>
                    <div class="category-card">
                        <div class="category-card-header" data-bs-toggle="collapse" data-bs-target="#parent-<?= htmlspecialchars($parent['iddm']) ?>">
                            <span class="fw-bold"><?= htmlspecialchars($parent['tendm']) ?> (ID: <?= htmlspecialchars($parent['iddm']) ?>)</span>
                            <div>
                                <button 
                                    onclick="event.stopPropagation(); themdmcon(<?= htmlspecialchars($parent['iddm']) ?>)"
                                    class="action-button text-success tooltip-custom me-2"
                                    data-tooltip="Thêm danh mục con"
                                    aria-label="Thêm danh mục con cho <?= htmlspecialchars($parent['tendm']) ?>"
                                >
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button 
                                    onclick="event.stopPropagation(); capnhatdanhmuc(<?= htmlspecialchars($parent['iddm']) ?>)"
                                    class="action-button text-primary tooltip-custom"
                                    data-tooltip="Cập nhật danh mục"
                                    aria-label="Cập nhật danh mục <?= htmlspecialchars($parent['tendm']) ?>"
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
                                <div class="child-category">
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
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filter-form');
            const parentFilter = document.getElementById('parentFilter');
            const searchInput = document.querySelector('.search-input');

            // Debug: Log giá trị ban đầu của dropdown
            console.log('Initial parentFilter value:', parentFilter.value);

            if (filterForm && parentFilter) {
                // Submit form on parent filter change
                parentFilter.addEventListener('change', () => {
                    console.log('Selected parentFilter:', parentFilter.value);
                    filterForm.submit();
                });

                // Debounce search input
                let searchTimeout;
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        console.log('Search query:', searchInput.value);
                        filterForm.submit();
                    }, 500);
                });

                // Submit form on Enter key
                searchInput.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        clearTimeout(searchTimeout);
                        console.log('Search query:', searchInput.value);
                        filterForm.submit();
                    }
                });

                // Ép giá trị dropdown để đảm bảo đúng với PHP
                parentFilter.value = <?= json_encode($parentFilter) ?>;
                console.log('Forced parentFilter value:', parentFilter.value);
            }
        });

        function clearFilters() {
            Swal.fire({
                title: 'Xóa bộ lọc?',
                text: 'Bạn có chắc chắn muốn xóa tất cả bộ lọc?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = new URL(window.location);
                    url.searchParams.delete('parentFilter');
                    url.searchParams.delete('search');
                    window.location.href = url.toString();
                }
            });
        }

        function themdmcha(id) {
            Swal.fire({
                title: 'Thêm danh mục cha',
                html: 'Chuyển hướng đến trang thêm danh mục cha...',
                timer: 1000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = `themdm.php?id=${id}`;
            });
        }

        function themdmcon(id) {
            Swal.fire({
                title: 'Thêm danh mục con',
                html: 'Chuyển hướng đến trang thêm danh mục con...',
                timer: 1000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = `themdm.php?id=${id}`;
            });
        }

        function capnhatdanhmuc(id) {
            Swal.fire({
                title: 'Cập nhật danh mục',
                html: 'Chuyển hướng đến trang cập nhật danh mục...',
                timer: 1000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = `capnhatdanhmuc.php?id=${id}`;
            });
        }
    </script>
</body>
</html>