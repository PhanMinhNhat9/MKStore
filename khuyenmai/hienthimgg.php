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
    <title>Quản lý mã giảm giá</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="hienthimgg.css?v=<?= time(); ?>">
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <h1>Hệ Thống Bán Hàng</h1>
            <div class="filter-section">
                <label for="statusFilter">Lọc theo trạng thái:</label>
                <select id="statusFilter">
                    <option value="all">Tất cả</option>
                    <option value="active">Còn hiệu lực</option>
                    <option value="expired">Hết hạn</option>
                    <option value="pending">Chưa hiệu lực</option>
                </select>

                <button onclick="themmgg()" class="btn-add-category">
                    <i class="fas fa-plus-circle"></i> Thêm Mã Giảm Giá
                </button>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <div class="table-body-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Giảm (%)</th>
                                <th>Hiệu lực</th>
                                <th>Hết hạn</th>
                                <th>Giá tối thiểu</th>
                                <th>Số lượng</th>
                                <th>Danh mục</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                                <td><?= $coupon['phantram'] ?>%</td>
                                <td><?= date('d-m-Y', strtotime($coupon['ngayhieuluc'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($coupon['ngayketthuc'])) ?></td>
                                <td><?= number_format($coupon['giaapdung'], 0, ',', '.') ?> VND</td>
                                <td><?= (int) $coupon['soluong'] ?></td>
                                <td><?= !empty($coupon['tendm']) ? htmlspecialchars($coupon['tendm']) : '<em>Không có</em>' ?></td>
                                <td>
                                    <?php
                                    if ($coupon['ngayhieuluc'] > $currentDate) {
                                        echo '<span class="status pending">Chưa hiệu lực</span>';
                                    } elseif ($coupon['ngayketthuc'] < $currentDate || $coupon['soluong'] <= 0) {
                                        echo '<span class="status expired">Hết hạn</span>';
                                    } else {
                                        echo '<span class="status active">Còn hiệu lực</span>';
                                    }
                                    ?>
                                </td>
                                <td class="actions">
                                    <button onclick="capnhatmgg(<?= $coupon['idmgg'] ?>)" class="btn edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="xoamgg(<?= $coupon['idmgg'] ?>)" class="btn delete-btn">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const statusFilter = document.getElementById("statusFilter");
            const rows = document.querySelectorAll(".styled-table tbody tr");

            function applyFilter() {
                const value = statusFilter.value;
                rows.forEach(row => {
                    const status = row.querySelector(".status");
                    const statusClass = status.classList.contains("active") ? "active"
                                        : status.classList.contains("expired") ? "expired"
                                        : "pending";

                    row.style.display = (value === "all" || value === statusClass) ? "" : "none";
                });
            }

            statusFilter.addEventListener("change", function () {
                localStorage.setItem("statusFilter", this.value);
                applyFilter();
            });

            // Khôi phục lựa chọn sau khi tải lại trang
            const savedValue = localStorage.getItem("statusFilter");
            if (savedValue) {
                statusFilter.value = savedValue;
                applyFilter();
            }
        });
    </script>
</body>
</html>

