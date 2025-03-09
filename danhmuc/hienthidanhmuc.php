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
    <title></title>
</head>
<body>
    <button class="add-dm-btn" onclick=""><i class="fas fa-plus-circle"></i> Thêm danh mục gốc</button>
    <div class="dm-table-container">
        <table class="dm-table">
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
            <tbody>
                <?php foreach ($categoryTree as $parent): ?>
                    <tr class="parent">
                        <td><?php echo $parent['iddm']; ?></td>
                        <td><?= $parent['tendm'] ?></td>
                        <td><img src="<?= $parent['icon'] ?>" class="icon" alt="Icon"></td>
                        <td><?= $parent['mota'] ?></td>
                        <td><?= $parent['thoigian'] ?></td>
                        <td>
                            <i onclick="themdmcon(<?php echo $parent['iddm']; ?>)" class="fas fa-plus-circle action-icons" title="Thêm"></i>
                            <i onclick="capnhatdanhmuc()" class="fas fa-edit action-icons" title="Sửa"></i>
                            <i onclick="xoadanhmuccha()" class="fas fa-trash-alt action-icons" title="Xóa"></i>
                        </td>
                    </tr>
                    <?php foreach ($parent['children'] as $child): ?>
                        <tr class="child">
                            <td><?= $child['iddm'] ?></td>
                            <td> 🌱 <?= $child['tendm'] ?></td>
                            <td><img src="<?= $child['icon'] ?>" class="icon" alt="Icon"></td>
                            <td><?= $child['mota'] ?></td>
                            <td><?= $child['thoigian'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

