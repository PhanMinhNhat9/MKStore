<?php
require_once 'config.php';
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
    <title>Danh Mục Sản Phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; }
        
        .table-container {
            max-height: 400px; /* Giới hạn chiều cao để có thanh cuộn */
            overflow-y: auto; /* Thanh cuộn dọc */
            overflow-x: auto; /* Thanh cuộn ngang nếu cần */
            border: 1px solid #ddd;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; white-space: nowrap; }
        th { background-color: #f4f4f4; position: sticky; top: 0; z-index: 2; }
        .parent { background: #e0f7fa; font-weight: bold; }
        .child { background: #f1f8e9; padding-left: 30px; }
        .icon { width: 32px; height: 32px; }
        
/* Nút Thêm Người Dùng - Tối giản */
.add-dm-btn {
    background: none;
    border: 1px solid #007BFF;
    color: #007BFF;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    margin: 10px 0px;
    margin-left: 10px;
    font-size: 14px;
    
}
.add-dm-btn:hover {
    background-color: #007BFF;
    color: white;
}
    </style>
</head>
<body>
<button class="add-dm-btn" onclick=""><i class="fas fa-plus"></i> Thêm danh mục</button>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Danh Mục</th>
                    <th>Icon</th>
                    <th>Mô Tả</th>
                    <th>Thời Gian</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categoryTree as $parent): ?>
                    <!-- Hiển thị danh mục cha -->
                    <tr class="parent">
                        <td><?php echo $parent['iddm']; ?></td>
                        <td><?= $parent['tendm'] ?></td>
                        <td><img src="<?= $parent['icon'] ?>" class="icon" alt="Icon"></td>
                        <td><?= $parent['mota'] ?></td>
                        <td><?= $parent['thoigian'] ?></td>
                    </tr>

                    <!-- Hiển thị danh mục con -->
                    <?php foreach ($parent['children'] as $child): ?>
                        <tr class="child">
                            <td><?= $child['iddm'] ?></td>
                            <td> └── <?= $child['tendm'] ?></td>
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
