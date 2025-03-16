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
    <link rel="stylesheet" href="capnhatdanhmuc.css">
</head>
<body>
        <h2>Danh sách danh mục</h2>
        <div class="info">
            <p><strong>Số lượng danh mục cha: <?= $countCha ?></strong></p>
            <p><strong>Số lượng danh mục con: <?= $countCon ?></strong></p>
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
                                <input type="submit" name="capnhatdm" class="btn-save" value="&#xf0c7;" style="font-family: 'Font Awesome 5 Free'; font-weight: 900;"> <br> <br>
                                <input type="submit" name="xoadm" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');" class="btn-delete" value="&#xf1f8;" style="font-family: 'Font Awesome 5 Free'; font-weight: 900;">
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

