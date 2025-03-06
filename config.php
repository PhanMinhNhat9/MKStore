<?php
    session_start();
require_once 'config.php';
function connectDatabase(): PDO {
    $host = "localhost";  
    $dbname = "quanlybanpk"; 
    $username = "root";   
    $password = "";    
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Hiển thị lỗi dưới dạng Exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Mặc định trả về dạng mảng kết hợp
            PDO::ATTR_EMULATE_PREPARES => false,  // Vô hiệu hóa giả lập prepared statements (bảo mật hơn)
            PDO::ATTR_PERSISTENT => true  // Kết nối bền vững (giảm thời gian kết nối lại)
        ];
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Lỗi kết nối CSDL: " . $e->getMessage()); // Không nên in lỗi ra ngoài thực tế, có thể log vào file
    }
}
// Đăng nhâp
function dangnhap($tendn, $matkhau) {
    if (empty($tendn) || empty($matkhau)) {
        return "Vui lòng nhập đầy đủ thông tin";
    }

    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM user WHERE tendn = :tendn LIMIT 1");
    $stmt->bindParam(':tendn', $tendn, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($matkhau, trim($user['matkhau']))) {
        $_SESSION['user'] = [
            'iduser' => $user['iduser'],
            'tendn' => $user['tendn'],
            'hoten' => $user['hoten'],
            'anh' => $user['anh'],
            'email' => $user['email'],
            'sdt' => $user['sdt'],
            'diachi' => $user['diachi'],
            'quyen' => $user['quyen']
        ];
        if ($_SESSION['user']['quyen']==0) {
            header("Location: trangchuadmin.html");
            exit();
        }
        else {
            header("Location: #");
            exit();
        }
    } else {
        return "Sai tài khoản hoặc mật khẩu";
    }
}

// Kiểm tra thông tin email + số điện thoại
function verifyUser($email, $sdt) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email AND sdt = :sdt LIMIT 1");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':sdt', $sdt, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Cập nhật mật khẩu mới
function updatePassword($iduser, $newPassword) {
    $pdo = connectDatabase();
    $hashPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE user SET matkhau = :matkhau WHERE iduser = :iduser");
    $stmt->bindParam(':matkhau', $hashPassword, PDO::PARAM_STR);
    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
    return $stmt->execute();
}
// Hàm lấy danh sách người dùng
function getAllUsers($iduser) {
    $pdo = connectDatabase();
    if ($iduser>0)
    {
        $sql = "SELECT iduser, hoten, tendn, anh, email, matkhau, sdt, diachi, quyen, thoigian FROM user 
            WHERE iduser=$iduser";
    } else {
        $sql = "SELECT iduser, hoten, tendn, anh, email, matkhau, sdt, diachi, quyen, thoigian FROM user";
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        // Lấy tất cả dữ liệu dưới dạng mảng kết hợp
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Xử lý lỗi nếu có
        die("Lỗi truy vấn: " . $e->getMessage());
    }
}
function themTK() {
    $pdo = connectDatabase();
    try {
        // Lấy dữ liệu từ form
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Mã hóa mật khẩu
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $role = $_POST['role'];
        
        // Xử lý file ảnh
        if (isset($_FILES['anh']) && $_FILES['anh']['error'] == 0) {
            $target_dir = "picture/";
            $target_file = $target_dir . basename($_FILES["anh"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Kiểm tra định dạng ảnh
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
            if (!in_array($imageFileType, $allowTypes)) {
                echo "Chỉ chấp nhận file ảnh định dạng JPG, PNG, JPEG, GIF.";
                return;
            }
            
            // Di chuyển file vào thư mục lưu trữ
            if (!move_uploaded_file($_FILES["anh"]["tmp_name"], $target_file)) {
                echo "Lỗi khi tải lên ảnh.";
                return;
            }
        } else {
            echo "Vui lòng chọn ảnh hợp lệ.";
            return;
        }

        // Chuẩn bị truy vấn SQL
        $sql = "INSERT INTO `user`(`hoten`, `tendn`, `anh`, `email`, `matkhau`, `sdt`, `diachi`, `quyen`)
                VALUES (:fullname, :username, :avatar, :email, :password, :phone, :address, :role )";
        
        // Thực hiện truy vấn
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':avatar', $target_file);
        
        if ($stmt->execute()) {
            echo "<script> alert('Thêm thành công!');
        window.location.replace('trangchuadmin.html?id=1');</script>";
        } else {
            echo "Lỗi khi thêm người dùng.";
        }
    } catch (PDOException $e) {
        echo "Lỗi kết nối CSDL: " . $e->getMessage();
    }
}

function capnhatTK($iduser, $hoten, $tendn, $email, $sdt, $diachi, $quyen, $file) {
    $pdo = connectDatabase(); // Kết nối PDO

    try {
        // Xử lý ảnh nếu có tải lên
        if (!empty($file['name'])) {
            $target_dir = "picture\\";
            $anh = $target_dir . basename($file["name"]);
            move_uploaded_file($file["tmp_name"], $anh);
            $anh = addslashes($anh);
        } else {
            // Nếu không có ảnh mới, lấy ảnh cũ từ DB
            $stmt = $pdo->prepare("SELECT anh FROM user WHERE iduser = ?");
            $stmt->execute([$iduser]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $anh = addslashes($row['anh']) ?? '';
        }

        // Câu lệnh UPDATE sử dụng prepared statement để chống SQL Injection
        $sql = "UPDATE user SET 
                hoten = :hoten, tendn = :tendn, email = :email, 
                sdt = :sdt, diachi = :diachi, quyen = :quyen, anh = :anh 
                WHERE iduser = :iduser";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':iduser'  => $iduser,
            ':hoten'   => $hoten,
            ':tendn'   => $tendn,
            ':email'   => $email,
            ':sdt'     => $sdt,
            ':diachi'  => $diachi,
            ':quyen'   => $quyen,
            ':anh'     => $anh
        ]);

        return true; // Trả về true nếu cập nhật thành công
    } catch (PDOException $e) {
        return "Lỗi cập nhật: " . $e->getMessage(); // Trả về lỗi nếu có
    }
}

function xoaTK($id) {
    $pdo = connectDatabase(); 

    try {
        $sql = "DELETE FROM user WHERE iduser = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Lỗi khi xóa tài khoản: " . $e->getMessage()); 
        return false;
    }
} 


?>
