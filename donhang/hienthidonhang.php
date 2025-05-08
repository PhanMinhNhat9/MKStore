<?php
require_once '../config.php';

// Kết nối CSDL
try {
    $pdo = connectDatabase();
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Lấy tham số tìm kiếm và trạng thái
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Xây dựng truy vấn SQL
try {
    $params = [];
    $sql = "SELECT iddh, sdt, tenkh, tongtien, trangthai, phuongthuctt, thoigian FROM donhang";
    $hasWhere = false;

    // Lọc theo quyền: nếu không phải admin thì giới hạn theo sdt
    if ($_SESSION['user']['quyen'] != 2589 && $_SESSION['user']['quyen'] != 0) {
        $sql .= " WHERE sdt = :sdt";
        $params['sdt'] = $_SESSION['user']['sdt'];
        $hasWhere = true;
    }

    // Tìm kiếm theo mã đơn hàng hoặc tên khách hàng
    if ($query !== '') {
        $condition = "(tenkh LIKE :searchTerm OR iddh LIKE :searchTerm1)";
        $sql .= $hasWhere ? " AND $condition" : " WHERE $condition";
        $params['searchTerm'] = "%$query%";
        $params['searchTerm1'] = "%$query%";
        $hasWhere = true;
    }

    // Lọc theo trạng thái
    if ($status !== 'all') {
        $sql .= $hasWhere ? " AND trangthai = :status" : " WHERE trangthai = :status";
        $params['status'] = $status;
    }

    $sql .= " ORDER BY thoigian DESC";

    // Chuẩn bị và thực thi truy vấn
    $stmtOrders = $pdo->prepare($sql);
    $stmtOrders->execute($params);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn CSDL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Hệ Thống Bán Hàng</title>
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
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 100;
    background-color: rgba(30, 41, 59, 0.4); /* Nền có độ trong suốt */
    backdrop-filter: blur(2px); /* Nền mờ */
    -webkit-backdrop-filter: blur(2px);
    color: white;
    transition: width 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); /* Đổ bóng bên phải */
}

.sidebar-collapsed {
    width: 60px;
}

