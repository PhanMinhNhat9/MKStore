<?php
    require_once '../config.php';
    $pdo = connectDatabase();

    $sql = "SELECT mg.idmgg, mg.code, mg.phantram, mg.ngayhieuluc, mg.ngayketthuc, 
                mg.giaapdung, mg.iddm, mg.soluong, mg.thoigian, dm.tendm 
            FROM magiamgia mg
            LEFT JOIN danhmucsp dm ON mg.iddm = dm.iddm";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentDate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Mã Giảm Giá - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="hienthimgg.css?v=<?= time(); ?>">
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
        <form class="filter-form" id="filterForm" method="GET" action="">
            <label for="statusFilter">Lọc theo trạng thái:</label>
            <select id="statusFilter" name="status">
                <option value="all" <?= isset($_GET['status']) && $_GET['status'] == 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="active" <?= isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : '' ?>>Còn hiệu lực</option>
                <option value="expired" <?= isset($_GET['status']) && $_GET['status'] == 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?>>Chưa hiệu lực</option>
            </select>
            <button type="submit" class="btn btn-filter" aria-label="Lọc mã giảm giá">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </form>
        <button class="btn btn-add" onclick="themmgg()" aria-label="Thêm mã giảm giá">
            <i class="fas fa-plus-circle"></i> Thêm Mã Giảm Giá
        </button>
    </aside>

    <!-- Main Content -->
    <main class="container">
        <section class="table-container">
            <table class="coupon-table">
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Giảm (%)</th>
                        <th scope="col">Hiệu lực</th>
                        <th scope="col">Hết hạn</th>
                        <th scope="col">Giá tối thiểu</th>
                        <th scope="col">Số lượng</th>
                        <th scope="col">Danh mục</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                            <td><?= (int)$coupon['phantram'] ?>%</td>
                            <td><?= date('d-m-Y', strtotime($coupon['ngayhieuluc'])) ?></td>
                            <td><?= date('d-m-Y', strtotime($coupon['ngayketthuc'])) ?></td>
                            <td><?= number_format($coupon['giaapdung'], 0, ',', '.') ?> VND</td>
                            <td><?= (int)$coupon['soluong'] ?></td>
                            <td><?= !empty($coupon['tendm']) ? htmlspecialchars($coupon['tendm']) : '<em>Không có</em>' ?></td>
                            <td>
                                <?php
                                if ($coupon['ngayhieuluc'] > $currentDate) {
                                    echo '<span class="status status-pending">Chưa hiệu lực</span>';
                                } elseif ($coupon['ngayketthuc'] < $currentDate || $coupon['soluong'] <= 0) {
                                    echo '<span class="status status-expired">Hết hạn</span>';
                                } else {
                                    echo '<span class="status status-active">Còn hiệu lực</span>';
                                }
                                ?>
                            </td>
                            <td class="actions">
                                <button 
                                    onclick="capnhatmgg(<?= $coupon['idmgg'] ?>)" 
                                    class="btn btn-edit" 
                                    aria-label="Cập nhật mã giảm giá"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button 
                                    onclick="xoamgg(<?= $coupon['idmgg'] ?>)" 
                                    class="btn btn-delete" 
                                    aria-label="Xóa mã giảm giá"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($coupons)): ?>
                        <tr>
                            <td colspan="9" class="no-data">Không có mã giảm giá nào phù hợp.</td>
                        </tr>
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
            sidebar.classList.toggle('collapsed');
            const isExpanded = !sidebar.classList.contains('collapsed');
            document.querySelector('.hamburger').setAttribute('aria-expanded', isExpanded);
        }
    </script>
</body>
</html>

