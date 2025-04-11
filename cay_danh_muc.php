<?php
require_once 'config.php';
$pdo = connectDatabase();

// Lấy danh mục
$sql = "SELECT iddm, tendm, loaidm, icon FROM danhmucsp ORDER BY loaidm, iddm";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$danhmucs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cây danh mục sản phẩm</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #f9f9f9;
            padding: 20px;
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid #ccc;
            font-size: 14px;
        }
        .tree {
            list-style-type: none;
            padding-left: 0;
        }
        .tree li {
            margin: 6px 0;
        }
        .tree .folder > i {
            margin-right: 6px;
            color: #e69500;
        }
        .tree .file {
            padding-left: 18px;
        }
        .tree .file i {
            color: #007bff;
            margin-right: 6px;
        }
        .tree a {
            text-decoration: none;
            color: #333;
        }
        .tree a:hover {
            color: #007bff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3><i class="fas fa-sitemap"></i> Cây danh mục</h3>
    <ul class="tree">
        <?php
        $grouped = [];
        foreach ($danhmucs as $dm) {
            $grouped[$dm['loaidm']][] = $dm;
        }
        foreach ($grouped as $loai => $dms): ?>
            <li class="folder">
                <i class="fas fa-folder"></i> <?= htmlspecialchars($loai) ?>
                <ul>
                    <?php foreach ($dms as $dm): ?>
                        <li class="file">
                            <a href="?iddm=<?= $dm['iddm'] ?>">
                                <i class="<?= htmlspecialchars($dm['icon']) ?>"></i>
                                <?= htmlspecialchars($dm['tendm']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
