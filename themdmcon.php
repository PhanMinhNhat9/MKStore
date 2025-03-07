<?php
require_once 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $loaidm = $_POST['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục con</title>
</head>
<body>
    <h2>Thêm danh mục con</h2>
    <form action="vd.php" method="POST" enctype="multipart/form-data">
        <label for="tendm">Tên danh mục:</label>
        <input type="text" id="tendm" name="tendm" required>
        <br>
        <label for="loaidm">Loại danh mục:</label>
        <input type="text" id="loaidm" name="loaidm" required value="<?php echo htmlspecialchars($loaidm); ?>">
        <br>
        <label for="icon">Icon URL:</label>
        <input type="file" id="icon" name="icon">
        <br>
        <label for="mota">Mô tả:</label>
        <textarea id="mota" name="mota"></textarea>
        <br>
        <input type="submit" name="themdmcon" value="Thêm">
    </form>
</body>
</html>