.sidebar-expanded {
    width: 220px;
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

        /* Table */
        .order-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .order-table th {
            background-color: #e9ecef;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .order-table td, .order-table th {
            padding: 0.75rem;
        }

        /* Mobile View */
        .order-card {
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
        .action-button {
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: none;
        }

        .action-button:hover {
            background-color: #e9ecef;
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
            .order-table th, .order-table td {
                font-size: 0.8rem;
            }
            .order-card {
                font-size: 0.8rem;
            }
            main {
                margin-left: 50px;
            }
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
        <form id="filter-form" class="filter-form px-3 d-none" method="GET" action="" aria-label="Filter orders by status">
            <label for="statusFilter" class="form-label text-white small">Lọc theo trạng thái:</label>
            <select id="statusFilter" name="status" class="form-select form-select-sm status-select" aria-describedby="statusFilterDescription">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="Chờ xác nhận" <?= $status === 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                <option value="Đã xác nhận" <?= $status === 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                <option value="Đã thanh toán" <?= $status === 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                <option value="Chưa thanh toán" <?= $status === 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                <option value="Đã gửi" <?= $status === 'Đã gửi' ? 'selected' : '' ?>>🚚 Đã gửi</option>
                <option value="Hủy đơn" <?= $status === 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
            </select>
            <span id="statusFilterDescription" class="visually-hidden">Chọn trạng thái để lọc danh sách đơn hàng</span>
        </form>
    </aside>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">
            <h2 class="h5 mb-3 fw-bold text-primary">Đơn Hàng</h2>
            <!-- Mobile View -->
            <div class="d-block d-md-none mobile-container overflow-auto" style="max-height: 600px;" aria-describedby="mobileOrderDescription">
                <div class="mobile-header">
                    <h3 class="h6 fw-bold text-dark mb-0">Danh Sách Đơn Hàng</h3>
                </div>
                <?php if (empty($orders)): ?>
                    <div class="alert alert-info text-center m-2" role="alert">
                        Không có đơn hàng nào phù hợp.
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card m-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Mã ĐH: <?= htmlspecialchars($order['iddh']) ?></span>
                            </div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <p class="mb-0 small"><strong>Mã KH:</strong></p>
                                    <form action="update_idkh.php" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                        <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                        <input
                                            type="text"
                                            name="idkh"
                                            value="<?= htmlspecialchars($order['sdt']) ?>"
                                            class="form-control form-control-sm"
                                            aria-label="Mã khách hàng"
                                        >
                                        <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                            <button
                                                type="submit"
                                                class="action-button text-primary tooltip-custom"
                                                data-tooltip="Lưu mã khách hàng"
                                                aria-label="Lưu mã khách hàng"
                                            >
                                                <i class="fas fa-save"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div class="col-12">
                                    <p class="mb-0 small"><strong>Tên KH:</strong></p>
                                    <?php
                                    $tenkh = trim($order['tenkh']) === ''
                                        ? (function ($pdo, $sdt) {
                                            $sql = "SELECT hoten FROM user WHERE sdt = :sdt";
                                            $stmt = $pdo->prepare($sql);
                                            $stmt->execute(['sdt' => $sdt]);
                                            return $stmt->fetchColumn() ?: '';
                                        })($pdo, $order['sdt'])
                                        : $order['tenkh'];
                                    ?>
                                    <form action="update_tenkh.php" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                        <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                        <input
                                            type="text"
                                            name="tenkh"
                                            value="<?= htmlspecialchars($tenkh) ?>"
                                            placeholder="Tên KH"
                                            class="form-control form-control-sm"
                                            aria-label="Tên khách hàng"
                                        >
                                        <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                            <button
                                                type="submit"
                                                class="action-button text-primary tooltip-custom"
                                                data-tooltip="Lưu tên khách hàng"
                                                aria-label="Lưu tên khách hàng"
                                            >
                                                <i class="fas fa-save"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0 small"><strong>Tổng Tiền:</strong> <?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0 small"><strong>Ngày Đặt:</strong> <?= htmlspecialchars($order['thoigian']) ?></p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-0 small"><strong>Trạng Thái:</strong></p>
                                    <?php if ($_SESSION['user']['quyen'] == 1): ?>
                                        <form action="yeucauhuydon.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="idkh" value="<?= htmlspecialchars($_SESSION['user']['iduser']) ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <select
                                                name="trangthai"
                                                onchange="this.form.submit()"
                                                class="form-select form-select-sm"
                                                aria-label="Trạng thái đơn hàng"
                                                <?= in_array($order['trangthai'], ['Hủy đơn', 'Đã thanh toán']) ? 'disabled' : '' ?>
                                            >
                                                <option value="<?= htmlspecialchars($order['trangthai']) ?>" selected>
                                                    <?= htmlspecialchars($order['trangthai']) ?>
                                                </option>
                                                <option value="Hủy đơn">🔴 Hủy đơn</option>
                                            </select>
                                        </form>
                                    <?php else: ?>
                                        <form action="update_trangthai.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <select
                                                name="trangthai"
                                                onchange="this.form.submit()"
                                                class="form-select form-select-sm"
                                                aria-label="Trạng thái đơn hàng"
                                                <?= in_array($order['trangthai'], ['Đã thanh toán', 'Hủy đơn']) ? 'disabled' : '' ?>
                                            >
                                                <option value="Chờ xác nhận" <?= $order['trangthai'] === 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                                                <option value="Đã xác nhận" <?= $order['trangthai'] === 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                                                <option value="Đã thanh toán" <?= $order['trangthai'] === 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                                                <option value="Chưa thanh toán" <?= $order['trangthai'] === 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                                                <option value="Đã gửi" <?= $order['trangthai'] === 'Đã gửi' ? 'selected' : '' ?>>🚚 Đã gửi</option>
                                                <option value="Hủy đơn" <?= $order['trangthai'] === 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <p class="mb-0 small"><strong>Đánh Giá:</strong></p>
                                    <?php if ($order['trangthai'] === 'Đã thanh toán'): ?>
                                        <form action="<?= $_SESSION['user']['quyen'] == 1 ? '../donhang/danhgia.php' : '../donhang/chitietdanhgia.php' ?>" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <input type="hidden" name="idkh" value="<?= htmlspecialchars($_SESSION['user']['iduser']) ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-sm action-button tooltip-custom"
                                                data-tooltip="<?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá đơn hàng' : 'Xem chi tiết' ?>"
                                                aria-label="<?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá đơn hàng' : 'Xem chi tiết' ?>"
                                            >
                                                <i class="fas fa-star me-1"></i>
                                                <?= $_SESSION['user']['quyen'] == 1 ? 'Đánh Giá' : 'Xem chi tiết' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <span id="mobileOrderDescription" class="visually-hidden">Danh sách đơn hàng hiển thị dưới dạng thẻ trên thiết bị di động, có thể cuộn dọc nếu danh sách dài.</span>
            </div>

            <!-- Desktop View -->
            <div class="table-container overflow-auto d-none d-md-block" style="max-height: 600px;">
                <table class="order-table table table-hover" aria-describedby="orderTableDescription">
                    <thead>
                        <tr>
                            <th scope="col">Mã ĐH</th>
                            <th scope="col">Mã KH</th>
                            <th scope="col">Tên KH</th>
                            <th scope="col">Tổng Tiền</th>
                            <th scope="col">Trạng Thái</th>
                            <th scope="col" class="d-none d-lg-table-cell">Ngày Đặt</th>
                            <th scope="col">Đánh Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có đơn hàng nào phù hợp.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($order['iddh']) ?></strong></td>
                                    <td>
                                        <form action="update_idkh.php" method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <input
                                                type="text"
                                                name="idkh"
                                                value="<?= htmlspecialchars($order['sdt']) ?>"
                                                class="form-control form-control-sm"
                                                aria-label="Mã khách hàng"
                                            >
                                            <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                                <button
                                                    type="submit"
                                                    class="action-button text-primary tooltip-custom"
                                                    data-tooltip="Lưu mã khách hàng"
                                                    aria-label="Lưu mã khách hàng"
                                                >
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td>
                                        <?php
                                        $tenkh = trim($order['tenkh']) === ''
                                            ? (function ($pdo, $sdt) {
                                                $sql = "SELECT hoten FROM user WHERE sdt = :sdt";
                                                $stmt = $pdo->prepare($sql);
                                                $stmt->execute(['sdt' => $sdt]);
                                                return $stmt->fetchColumn() ?: '';
                                            })($pdo, $order['sdt'])
                                            : $order['tenkh'];
                                        ?>
                                        <form action="update_tenkh.php" method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <input
                                                type="text"
                                                name="tenkh"
                                                value="<?= htmlspecialchars($tenkh) ?>"
                                                placeholder="Tên KH"
                                                class="form-control form-control-sm"
                                                aria-label="Tên khách hàng"
                                            >
                                            <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                                <button
                                                    type="submit"
                                                    class="action-button text-primary tooltip-custom"
                                                    data-tooltip="Lưu tên khách hàng"
                                                    aria-label="Lưu tên khách hàng"
                                                >
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                                    <td>
                                        <?php if ($_SESSION['user']['quyen'] == 1): ?>
                                            <form action="yeucauhuydon.php" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                <input type="hidden" name="idkh" value="<?= htmlspecialchars($_SESSION['user']['iduser']) ?>">
                                                <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                                <select
                                                    name="trangthai"
                                                    onchange="this.form.submit()"
                                                    class="form-select form-select-sm"
                                                    aria-label="Trạng thái đơn hàng"
                                                    <?= in_array($order['trangthai'], ['Hủy đơn', 'Đã thanh toán']) ? 'disabled' : '' ?>
                                                >
                                                    <option value="<?= htmlspecialchars($order['trangthai']) ?>" selected>
                                                        <?= htmlspecialchars($order['trangthai']) ?>
                                                    </option>
                                                    <option value="Hủy đơn">🔴 Hủy đơn</option>
                                                </select>
                                            </form>
                                        <?php else: ?>
                                            <form action="update_trangthai.php" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                                <select
                                                    name="trangthai"
                                                    onchange="this.form.submit()"
                                                    class="form-select form-select-sm"
                                                    aria-label="Trạng thái đơn hàng"
                                                    <?= in_array($order['trangthai'], ['Đã thanh toán', 'Hủy đơn']) ? 'disabled' : '' ?>
                                                >
                                                    <option value="Chờ xác nhận" <?= $order['trangthai'] === 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                                                    <option value="Đã xác nhận" <?= $order['trangthai'] === 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                                                    <option value="Đã thanh toán" <?= $order['trangthai'] === 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                                                    <option value="Chưa thanh toán" <?= $order['trangthai'] === 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                                                    <option value="Đã gửi" <?= $order['trangthai'] === 'Đã gửi' ? 'selected' : '' ?>>🚚 Đã gửi</option>
                                                    <option value="Hủy đơn" <?= $order['trangthai'] === 'Hủy đơn' ? 'selected' : '' ?>>🔴 Hủy đơn</option>
                                                </select>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell"><?= htmlspecialchars($order['thoigian']) ?></td>
                                    <td>
                                        <?php if ($order['trangthai'] === 'Đã thanh toán'): ?>
                                            <form action="<?= $_SESSION['user']['quyen'] == 1 ? '../donhang/danhgia.php' : '../donhang/chitietdanhgia.php' ?>" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                                <input type="hidden" name="idkh" value="<?= htmlspecialchars($_SESSION['user']['iduser']) ?>">
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary btn-sm action-button tooltip-custom"
                                                    data-tooltip="<?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá đơn hàng' : 'Xem chi tiết' ?>"
                                                    aria-label="<?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá đơn hàng' : 'Xem chi tiết' ?>"
                                                >
                                                    <i class="fas fa-star me-1"></i>
                                                    <?= $_SESSION['user']['quyen'] == 1 ? 'Đánh Giá' : 'Xem chi tiết' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <span id="orderTableDescription" class="visually-hidden">Bảng hiển thị danh sách đơn hàng với thông tin mã đơn hàng, mã khách hàng, tên khách hàng, tổng tiền, trạng thái, ngày đặt và đánh giá, có thể cuộn dọc nếu danh sách dài.</span>
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
            const hamburger = document.querySelector('.hamburger');

            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
            const isExpanded = sidebar.classList.contains('sidebar-expanded');

            if (isExpanded) {
                title.classList.remove('d-none');
                filterForm.classList.remove('d-none');
                hamburger.classList.add('active');
            } else {
                title.classList.add('d-none');
                filterForm.classList.add('d-none');
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
            const hamburger = document.querySelector('.hamburger');
            const savedState = localStorage.getItem('sidebarState');

            if (savedState === 'expanded') {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                title.classList.remove('d-none');
                filterForm.classList.remove('d-none');
                hamburger.classList.add('active');
            } else {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.classList.remove('sidebar-expanded');
                title.classList.add('d-none');
                filterForm.classList.add('d-none');
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
    </script>
</body>
</html>