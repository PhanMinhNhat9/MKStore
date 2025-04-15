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
    <title>Quản Lý Danh Mục</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="hienthidanhmuc.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container">

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
                <tbody id="categoryTable">
                    <?php foreach ($categoryTree as $parent): ?>
                        <tr class="parent">
                            <td><?php echo $parent['iddm']; ?></td>
                            <td><?php echo $parent['tendm']; ?></td>
                            <td>
                                <img src="../<?php echo $parent['icon']; ?>" class="icon" alt="Icon danh mục">
                            </td>
                            <td><?php echo $parent['mota']; ?></td>
                            <td><?php echo $parent['thoigian']; ?></td>
                            <td>
                                <button onclick="themdmcon(<?php echo $parent['iddm']; ?>)" class="action-btn add-btn" title="Thêm danh mục con">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button onclick="capnhatdanhmuc()" class="action-btn edit-btn" title="Sửa danh mục">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                            </td>
                        </tr>
                        <?php foreach ($parent['children'] as $child): ?>
                            <tr class="child">
                                <td><?php echo $child['iddm']; ?></td>
                                <td><span class="child-indent">↳</span> <?php echo $child['tendm']; ?></td>
                                <td>
                                    <img src="../<?php echo $child['icon']; ?>" class="icon" alt="Icon danh mục con">
                                </td>
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
</body>
</html>

