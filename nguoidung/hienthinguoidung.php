<?php
require_once '../config.php';
$pdo = connectDatabase();
$sql = "SELECT iduser, hoten, tendn, anh, email, matkhau, sdt, diachi, quyen, thoigian FROM user";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <button onclick="themnguoidung()" class="themnguoidung"><i class="fas fa-plus-circle"></i> Thêm người dùng</button>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ Tên</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Ảnh</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Địa Chỉ</th>
                    <th>Quyền</th>
                    <th>Thời Gian</th>
                    <th>Hành Động</th>
                </tr>  
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['iduser'] ?></td>
                        <td><?= $user['hoten'] ?></td>
                        <td><?= $user['tendn'] ?></td>
                        <td><img src="<?= $user['anh'] ?>" width="30" height="30"></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['sdt'] ?></td>
                        <td><?= $user['diachi'] ?></td>
                        <td><?= $user['quyen'] ?></td>
                        <td><?= $user['thoigian'] ?></td>
                        <td>
                            <a href="nguoidung/capnhatnguoidung.php?id=<?= $user['iduser'] ?>" class="action-icon edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="nguoidung/xoanguoidung.php?id=<?= $user['iduser'] ?>" class="action-icon delete" onclick="return confirm('Bạn có chắc muốn xóa?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
