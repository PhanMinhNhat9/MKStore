<?php
require_once '../config.php';

// Kết nối CSDL
try {
    $pdo = connectDatabase();
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Lấy ngày và giờ hiện tại
date_default_timezone_set('Asia/Ho_Chi_Minh');
$currentDate = date('Y-m-d H:i:s');

// Khởi tạo bộ lọc trạng thái
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Xây dựng truy vấn SQL với bộ lọc trạng thái
try {
    $sql = "SELECT mg.idmgg, mg.code, mg.phantram, mg.ngayhieuluc, mg.ngayketthuc, 
                mg.iddm, mg.thoigian, dm.tendm 
            FROM magiamgia mg
            LEFT JOIN danhmucsp dm ON mg.iddm = dm.iddm";

    $conditions = [];
    $params = [];

    // Áp dụng bộ lọc trạng thái
    if ($status !== 'all') {
        if ($status === 'active') {
            $conditions[] = "mg.ngayhieuluc <= :currentDate1 AND mg.ngayketthuc >= :currentDate2";
            $params[':currentDate1'] = $currentDate;
            $params[':currentDate2'] = $currentDate;
        } elseif ($status === 'expired') {
            $conditions[] = "mg.ngayketthuc < :currentDate";
            $params[':currentDate'] = $currentDate;
        } elseif ($status === 'pending') {
            $conditions[] = "mg.ngayhieuluc > :currentDate";
            $params[':currentDate'] = $currentDate;
        }
    }

    // Gắn điều kiện vào SQL
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn CSDL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Mã Giảm Giá - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f4f6f9;
            margin: 0;
            overflow-x: hidden;
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
            background-color: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            color: white;
            transition: width 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
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
            margin-left: 50px;
        }

        /* Table */
        .coupon-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .coupon-table th {
            background-color: #e9ecef;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .coupon-table td, .coupon-table th {
            padding: 0.75rem;
        }

        /* Mobile View */
        .coupon-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            background-color: white;
            padding: 0.75rem;
        }

        .mobile-header {
            position: sticky;
            top: 0;
            background-color: #ffffff;
            z-index: 10;
            padding: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .action-buttons .btn:hover {
            transform: scale(1.1);
        }

        .action-buttons .btn-primary {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .action-buttons .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .action-buttons .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .action-buttons .btn-danger:hover {
            background-color: #b02a37;
            border-color: #b02a37;
        }

        /* Scrollbar */
        .table-container::-webkit-scrollbar,
        .mobile-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-container::-webkit-scrollbar-thumb,
        .mobile-container::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-track,
        .mobile-container::-webkit-scrollbar-track {
            background-color: #f8f9fa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .coupon-table th, .coupon-table td {
                font-size: 0.8rem;
            }
            .coupon-card {
                font-size: 0.8rem;
            }
            .coupon-card .row {
                margin: 0;
            }
            .coupon-card .col-6 {
                padding: 0 5px;
            }
            .action-buttons .btn {
                width: 45%;
                margin: 5px;
            }
        }

        .btn-add {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.35);
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background-color: #1d4ed8;
            box-shadow: 0 5px 14px rgba(59, 130, 246, 0.5);
            transform: translateY(-1px);
        }

        .btn-add i {
            font-size: 15px;
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
        <form id="filter-form" class="filter-form px-3 d-none" method="GET" action="" aria-label="Filter coupons by status">
            <label for="statusFilter" class="form-label text-white small">Lọc theo trạng thái:</label>
            <select id="statusFilter" name="status" class="form-select form-select-sm text-black" aria-describedby="statusFilterDescription">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Còn hiệu lực</option>
                <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chưa hiệu lực</option>
            </select>
            <span id="statusFilterDescription" class="visually-hidden">Chọn trạng thái để lọc danh sách mã giảm giá</span>
        </form>
        <button class="btn btn-add btn-sm mx-3 mt-3 d-none" onclick="themmgg()" aria-label="Thêm mã giảm giá">
            <i class="fas fa-plus-circle me-1"></i> Thêm Mã Giảm Giá
        </button>
    </aside>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">
            <h2 class="h5 mb-3 fw-bold text-primary">Mã Giảm Giá</h2>
            <!-- Mobile View -->
            <div class="d-block d-md-none mobile-container overflow-auto" style="max-height: 600px;" aria-describedby="mobileCouponDescription">
                <div class="mobile-header">
                    <h3 class="h6 fw-bold text-dark mb-0">Danh Sách Mã Giảm Giá</h3>
                </div>
                <?php if (empty($coupons)): ?>
                    <div class="alert alert-info text-center m-2" role="alert">
                        Không có mã giảm giá nào phù hợp.
                    </div>
                <?php else: ?>
                    <?php foreach ($coupons as $coupon): ?>
                        <div class="coupon-card m-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold"><?= htmlspecialchars($coupon['code']) ?></span>
                                <div class="action-buttons">
                                    <button 
                                        onclick="capnhatmgg(<?= $coupon['idmgg'] ?>)"
                                        class="btn btn-primary btn-sm me-1"
                                        title="Cập nhật mã giảm giá"
                                        aria-label="Cập nhật mã giảm giá"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        onclick="xoamgg(<?= $coupon['idmgg'] ?>)"
                                        class="btn btn-danger btn-sm"
                                        title="Xóa mã giảm giá"
                                        aria-label="Xóa mã giảm giá"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <p class="mb-0 small"><strong>Giảm:</strong> <?= (int)$coupon['phantram'] ?>%</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0 small"><strong>Hiệu lực:</strong> <?= date('d-m-Y', strtotime($coupon['ngayhieuluc'])) ?></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0 small"><strong>Hết hạn:</strong> <?= date('d-m-Y', strtotime($coupon['ngayketthuc'])) ?></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0 small"><strong>Danh mục:</strong> <?= !empty($coupon['tendm']) ? htmlspecialchars($coupon['tendm']) : '<em>Không có</em>' ?></p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-0 small"><strong>Trạng thái:</strong> 
                                        <?php
                                        error_log("Debug: Coupon {$coupon['code']}, NgayHieuLuc: {$coupon['ngayhieuluc']}, NgayKetThuc: {$coupon['ngayketthuc']}, CurrentDate: $currentDate");
                                        if ($coupon['ngayhieuluc'] > $currentDate) {
                                            echo '<span class="text-warning">Chưa hiệu lực</span>';
                                        } elseif ($coupon['ngayketthuc'] < $currentDate) {
                                            echo '<span class="text-danger">Hết hạn</span>';
                                        } else {
                                            echo '<span class="text-success">Còn hiệu lực</span>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <span id="mobileCouponDescription" class="visually-hidden">Danh sách mã giảm giá hiển thị dưới dạng thẻ trên thiết bị di động, có thể cuộn dọc nếu danh sách dài.</span>
            </div>

            <!-- Desktop View -->
            <div class="table-container d-none d-md-block rounded overflow-hidden border bg-white" style="max-height: 600px;">
                <div class="table-responsive" style="max-height: 600px;">
                    <table class="coupon-table table mb-0" aria-describedby="couponTableDescription">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Mã</th>
                                <th scope="col">Giảm (%)</th>
                                <th scope="col">Hiệu lực</th>
                                <th scope="col">Hết hạn</th>
                                <th scope="col">Danh mục</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($coupons)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Không có mã giảm giá nào phù hợp.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($coupons as $coupon): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                                        <td><?= (int)$coupon['phantram'] ?>%</td>
                                        <td><?= date('d-m-Y', strtotime($coupon['ngayhieuluc'])) ?></td>
                                        <td><?= date('d-m-Y', strtotime($coupon['ngayketthuc'])) ?></td>
                                        <td><?= !empty($coupon['tendm']) ? htmlspecialchars($coupon['tendm']) : '<em>Không có</em>' ?></td>
                                        <td>
                                            <?php
                                            error_log("Debug: Coupon {$coupon['code']}, NgayHieuLuc: {$coupon['ngayhieuluc']}, NgayKetThuc: {$coupon['ngayketthuc']}, CurrentDate: $currentDate");
                                            if ($coupon['ngayhieuluc'] > $currentDate) {
                                                echo '<span class="text-warning">Chưa hiệu lực</span>';
                                            } elseif ($coupon['ngayketthuc'] < $currentDate) {
                                                echo '<span class="text-danger">Hết hạn</span>';
                                            } else {
                                                echo '<span class="text-success">Còn hiệu lực</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button 
                                                    onclick="capnhatmgg(<?= $coupon['idmgg'] ?>)"
                                                    class="btn btn-primary btn-sm me-1"
                                                    title="Cập nhật mã giảm giá"
                                                    aria-label="Cập nhật mã giảm giá"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button 
                                                    onclick="xoamgg(<?= $coupon['idmgg'] ?>)"
                                                    class="btn btn-danger btn-sm"
                                                    title="Xóa mã giảm giá"
                                                    aria-label="Xóa mã giảm giá"
                                                >
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <span id="couponTableDescription" class="visually-hidden">Bảng hiển thị danh sách mã giảm giá...</span>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            document.getElementById('statusFilter').addEventListener('change', () => {
                filterForm.submit();
            });
        }

        function themmgg() {
            Swal.fire({
                title: 'Thêm mã giảm giá',
                html: 'Chuyển hướng đến trang thêm mã giảm giá...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = 'themmgg.php';
            });
        }

        function capnhatmgg(id) {
            Swal.fire({
                title: 'Cập nhật mã giảm giá',
                html: 'Chuyển hướng đến trang cập nhật mã giảm giá...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = `capnhatmgg.php?id=${id}`;
            });
        }

        function xoamgg(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Mã giảm giá này sẽ bị xóa vĩnh viễn!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `xoamgg.php?id=${id}`;
                }
            });
        }
    </script>
</body>
</html>