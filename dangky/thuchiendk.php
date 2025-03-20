<?php
    include "../config.php";
    // if (!isset($_POST['verification_code'])) {
    //     echo "Lỗi xác thực!";
    //     exit;
    // }
    // $enteredCode = $_POST['verification_code'];
    // $storedCode = $_SESSION['verification_code'] ?? null;
    // $email = $_SESSION['email_temp'];
    // $hoten = $_SESSION['hoten_temp'];
    // $tendn = $_SESSION['tendn_temp'];
    // $mk = password_hash($_SESSION['mk_temp'], PASSWORD_BCRYPT); // Mã hóa mật khẩu
    // $sdt = $_SESSION['sdt_temp'];
    // $diachi = $_SESSION['diachi_temp'];
    // $anh = $_SESSION['anh_temp'];
    // if ($enteredCode != $storedCode) {
        // try {
        $anh = $_FILES['anh'];
            $pdo = connectDatabase(); // Kết nối CSDL
            if (!empty($anh['name'])) {
                $target_dir = "../picture/";
                $target_file = $target_dir . basename($anh["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                echo $target_file;
                
                
                // Kiểm tra định dạng ảnh
                $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
                if (!in_array($imageFileType, $allowTypes)) {
                    echo "Chỉ chấp nhận file ảnh định dạng JPG, PNG, JPEG, GIF.";
                    return;
                }
                
                // Di chuyển file vào thư mục lưu trữ
                if (!move_uploaded_file($anh["tmp_name"], $target_file)) {
                    echo "Lỗi khi tải lên ảnh.";
                    return;
                }
            }

            // // Câu lệnh SQL để lưu thông tin người dùng
            // $sql = "INSERT INTO user (hoten, tendn, email, matkhau, sdt, diachi, anh, quyen) 
            //         VALUES (:hoten, :tendn, :email, :matkhau, :sdt, :diachi, :anh, 1)";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([
            //     ':hoten' => $hoten,
            //     ':tendn' => $tendn,
            //     ':email' => $email,
            //     ':matkhau' => $mk,
            //     ':sdt' => $sdt,
            //     ':diachi' => $diachi,
            //     ':anh' => $avatarPath
            // ]);

            // if ($stmt->rowCount() > 0) {
            //     //echo "<script>alert('Xác thực thành công! Tài khoản đã được tạo.'); window.location.href='../GUI&dangnhap.php';</script>";
            // } else {
            //     echo "<script>alert('Lỗi khi lưu dữ liệu vào CSDL.'); window.location.href='giaodiendk.php';</script>";
            // }

            // unset($_SESSION['verification_code']);
            // unset($_SESSION['email_temp']);
            // unset($_SESSION['hoten_temp']);
            // unset($_SESSION['tendn_temp']);
            // unset($_SESSION['mk_temp']);
            // unset($_SESSION['sdt_temp']);
            // unset($_SESSION['diachi_temp']);
            // unset($_SESSION['anh_temp']);

    //     } catch(PDOException $e) {
    //         echo "Lỗi khi lưu dữ liệu: " . $e->getMessage();
    //     }
    // } else {
    //     echo "Mã xác thực không đúng!";
    // }
?>
