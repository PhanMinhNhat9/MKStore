<?php
require_once '../config.php';
$pdo = connectDatabase();

// Lấy danh sách danh mục và danh mục cha trong một truy vấn
$sql = "SELECT dm1.iddm, dm1.tendm, dm1.loaidm, dm1.icon, dm1.mota, 
               dm2.tendm AS tencha 
        FROM danhmucsp dm1 
        LEFT JOIN danhmucsp dm2 ON dm1.loaidm = dm2.iddm";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$danhmuc = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tạo danh sách danh mục cha từ danh sách danh mục có loaidm = 0
$danhmucCha = array_filter($danhmuc, fn($dm) => $dm['loaidm'] == 0);

// Đếm số lượng danh mục cha & con
$countCha = count($danhmucCha);
$countCon = count($danhmuc) - $countCha;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật danh mục</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { background-color: #f8f9fa; padding: 20px; }
        h2 { text-align: center; color: #007bff; margin-bottom: 20px; font-size: 24px; }
        
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .info p { font-size: 16px; color: #333; }
        .btn-back { background: #28a745; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-back:hover { background: #218838; }

        .table-container { overflow-x: auto; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #007bff; color: white; position: sticky; top: 0; }
        input, select { width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px; outline: none; }
        input:focus, select:focus { border-color: #007bff; box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); }
        
        .icon-preview { width: 40px; height: 40px; object-fit: contain; display: block; margin: auto; border-radius: 4px; }
        .btn-save { background: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-save:hover { background: #0056b3; }
        
        .file-input { display: flex; align-items: center; gap: 10px; }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    
        <h2>Danh sách danh mục</h2>
        <div class="info">
            <p>Số lượng danh mục cha: <strong><?= $countCha ?></strong></p>
            <p>Số lượng danh mục con: <strong><?= $countCon ?></strong></p>
            <button class="btn-back" onclick="goBack()"><i class="fas fa-arrow-left"></i> Trở về</button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th>Icon hiện tại</th>
                        <th>Icon mới</th>
                        <th>Mô tả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhmuc as $dm): ?>
                    <tr>
                        <form action="../config.php" method="POST" enctype="multipart/form-data">
                            <td><?= htmlspecialchars($dm['iddm']) ?></td>
                            <td><input type="text" name="tendm" value="<?= htmlspecialchars($dm['tendm']) ?>"></td>
                            <td>
                                <?php if ($dm['loaidm'] > 0): ?>
                                    <select name="loaidm">
                                        <option value="">Chọn danh mục cha</option>
                                        <?php foreach ($danhmucCha as $cha): ?>
                                            <option value="<?= $cha['iddm'] ?>" <?= ($cha['iddm'] == $dm['loaidm']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cha['tendm']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <i class="fas fa-folder" style="color: blue;"></i> Danh mục cha
                                <?php endif; ?>
                            </td>
                            <td>
                                <img class="icon-preview" src="../<?= htmlspecialchars($dm['icon']) ?>">
                                <input type="hidden" name="icon" value="<?= htmlspecialchars($dm['icon']) ?>">
                            </td>
                            <td class="file-input">
                                <input type="file" name="icon_new" accept="image/*" onchange="previewImage(this, 'preview-<?= $dm['iddm'] ?>')">
                                <img id="preview-<?= $dm['iddm'] ?>" src="../<?= htmlspecialchars($dm['icon']) ?>" class="icon-preview">
                            </td>
                            <td><input type="text" name="mota" value="<?= htmlspecialchars($dm['mota']) ?>"></td>
                            <td>
                                <input type="hidden" name="iddm" value="<?= htmlspecialchars($dm['iddm']) ?>">
                                
                                <input type="submit" name="capnhatdm" class="btn-save" value="&#xf0c7;" style="font-family: 'Font Awesome 5 Free'; font-weight: 900;">
                                
                                <input type="submit" name="xoadm" class="btn-delete" value="&#xf1f8;" style="font-family: 'Font Awesome 5 Free'; font-weight: 900;">
                            </td>

                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>

