<?php
require_once '../config.php';
$pdo = connectDatabase();

$sql = "SELECT iddm, tendm, loaidm, icon, mota, thoigian FROM danhmucsp ORDER BY loaidm, iddm";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categoryTree = [];
foreach ($categories as $cat) {
    if ($cat['loaidm'] == 0) {
        $categoryTree[$cat['iddm']] = $cat + ['children' => []];
    } else {
        $categoryTree[$cat['loaidm']]['children'][] = $cat;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Danh Mục - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthidanhmuc.css?v=<?= time(); ?>">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h1>Hệ Thống Bán Hàng</h1>
        <label for="parentFilter">Lọc theo danh mục cha:</label> <br> <br>
        <select id="parentFilter">
            <option value="all">Tất cả</option>
            <?php foreach ($categoryTree as $parent): ?>
                <option value="<?php echo $parent['iddm']; ?>"><?php echo $parent['tendm']; ?></option>
            <?php endforeach; ?>
        </select>
        <center><button onclick="themdmcha(0)" title="Thêm con">
            <i class="fas fa-plus-circle"></i> Thêm Danh Mục Cha
        </button> </center>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Table -->
        <div class="table-container">
            <!-- Table Head -->
            <table class="table-header">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Icon</th>
                        <th>Mô Tả</th>
                        <th>Thời Gian</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
            </table>

            <!-- Table Body with Scroll -->
            <div class="table-body-container">
                <table class="table-body">
                    <tbody>
                        <?php foreach ($categoryTree as $parent): ?>
                            <tr class="parent" data-parent-id="<?php echo $parent['iddm']; ?>">
                                <td><?php echo $parent['iddm']; ?></td>
                                <td><?php echo $parent['tendm']; ?></td>
                                <td><img src="../<?php echo $parent['icon']; ?>" class="icon" alt="Icon"></td>
                                <td><?php echo $parent['mota']; ?></td>
                                <td><?php echo $parent['thoigian']; ?></td>
                                <td>
                                    <button onclick="themdmcon(<?php echo $parent['iddm']; ?>)" class="text-blue" title="Thêm con">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                    <button onclick="capnhatdanhmuc()" class="text-green" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php foreach ($parent['children'] as $child): ?>
                                <tr class="child" data-parent-id="<?php echo $parent['iddm']; ?>">
                                    <td><?php echo $child['iddm']; ?></td>
                                    <td><span class="child-indent">↳</span> <?php echo $child['tendm']; ?></td>
                                    <td><img src="../<?php echo $child['icon']; ?>" class="icon" alt="Icon con"></td>
                                    <td><?php echo $child['mota']; ?></td>
                                    <td><?php echo $child['thoigian']; ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>



        // Lọc theo danh mục cha
        document.getElementById('parentFilter').addEventListener('change', function () {
            const selectedParentId = this.value;
            const rows = document.querySelectorAll('.table-body tbody tr');

            rows.forEach(row => {
                const rowParentId = row.getAttribute('data-parent-id');
                if (selectedParentId === 'all') {
                    row.style.display = '';
                } else {
                    row.style.display = rowParentId === selectedParentId ? '' : 'none';
                }
            });
        });
    </script>
</body>
</html>
