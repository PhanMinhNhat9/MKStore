<?php
    include "config.php";
    if (!isset($_POST['verification_code'])) {
        echo "Lỗi xác thực!";
        exit;
    }
    $enteredCode = $_POST['verification_code'];
    $storedCode = $_SESSION['verification_code'] ?? null;
    $email = $_SESSION['email_temp'];
    $hoten = $_SESSION['hoten_temp'];
    $tendn = $_SESSION['tendn_temp'];
    $mk = password_hash($_SESSION['mk_temp'], PASSWORD_BCRYPT); // Mã hóa mật khẩu
    $sdt = $_SESSION['sdt_temp'];
    $diachi = $_SESSION['diachi_temp'];
    $anh = $_SESSION['anh_temp'];
    echo $enteredCode.'<br>';
    echo $storedCode;
    if ($enteredCode == $storedCode) {
        try {
            $pdo = connectDatabase(); // Kết nối CSDL
            // Xử lý upload ảnh đại diện (nếu có)
            $avatarPath = "picture/default.jpg"; // Ảnh mặc định
            if ($anh && $anh['size'] > 0) {
                $targetDir = "picture/";
                $fileName = $target_dir . basename($_FILES["anh"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                move_uploaded_file($anh["tmp_name"], $targetFilePath);
                $avatarPath = $targetFilePath;
            }

            // Câu lệnh SQL để lưu thông tin người dùng
            $sql = "INSERT INTO user (hoten, tendn, email, matkhau, sdt, diachi, anh, quyen) 
                    VALUES (:hoten, :tendn, :email, :matkhau, :sdt, :diachi, :anh, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':hoten' => $hoten,
                ':tendn' => $tendn,
                ':email' => $email,
                ':matkhau' => $mk,
                ':sdt' => $sdt,
                ':diachi' => $diachi,
                ':anh' => $avatarPath
            ]);

            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Xác thực thành công! Tài khoản đã được tạo.'); window.location.href='GUI&dangnhap.php';</script>";
            } else {
                echo "<script>alert('Lỗi khi lưu dữ liệu vào CSDL.'); window.location.href='giaodiendk.php';</script>";
            }

            $_SESSION['verification_code'] = '';
            $_SESSION['email_temp'] = '';
            $_SESSION['hoten_temp'] = '';
            $_SESSION['tendn_temp'] = '';
            $_SESSION['mk_temp'] = '';
            $_SESSION['sdt_temp'] = '';
            $_SESSION['diachi_temp'] = '';
            $_SESSION['anh_temp'] = '';

        } catch(PDOException $e) {
            echo "Lỗi khi lưu dữ liệu: " . $e->getMessage();
            // echo "<script>alert('Lỗi khi lưu dữ liệu vào CSDL.'); window.location.href='giaodiendk.php';</script>";
        }
    } else {
        echo "Mã xác thực không đúng!";
    }
?>
