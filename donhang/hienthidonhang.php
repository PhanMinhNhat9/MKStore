<?php
require_once('../config.php');

// Kết nối CSDL
try {
    $pdo = connectDatabase();
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Lấy tham số tìm kiếm và trạng thái
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

// Xây dựng truy vấn SQL
try {
    $params = [];
    $sql = "SELECT iddh, sdt, tenkh, tongtien, trangthai, hientrang, phuongthuctt, thoigian FROM donhang";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Quản Lý Đơn Hàng - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #60a5fa;
            --accent-color: #34d399;
            --danger-color: #f87171;
            --background-color: #f1f5f9;
            --card-background: #ffffff;
            --text-color: #1e293b;
            --border-color: #e5e7eb;
            --hover-color: #eff6ff;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 0.875rem;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding-top: 60px;
            overflow-x: auto;
            line-height: 1.4;
        }

        /* Navbar */
        #navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .navbar-filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 650px; /* Adjusted for wider elements */
            flex-wrap: nowrap; /* Keep items on one row */
        }

        .navbar-filter .form-control {
            border-radius: 6px;
            border: 1px solid var(--border-color);
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            max-width: 400px; /* Wider search bar */
            width: 100%;
            min-width: 200px; /* Prevent collapse */
        }

        .navbar-filter .form-select {
            border-radius: 6px;
            border: 1px solid var(--border-color);
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            max-width: 200px; /* Wider select */
            width: 100%;
            min-width: 150px;
        }

        .navbar-filter .form-control:focus,
        .navbar-filter .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.25);
        }

        .navbar-filter .action-button {
            padding: 0.5rem 0.75rem; /* Match input height */
            font-size: 0.875rem;
        }

        /* Hamburger */
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
            transition: all 0.3s ease;
        }

        .hamburger span:nth-child(1) { top: 0; }
        .hamburger span:nth-child(2) { top: 7px; }
        .hamburger span:nth-child(3) { top: 14px; }

        .hamburger.active span:nth-child(1) {
            top: 7px;
            transform: rotate(45deg);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
            left: -50px;
        }

        .hamburger.active span:nth-child(3) {
            top: 7px;
            transform: rotate(-45deg);
        }

        /* Main Content */
        main {
            padding: 1rem;
        }

        /* Card Layout */
        .order-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-top: 0.75rem;
        }

        .order-card {
            background: var(--card-background);
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            padding: 0.8rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-width: 200px;
        }

        .order-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .order-card h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0 0 0.75rem;
            color: var(--primary-color);
        }

        .order-card .order-info {
            display: grid;
            gap: 0.5rem;
        }

        .order-card .order-info div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .order-card .order-info label {
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.75rem;
        }

        .order-card .order-info span,
        .order-card .order-info form {
            flex: 1;
            text-align: right;
            font-size: 0.75rem;
        }

        .order-card .order-info form {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            justify-content: flex-end;
        }

        /* Buttons */
        .action-button {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            border: none;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
        }

        .action-button:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-button:active {
            transform: scale(1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-success {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        /* Inputs and Selects */
        .form-control-sm, .form-select-sm {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 200px;
        }

        .form-control-sm:focus, .form-select-sm:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.25);
        }

        /* Tooltip */
        .tooltip-custom {
            position: relative;
        }

        .tooltip-custom:hover:after {
            content: attr(data-tooltip);
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            z-index: 20;
            margin-top: 0.4rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 1.5rem;
            background: var(--card-background);
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            margin-top: 0.75rem;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 1.5rem;
        }

        .empty-state p {
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .order-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 50px;
                padding-bottom: 6rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-filter {
                max-width: 100%;
                flex-direction: column;
                align-items: stretch;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .navbar-filter .form-control,
            .navbar-filter .form-select {
                max-width: 100%;
                font-size: 0.85rem;
                padding: 0.4rem 0.6rem;
            }

            .navbar-filter .action-button {
                align-self: flex-end;
                padding: 0.4rem 0.6rem;
                font-size: 0.85rem;
            }

            .order-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-card {
                padding: 0.6rem;
            }

            .order-card h3 {
                font-size: 0.9rem;
            }

            .order-card .order-info div {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
            }

            .order-card .order-info label,
            .order-card .order-info span {
                font-size: 0.7rem;
            }

            .form-control-sm, .form-select-sm {
                font-size: 0.7rem;
                max-width: 100%;
            }

            .action-button {
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .order-container {
                grid-template-columns: 1fr;
            }

            .order-card .order-info div {
                font-size: 0.65rem;
            }

            .action-button {
                padding: 0.25rem 0.5rem;
                font-size: 0.65rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav id="navbar" class="navbar navbar-expand-md">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Hệ Thống Bán Hàng</a>
            <button class="hamburger d-md-none" aria-label="Toggle Filter" onclick="toggleFilter()">
                <span></span>
                <span></span>
                <span></span>
                <span class="visually-hidden">Toggle Filter</span>
            </button>
            <div class="collapse navbar-collapse" id="navbarFilter">
                <form id="filter-form" class="navbar-filter ms-auto" method="GET" action="" aria-label="Filter orders by status and search">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <input
                                type="text"
                                name="query"
                                value="<?= htmlspecialchars($query) ?>"
                                class="form-control"
                                placeholder="Tìm mã đơn/tên KH"
                                aria-label="Tìm kiếm đơn hàng"
                            >
                            <button type="submit" class="action-button btn-primary" aria-label="Tìm kiếm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <select
                            id="statusFilter"
                            name="status"
                            class="form-select"
                            aria-describedby="statusFilterDescription"
                        >
                            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Tất cả</option>
                            <option value="Chưa thanh toán" <?= $status === 'Chưa thanh toán' ? 'selected' : '' ?>>Chưa thanh toán</option>
                            <option value="Đã thanh toán" <?= $status === 'Đã thanh toán' ? 'selected' : '' ?>>Đã thanh toán</option>
                        </select>
                    </div>
                    <span id="statusFilterDescription" class="visually-hidden">Chọn trạng thái hoặc nhập từ khóa để lọc danh sách đơn hàng</span>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle text-muted mb-2"></i>
                    <p class="text-muted mb-0">Không có đơn hàng nào phù hợp.</p>
                </div>
            <?php else: ?>
                <div class="order-container" aria-describedby="orderContainerDescription">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <h3>Mã ĐH: <?= htmlspecialchars($order['iddh']) ?></h3>
                            <div class="order-info">
                                <div>
                                    <label>Mã KH:</label>
                                    <form action="update_idkh.php" method="POST">
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
                                                class="action-button btn-primary tooltip-custom"
                                                data-tooltip="Lưu mã khách hàng"
                                                aria-label="Lưu mã khách hàng"
                                            >
                                                <i class="fas fa-save"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div>
                                    <label>Tên KH:</label>
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
                                    <form action="update_tenkh.php" method="POST">
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
                                                class="action-button btn-primary tooltip-custom"
                                                data-tooltip="Lưu tên khách hàng"
                                                aria-label="Lưu tên khách hàng"
                                            >
                                                <i class="fas fa-save"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div>
                                    <label>Tổng Tiền:</label>
                                    <span><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</span>
                                </div>
                                <div>
                                    <label>Phương thức TT:</label>
                                    <span><?= htmlspecialchars($order['phuongthuctt']) ?></span>
                                </div>
                                <div>
                                    <label>Hiện trạng:</label>
                                    <?php if ($_SESSION['user']['quyen'] == 2589 || $_SESSION['user']['quyen'] == 0): ?>
                                        <form action="update_hientrang.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <select
                                                name="hientrang"
                                                onchange="this.form.submit()"
                                                class="form-select form-select-sm"
                                                aria-label="Hiện trạng đơn hàng"
                                                <?= in_array($order['hientrang'], ['Đã nhận hàng']) ? 'disabled' : '' ?>
                                            >
                                                <option value="Chờ xác nhận" <?= $order['hientrang'] === 'Chờ xác nhận' ? 'selected' : '' ?>>🔵 Chờ xác nhận</option>
                                                <option value="Đã xác nhận" <?= $order['hientrang'] === 'Đã xác nhận' ? 'selected' : '' ?>>🟢 Đã xác nhận</option>
                                                <option value="Đang đóng gói" <?= $order['hientrang'] === 'Đang đóng gói' ? 'selected' : '' ?>>📦 Đang đóng gói</option>
                                                <option value="Giao hàng cho nhà vận chuyển" <?= $order['hientrang'] === 'Giao hàng cho nhà vận chuyển' ? 'selected' : '' ?>>🚛 Giao hàng cho nhà vận chuyển</option>
                                                <option value="Đang vận chuyển" <?= $order['hientrang'] === 'Đang vận chuyển' ? 'selected' : '' ?>>🚚 Đang vận chuyển</option>
                                                <option value="Đã nhận hàng" <?= $order['hientrang'] === 'Đã nhận hàng' ? 'selected' : '' ?>>✅ Đã nhận hàng</option>
                                            </select>
                                        </form>
                                    <?php else: ?>
                                        <span><?= htmlspecialchars($order['hientrang']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label>Trạng Thái:</label>
                                    <?php if ($_SESSION['user']['quyen'] == 1): ?>
                                        <span><?= htmlspecialchars($order['trangthai']) ?></span>
                                    <?php else: ?>
                                        <form action="update_trangthai.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <select
                                                name="trangthai"
                                                onchange="this.form.submit()"
                                                class="form-select form-select-sm"
                                                aria-label="Trạng thái đơn hàng"
                                                <?= $order['trangthai'] === 'Đã thanh toán' ? 'disabled' : '' ?>
                                            >
                                                <option value="Chưa thanh toán" <?= $order['trangthai'] === 'Chưa thanh toán' ? 'selected' : '' ?>>🟠 Chưa thanh toán</option>
                                                <option value="Đã thanh toán" <?= $order['trangthai'] === 'Đã thanh toán' ? 'selected' : '' ?>>🟢 Đã thanh toán</option>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label>Ngày Đặt:</label>
                                    <span><?= htmlspecialchars($order['thoigian']) ?></span>
                                </div>
                                <div>
                                    <label>Cập nhật TT:</label>
                                    <?php if ($_SESSION['user']['quyen'] != 1 && $order['trangthai'] !== 'Đã thanh toán'): ?>
                                        <form action="update_payment_status.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <button
                                                type="submit"
                                                class="action-button btn-success tooltip-custom"
                                                data-tooltip="Cập nhật trạng thái thành Đã thanh toán"
                                                aria-label="Cập nhật trạng thái thành Đã thanh toán"
                                            >
                                                <i class="fas fa-check me-1"></i>Đã TT
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa đến lúc!</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label>Hủy Đơn:</label>
                                    <?php if (in_array($order['hientrang'], ['Chờ xác nhận', 'Đã xác nhận', 'Đang đóng gói']) && in_array($order['trangthai'], ['Chưa thanh toán'])): ?>
                                        <form action="yeucauhuydon.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="idkh" value="<?= htmlspecialchars($_SESSION['user']['iduser']) ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <button
                                                type="submit"
                                                class="action-button btn-danger tooltip-custom"
                                                data-tooltip="Hủy đơn hàng"
                                                aria-label="Hủy đơn hàng"
                                                onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')"
                                            >
                                                <i class="fas fa-times me-1"></i>Hủy
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Không thể hủy!</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label>Đánh Giá:</label>
                                    <?php if ($order['trangthai'] === 'Đã thanh toán'): ?>
                                        <form action="<?= $_SESSION['user']['quyen'] == 1 ? '../donhang/danhgia.php' : '../donhang/chitietdanhgia.php' ?>" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="iddh" value="<?= htmlspecialchars($order['iddh']) ?>">
                                            <input type="hidden" name="idkh" value="<?= htmlspecialchars($_SESSION['user']['iduser']) ?>">
                                            <button
                                                type="submit"
                                                class="action-button btn-primary tooltip-custom"
                                                data-tooltip="<?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá đơn hàng' : 'Xem chi tiết' ?>"
                                                aria-label="<?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá đơn hàng' : 'Xem chi tiết' ?>"
                                            >
                                                <i class="fas fa-star me-1"></i>
                                                <?= $_SESSION['user']['quyen'] == 1 ? 'Đánh giá' : 'Xem CT' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa đến lúc!</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <span id="orderContainerDescription" class="visually-hidden">Danh sách các đơn hàng được hiển thị dưới dạng thẻ, mỗi thẻ chứa thông tin mã đơn hàng, mã khách hàng, tên khách hàng, tổng tiền, phương thức thanh toán, hiện trạng, trạng thái, ngày đặt, cập nhật thanh toán, hủy đơn và đánh giá.</span>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function toggleFilter() {
            const navbarFilter = document.getElementById('navbarFilter');
            const hamburger = document.querySelector('.hamburger');
            const isExpanded = navbarFilter.classList.contains('show');

            if (!isExpanded) {
                navbarFilter.classList.add('show');
                hamburger.classList.add('active');
            } else {
                navbarFilter.classList.remove('show');
                hamburger.classList.remove('active');
            }

            hamburger.setAttribute('aria-expanded', !isExpanded);
            localStorage.setItem('filterState', !isExpanded ? 'expanded' : 'collapsed');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const navbarFilter = document.getElementById('navbarFilter');
            const hamburger = document.querySelector('.hamburger');
            const savedState = localStorage.getItem('filterState');

            if (savedState === 'expanded' && window.innerWidth < 768) {
                navbarFilter.classList.add('show');
                hamburger.classList.add('active');
                hamburger.setAttribute('aria-expanded', true);
            } else {
                navbarFilter.classList.remove('show');
                hamburger.classList.remove('active');
                hamburger.setAttribute('aria-expanded', false);
            }

            const filterForm = document.getElementById('filter-form');
            if (filterForm) {
                document.getElementById('statusFilter').addEventListener('change', () => {
                    filterForm.submit();
                });
            }
        });
    </script>
</body>
</html>