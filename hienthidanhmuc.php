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
    <script src="trangchuadmin.js"></script>
    <style>
        /* Định dạng cho việc hiển thị bảng danh mục */
        .dm-table-container {
            margin: 0 auto;
            max-height: 390px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }

        .dm-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .dm-table thead {
            position: sticky;
            top: 0;
            background: #007bff;
            color: white;
            z-index: 10;
        }

        .dm-table th, .dm-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .dm-table th {
            background: #007bff;
            color: #fff;
            border: 1px solid #ddd;
        }

        .dm-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .dm-table img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .action-icons {
            cursor: pointer;
            margin: 0 5px;
            transition: transform 0.2s, color 0.2s;
        }

        .action-icons:hover {
            transform: scale(1.2);
            color: #0056b3;
        }

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
            transition: background 0.3s, color 0.3s;
        }

        .add-dm-btn:hover {
            background-color: #007BFF;
            color: white;
        }

        @media (max-width: 768px) {
            .dm-table-container { width: 90%; }
            .dm-table th, .dm-table td { font-size: 12px; padding: 6px; }
        }

        .fa-plus-circle { color: #28a745; } /* Xanh lá - Thêm */
        .fa-edit { color: #ffc107; } /* Vàng - Sửa */
        .fa-trash-alt { color: #dc3545; } /* Đỏ - Xóa */
    </style>
</head>
<body>
    <button class="add-dm-btn" onclick=""><i class="fas fa-plus"></i> Thêm danh mục gốc</button>
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
                            <i onclick="themdanhmuc(<?php echo $parent['iddm']; ?>)" class="fas fa-plus-circle action-icons" title="Thêm"></i>
                            <i onclick="capnhatdanhmuc()" class="fas fa-edit action-icons" title="Sửa"></i>
                            <i onclick="xoadanhmuc()" class="fas fa-trash-alt action-icons" title="Xóa"></i>
                        </td>
                    </tr>
                    <?php foreach ($parent['children'] as $child): ?>
                        <tr class="child">
                            <td><?= $child['iddm'] ?></td>
                            <td> 🌱 <?= $child['tendm'] ?></td>
                            <td><img src="<?= $child['icon'] ?>" class="icon" alt="Icon"></td>
                            <td><?= $child['mota'] ?></td>
                            <td><?= $child['thoigian'] ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

