<?php
require_once '../config.php';
$pdo = connectDatabase();
// Truy vấn dữ liệu danh mục
$sql = "SELECT iddm, tendm, loaidm, icon, mota, thoigian FROM danhmucsp ORDER BY loaidm, iddm";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Sắp xếp danh mục cha và con
$categoryTree = [];
foreach ($categories as $category) {
    if ($category['loaidm'] == 0) {
        // Danh mục cha
        $categoryTree[$category['iddm']] = $category;
        $categoryTree[$category['iddm']]['children'] = [];
    } else {
        // Danh mục con
        $categoryTree[$category['loaidm']]['children'][] = $category;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="hienthidanhmuc.css?v=<?= time(); ?>">
    <script src="../trangchuadmin.js"></script>
    <title></title>
</head>
<body>
    <div class="dm-table-container">
        <table class="dm-table">
            <thead>
                <tr>
                    <th style="text-align: center;">ID</th>
                    <th style="text-align: center;">Tên Danh Mục</th>
                    <th style="text-align: center;">Icon</th>
                    <th style="text-align: center;">Mô Tả</th>
                    <th style="text-align: center;">Thời Gian</th>
                    <th style="text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categoryTree as $parent): ?>
                    <tr class="parent">
                        <td style="text-align: center;"><?php echo $parent['iddm']; ?></td>
                        <td><?= $parent['tendm'] ?></td>
                        <td style="text-align: center;">
                            <div class="tooltip-container">
                                <img src="../<?= $parent['icon'] ?>" class="icon" alt="Icon">
                                <span class="tooltip-text"><?= $parent['tendm'] ?></span>
                            </div>
                        </td>
                        <td><?= $parent['mota'] ?></td>
                        <td style="text-align: center;"><?= $parent['thoigian'] ?></td>
                        <td style="text-align: center">
                            <i onclick="themdmcon(<?php echo $parent['iddm']; ?>)" class="fas fa-plus-circle action-icons btn-xoadm" title="Thêm"></i>
                            <i onclick="capnhatdanhmuc()" class="fas fa-edit action-icons btn-updatedm" title="Sửa"></i>
                        </td>
                    </tr>
                    <?php foreach ($parent['children'] as $child): ?>
                        <tr class="child">
                            <td style="text-align: center;"><?= $child['iddm'] ?></td>
                            <td> 🌱 <?= $child['tendm'] ?></td>
                            <td style="text-align: center;">
                                <div class="tooltip-container">
                                    <img src="../<?= $child['icon'] ?>" class="icon" alt="Icon">
                                    <span class="tooltip-text"><?= $child['tendm'] ?></span>
                                </div>
                            </td>
                            <td><?= $child['mota'] ?></td>
                            <td style="text-align: center;"><?= $child['thoigian'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

