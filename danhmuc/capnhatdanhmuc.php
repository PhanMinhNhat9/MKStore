<?php
require_once '../config.php';
$pdo = connectDatabase(); 

$sql = "SELECT dm1.iddm, dm1.tendm, dm1.loaidm, dm1.icon, dm1.mota, 
        dm2.tendm AS tencha FROM danhmucsp dm1 
        LEFT JOIN danhmucsp dm2 ON dm1.loaidm = dm2.iddm";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$danhmuc = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlCha = "SELECT * FROM danhmucsp WHERE loaidm = 0";
$stmtCha = $pdo->prepare($sqlCha);
$stmtCha->execute();
$danhmucCha = $stmtCha->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật danh mục</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color:rgb(255, 255, 255);
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        input, select {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }
        input:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .icon-preview {
            width: 40px;
            height: 40px;
            object-fit: contain;
            display: block;
            margin: auto;
        }
        .btn-save {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-save:hover {
            background: #0056b3;
        }
        #icon {
            width: 30px;
            height: 30px;
        }
        thead {
            position: sticky;
            top: 0;
            background: #007bff;
            z-index: 2;
        }


    </style>
</head>
<body>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <th><i class="fas fa-tags"></i> Tên danh mục</th>
                    <th><i class="fas fa-list"></i> Danh mục cha</th>
                    <th><i class="fas fa-image"></i> Icon hiện tại</th>
                    <th><i class="fas fa-upload"></i> Icon mới</th>
                    <th><i class="fas fa-info-circle"></i> Mô tả</th>
                    <th><i class="fas fa-save"></i> Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhmuc as $dm) { ?>
                <tr>
                    <form action="../config.php" method="POST" enctype="multipart/form-data">
                        <td><?php echo $dm['iddm']; ?></td>
                        <td><input type="text" name="tendm" value="<?php echo $dm['tendm']; ?>"></td>
                        <td>
                            <?php if ($dm['loaidm'] > 0) { ?>
                                <select name="loaidm">
                                    <option value="">Chọn danh mục cha</option>
                                    <?php foreach ($danhmucCha as $cha) { ?>
                                        <option value="<?php echo $cha['iddm']; ?>" <?php echo ($cha['iddm'] == $dm['loaidm']) ? 'selected' : ''; ?>>
                                            <?php echo $cha['tendm']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <i class="fas fa-folder" style="color: blue;"></i> Danh mục cha
                            <?php } ?>
                        </td>
                        <td>
                            <img id="icon" name="icon" src="../<?php echo $dm['icon'] ?>">
                        </td>
                        <td>
                            <input type="file" name="icon_new" accept="image/*" onchange="previewImage(this, 'preview-<?php echo $dm['iddm']; ?>')">
                            <img id="preview-<?php echo $dm['iddm']; ?>" src="<?php echo $dm['icon']; ?>" class="icon-preview">
                        </td>
                        <td><input type="text" name="mota" value="<?php echo $dm['mota']; ?>"></td>
                        <td>
                            <input type="hidden" name="iddm" value="<?php echo $dm['iddm']; ?>">
                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Lưu</button>
                        </td>
                    </form>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        function previewImage(input, previewId) {
            let file = input.files[0];
            let preview = document.getElementById(previewId);
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
