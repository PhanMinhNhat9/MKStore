<?php
require_once 'config.php';
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
    <title>Danh Sách Người Dùng</title>
    <!-- Thêm thư viện Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .table-container {
            max-height: 395px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
            position: sticky;
            top: 0;
        }
        .status.complete {
            color: green;
        }
        .status.pending {
            color: red;
        }
        /* Định dạng icon */
        .action-icon {
            display: inline-block;
            padding: 8px;
            border-radius: 5px;
            font-size: 18px;
            color: white;
            transition: all 0.3s ease-in-out;
            margin-right: 5px;
            text-align: center;
        }
        .action-icon.edit {
            color: #28a745;
        }
        .action-icon.delete {
            color: #dc3545;
        }
        .action-icon:hover {
            opacity: 0.7;
            transform: scale(1.1);
        }
        /* Căn giữa nội dung */
        td img {
            display: block;
            margin: auto;
            border-radius: 50%;
        }
        td {
            vertical-align: middle;
            text-align: center;
        }
        .themnguoidung {
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
        .themnguoidung:hover {
            background-color: #007BFF;
            color: white;
        }
        
    </style>
</head>
<body>
<button class="themnguoidung"><i class="fas fa-plus-circle"></i> Thêm người dùng</button>
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
                            <a href="LoadUpdateUser.php?id=<?= $user['iduser'] ?>" class="action-icon edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_user.php?id=<?= $user['iduser'] ?>" class="action-icon delete" onclick="return confirm('Bạn có chắc muốn xóa?');">
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
