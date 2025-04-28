<?php
require_once '../config.php';
$pdo = connectDatabase();

$iduser = $_SESSION['user']['iduser'];

// Câu SQL cần lấy tất cả thông tin user
$sql = "SELECT `iduser`, `hoten`, `tendn`, `anh`, `email`, `matkhau`, `sdt`, `diachi`, `quyen`, `thoigian`
        FROM `user`
        WHERE `iduser` = :iduser";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT); // bảo vệ kiểu dữ liệu
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC); // Lấy 1 dòng dữ liệu

$sql = "SELECT iduser FROM khxoatk WHERE iduser = :iduser";
$ktstmt = $pdo->prepare($sql);
$ktstmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
$ktstmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <style>
        /* CSS Variables */
:root {
    --primary-color: #3182ce;
    --success-color: #38a169;
    --danger-color: #e53e3e;
    --sidebar-bg: #2d3748;
    --text-color: #2d3748;
    --card-bg: #ffffff;
    --border-color: #e2e8f0;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --spacing: 16px;
    --border-radius: 8px;
    --font-size-base: 14px;
    --transition: all 0.3s ease;
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 60px;
}

/* Reset */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    display: flex;
    min-height: 100vh;
    background-color: #f4f6f8;
    color: var(--text-color);
    font-size: var(--font-size-base);
}
        .profile-container {
    display: flex;
    gap: 20px;
    padding: 20px;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f4f4;
    flex-wrap: wrap;    

}

.profile-section {
    flex: 1 1 300px; /* Chiều rộng tối thiểu là 300px, tự co dãn nếu đủ chỗ */
    background-color: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
}


.profile-section h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 5px;
}

.edit-btn {
    background-color: transparent;
    border: none;
    cursor: pointer;
    color: #007bff;
    font-size: 16px;
    transition: color 0.3s;
    padding-left: 48%;
}

.edit-btn:hover {
    color: #0056b3;
}

.profile-grid {

    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px 20px;
}

.profile-grid label {
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
}

.profile-grid input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f8f8f8;
    color: #333;
}

.profile-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 40px;
}

.profile-avatar img {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.KHbtn {
    padding: 8px 14px;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
}

.KHbtn-update {
    background-color: #007bff;
}

.KHbtn-delete {
    background-color: #dc3545;
}
.custom-file-upload {
    margin-top: 10px;
    position: relative;
    display: inline-block;
}

.custom-file-upload input[type="file"] {
    display: none;
}

.custom-file-upload label {
    display: inline-block;
    padding: 8px 15px;
    background-color: #17a2b8;
    color: #fff;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-bottom: 20px;
}

.custom-file-upload label:hover {
    background-color: #138496;
}

.profile-grid-wrapper {
    overflow-y: auto;  /* Cho phép cuộn ngang */
    -webkit-overflow-scrolling: touch;  /* Thêm hỗ trợ cuộn mượt trên thiết bị di động */
    height: 100vh;
    margin: auto;
}

/* Media Queries for Mobile Devices */
@media (max-width: 768px) {
    .profile-container {
        flex-direction: column;  /* Đặt các phần tử trong .profile-container theo chiều dọc */
        padding: 10px;
    }

    .profile-section {
        flex: 1 1 100%;  /* Chiều rộng 100% cho các phần tử */
        margin-bottom: 15px;
    }

    .profile-avatar img {
        width: 120px;
        height: 120px;  /* Giảm kích thước ảnh đại diện */
    }

    .edit-btn {
        padding-left: 20px; /* Căn chỉnh lại nút chỉnh sửa */
    }

    .profile-grid {
        grid-template-columns: 1fr;  /* Đặt 1 cột duy nhất cho các trường trong grid */
        gap: 10px;
    }

    .profile-grid input {
        padding: 10px;
    }

    .custom-file-upload label {
        padding: 8px 12px;
    }
}
    </style>
</head>
<body>
<div class="profile-grid-wrapper"> 
    <main class="profile-container">
       
            <!-- Ảnh đại diện -->
            <section class="profile-section">
                <h3>Ảnh đại diện</h3>
                <div class="profile-avatar">
                    <img 
                        src="../<?= htmlspecialchars($user['anh']) ?>" 
                        alt="Ảnh đại diện" 
                    >
                </div>

                <!-- Input file để chọn ảnh mới -->
                <div class="custom-file-upload">
                    <label for="anh_moi" 
                        onclick="openModal('../<?= htmlspecialchars($user['anh']) ?>', <?= $user['iduser'] ?>)"
                    >
                        <i class="fas fa-image"></i> Chọn ảnh mới
                    </label>
                </div>

                <!-- Nút hành động -->
                <div style="margin-top: 10px; display: flex; gap: 10px;">
                    <button type="submit" name="capnhat_anh" class="KHbtn KHbtn-update">
                        <i class="fas fa-upload"></i> Cập nhật ảnh
                    </button>
                    <button type="submit" name="xoa_anh" class="KHbtn KHbtn-delete" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                        <i class="fas fa-trash-alt"></i> Xóa ảnh
                    </button>
                </div>
            </section>
            <!-- Thông tin cá nhân -->
            <section class="profile-section">
                <h3>Thông tin cá nhân             
                    <button class="edit-btn" onclick="capnhatnguoidung(<?= $user['iduser'] ?>)">
                        <i class="fas fa-pen"></i>
                    </button>
                </h3>
                <div class="profile-grid">
                    <div><label>ID User</label><input type="text" value="<?= htmlspecialchars($user['iduser']) ?>" disabled></div>
                    <div><label>Họ tên</label><input type="text" value="<?= htmlspecialchars($user['hoten']) ?>" disabled></div>
                    <div><label>Địa chỉ</label><input type="text" value="<?= htmlspecialchars($user['diachi']) ?>" disabled></div>
                    <div><label>Email</label><input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
                    <div><label>SDT</label><input type="text" value="<?= htmlspecialchars($user['sdt']) ?>" disabled></div>
                    <?php if ($ktstmt->rowCount() === 0): ?>
                    <div><label>Trạng thái</label><input type="text" value="Đang hoạt động" disabled></div>
                    <?php else: ?>
                        <div><label>Trạng thái</label><input type="text" value="Tạm ngừng hoạt động" disabled></div>
                        <?php endif; ?>
                </div>
            </section>
            <!-- Thông tin lớp chuyên ngành -->
            <section class="profile-section">
                <h3>Thông tin tài khoản</h3>
                <div class="profile-grid">
                    <div><label>Tên đăng nhập:</label><input type="text" value="<?= htmlspecialchars($user['tendn']) ?>" disabled></div>
                    <div><label>Mật khẩu:</label><input type="password" value="<?=$user['matkhau']?>" disabled></div>
                </div>
            </section>

    </main>
</div>
</body>
</html>
