<?php
    // Cấu hình session an toàn
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
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
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }
    
    function dangnhap($tendn, $matkhau) {
        if (empty($tendn) || empty($matkhau)) {
            return "Vui lòng nhập đầy đủ thông tin";
        }
        $pdo = connectDatabase();
        if (!isset($_SESSION['last_attempt_time'])) {
            $_SESSION['last_attempt_time'] = time();
        }
        // Nếu đã quá 10 phút từ lần nhập sai đầu tiên, reset lại số lần nhập
        if (time() - $_SESSION['last_attempt_time'] > 10) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();
        }
       
        if ($_SESSION['login_attempts'] >= 2 && time() < $_SESSION['lock_time']) { 
            return "Bạn đã nhập sai quá nhiều lần, hãy thử lại sau 2 phút.";
        }
       
        $stmt = $pdo->prepare("SELECT * FROM user WHERE tendn = :tendn LIMIT 1");
        $stmt->bindParam(':tendn', $tendn, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($matkhau, trim($user['matkhau']))) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lock_time'] = 0; // Reset thời gian khóa
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
            
            if ($_SESSION['user']['quyen']==0 || $_SESSION['user']['quyen']==2589) {
                header("Location: trangchuadmin.php");
                exit();
            }
            else {           
                header("Location: trangchunguoidung.php");
                exit();
            }
            
        } else {
            $_SESSION['login_attempts'] = $_SESSION['login_attempts'] + 1;
            $_SESSION['last_attempt_time'] = time();
            if ($_SESSION['login_attempts'] >= 2) {
                $_SESSION['lock_time'] = time() + 10; // Khóa trong 2 phút
            }
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
    
    require 'PHPMailer-master\src\Exception.php';
    require 'PHPMailer-master\src\PHPMailer.php';
    require 'PHPMailer-master\src\SMTP.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    function sendVerificationEmail($email, $verificationCode) {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8'; // Đảm bảo email sử dụng UTF-8
            $mail->Host = 'smtp.gmail.com'; // SMTP của Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'nguyentuanand2589@gmail.com'; // Thay bằng email của bạn
            $mail->Password = 'ykrq borr osxw urtl'; // Thay bằng mật khẩu ứng dụng của Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            // Cấu hình người gửi và người nhận
            $mail->setFrom('nguyentuanand2589@gmail.com', 'Hệ thống xác thực');
            $mail->addAddress($email);
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Xác thực tài khoản của bạn';
            $mail->Body    = "
                                <h3>Chào mừng bạn đến với hệ thống của chúng tôi!</h3>
                                <p>Vui lòng nhập mã xác thực sau để hoàn tất đăng ký:</p>
                                <h2>$verificationCode</h2>
                                <p>Nếu bạn không yêu cầu đăng ký, vui lòng bỏ qua email này.</p>
                            ";
            // Gửi email
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
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

    function themNguoiDung($fullname, $username, $email, $password, $phone, $address, $role, $target_file) {
        $pdo = connectDatabase();
        try {
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
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    function capnhatNguoiDung($iduser, $hoten, $tendn, $email, $sdt, $diachi, $quyen, $matkhau) {
        $pdo = connectDatabase(); // Kết nối PDO
        try {
            $sql = "UPDATE user SET 
                    hoten = :hoten, tendn = :tendn, email = :email, 
                    sdt = :sdt, diachi = :diachi, quyen = :quyen";
            $params = [
                ':iduser'  => $iduser,
                ':hoten'   => $hoten,
                ':tendn'   => $tendn,
                ':email'   => $email,
                ':sdt'     => $sdt,
                ':diachi'  => $diachi,
                ':quyen'   => $quyen,
            ];
            if (!empty($matkhau)) {
                $sql .= ", matkhau = :matkhau"; 
                $params[':matkhau'] = $matkhau; 
            }
            $sql .= " WHERE iduser = :iduser";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    function xoaNguoiDung($id) {
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

    function capnhatAnhUser() {
        $pdo = connectDatabase();
        $iduser = intval($_POST['iduser']);
        // File tồn tại và không có lỗi
        $file = $_FILES['fileInput'];
        // // Xử lý ảnh nếu có tải lên
        if (!empty($file['name'])) {
            $td = "../";
            $target_dir = "picture/";
            $anh = $td . $target_dir . basename($file["name"]);
            move_uploaded_file($file["tmp_name"], $anh);
            $anh = addslashes($target_dir . basename($file["name"]));
        } 
        else {
            // Nếu không có ảnh mới, lấy ảnh cũ từ DB
            $stmt = $pdo->prepare("SELECT anh FROM user WHERE iduser = ?");
            $stmt->execute([$iduser]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $anh = addslashes($row['anh']) ?? '';
        }
        $updateSql = "UPDATE user SET anh = :anh WHERE iduser = :iduser";
        $stmt = $pdo->prepare($updateSql);
        $success = $stmt->execute([
            'anh' => $anh,
            'iduser' => $iduser
        ]);
        if ($success) {
            return true;
        } else {
            return false;
        }
    }

    function capnhatSanPham() {
        $pdo = connectDatabase();

        $idsp = intval($_POST["idsp"]);
        $tensp = htmlspecialchars($_POST["tensp"]);
        $mota = htmlspecialchars($_POST["mota"]);
        $giaban = floatval($_POST["giaban"]);
        $soluong = intval($_POST["soluong"]);
        $iddm = intval($_POST["iddm"]);
        $file = $_FILES['anh'];
        // // Xử lý ảnh nếu có tải lên
        if (!empty($file['name'])) {
            $td = "../";
            $target_dir = "picture/";
            $anh = $td . $target_dir . basename($file["name"]);
            move_uploaded_file($file["tmp_name"], $anh);
            $anh = addslashes($target_dir . basename($file["name"]));
            $sql = "UPDATE sanpham SET tensp = :tensp, mota = :mota, giaban = :giaban, soluong = :soluong, anh = :anh, iddm = :iddm WHERE idsp = :idsp";
        } else {
            $sql = "UPDATE sanpham SET tensp = :tensp, mota = :mota, giaban = :giaban, soluong = :soluong, iddm = :iddm WHERE idsp = :idsp";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tensp', $tensp, PDO::PARAM_STR);
        $stmt->bindParam(':mota', $mota, PDO::PARAM_STR);
        $stmt->bindParam(':giaban', $giaban, PDO::PARAM_STR);
        $stmt->bindParam(':soluong', $soluong, PDO::PARAM_INT);
        $stmt->bindParam(':iddm', $iddm, PDO::PARAM_INT);
        $stmt->bindParam(':idsp', $idsp, PDO::PARAM_INT);
        if (!empty($_FILES["anh"]["name"])) {
            $stmt->bindParam(':anh', $anh, PDO::PARAM_STR);
        }
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    function themSanPham() {
        $tensp = $_POST['tensp'];
        $mota = $_POST['mota'];
        $giaban = $_POST['giaban'];
        $soluong = $_POST['soluong'];

        $iddm = $_POST['iddm'];

        $file = $_FILES['anh'];
        // // Xử lý ảnh nếu có tải lên
        if (!empty($file['name'])) {
            $td = "../";
            $target_dir = "picture/";
            $anh = $td . $target_dir . basename($file["name"]);
            move_uploaded_file($file["tmp_name"], $anh);
            $anh = addslashes($target_dir . basename($file["name"]));
        } 
        // Thêm vào database
        try {
            $pdo = connectDatabase();
            $stmt = $pdo->prepare("INSERT INTO sanpham (tensp, mota, giaban, soluong, anh, iddm) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tensp, $mota, $giaban, $soluong, $anh, $iddm]);
            if ($stmt->rowcount()>0) {
                $idsp = $pdo->lastInsertId();
                if (taoMaQR($idsp)) {
                    $kq = themMaQR($idsp);
                    if ($kq) return true; else return false;
                } else return false;
                
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
            return false;
        }
    }
    function taoMaQR($idsp) {
        require_once 'phpqrcode-master/qrlib.php'; // Import thư viện phpqrcode
        $td = "../";
        // Tạo thư mục lưu QR nếu chưa có
        $qrDir = $td."qrcodes/";
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0777, true);
        }

        $file = $qrDir . "sp_$idsp.png"; // Tạo đường dẫn lưu QR

        // Kiểm tra nếu chưa có thì tạo mới
        if (!file_exists($file)) {
            QRcode::png($idsp, $file, QR_ECLEVEL_L, 5);
        }

        // Kiểm tra lại xem file QR đã được tạo chưa
        if (file_exists($file)) {
            return true;
        } else {
            return false;
        }
    }
    function themMaQR($idsp) {
        try {
            $pdo = connectDatabase(); // Kết nối CSDL
            $qrcode = "sp_$idsp.png"; // Tên file QR (đã có sẵn)
    
            // Kiểm tra nếu idsp đã tồn tại trong bảng qrcode
            $checkQuery = "SELECT idsp FROM qrcode WHERE idsp = ?";
            $stmt = $pdo->prepare($checkQuery);
            $stmt->execute([$idsp]);
    
            if ($stmt->rowCount() == 0) {
                // Chèn dữ liệu vào bảng qrcode
                $insertQuery = "INSERT INTO qrcode (qrcode, idsp) VALUES (?, ?)";
                $stmt = $pdo->prepare($insertQuery);
                $stmt->execute([$qrcode, $idsp]);
    
                if ($stmt->rowCount() > 0) {
                    return true; // Chèn thành công
                } else {
                    return false; // Chèn thất bại
                }
            } else {
                return true; // Đã tồn tại, không cần thêm nữa nhưng vẫn coi là thành công
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
            return false;
        }
    }
    
    function themDMcon() {
        $pdo = connectDatabase(); 
        $tendm = trim($_POST['tendm'] ?? '');
        $mota = trim($_POST['mota'] ?? '');
        $loaidm = trim($_POST['loaidm'] ?? '');
    
        // Kiểm tra dữ liệu đầu vào
        if (empty($tendm) || empty($loaidm)) {
            return ['success' => false, 'message' => 'Tên danh mục và loại danh mục không được để trống!'];
        }
    
        // Xử lý upload icon
        $icon = '';
        if (!empty($_FILES['icon']['name'])) {
            $target_dir = "icon/";
            $file_name = basename($_FILES["icon"]["name"]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_ext, $allowed_types)) {
                return ['success' => false, 'message' => 'Chỉ chấp nhận các file JPG, JPEG, PNG, GIF!'];
            }
            $target_file = $target_dir . $file_name;
            if (!move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file)) {
                return ['success' => false, 'message' => 'Lỗi khi tải lên tệp!'];
            }
            $icon = $target_file;
        }
    
        try {
            $stmt = $pdo->prepare("INSERT INTO danhmucsp (tendm, loaidm, icon, mota) VALUES (:tendm, :loaidm, :icon, :mota)");
            $stmt->execute([
                'tendm' => $tendm,
                'loaidm' => $loaidm,
                'icon' => $icon,
                'mota' => $mota
            ]);
    
            // Trả về thành công
            return ['success' => true, 'message' => 'Thêm danh mục thành công!'];
        } catch (PDOException $e) {
            // Trả về lỗi PDO
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }
    
    function capnhatDanhMuc() {
        $pdo = connectDatabase();
        $iddm = $_POST['iddm'];
        $tendm = $_POST['tendm'];
        $loaidm = $_POST['loaidm'] ?? 0; 
        $mota = $_POST['mota'];
        $icon = $_POST['icon']; 

        if (!empty($_FILES['icon_new']['name'])) {
            $targetDir = "icon/";
            $fileName = basename($_FILES["icon_new"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Chỉ cho phép các định dạng ảnh hợp lệ
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($_FILES["icon_new"]["tmp_name"], $targetFilePath)) {
                    $icon = "icon/" . $fileName; // Lưu đường dẫn ảnh mới vào DB
                }
            }
        }

        // Cập nhật dữ liệu vào cơ sở dữ liệu
        $sql = "UPDATE danhmucsp SET tendm = :tendm, loaidm = :loaidm, icon = :icon, mota = :mota WHERE iddm = :iddm";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tendm', $tendm);
        $stmt->bindParam(':loaidm', $loaidm);
        $stmt->bindParam(':icon', $icon);
        $stmt->bindParam(':mota', $mota);
        $stmt->bindParam(':iddm', $iddm);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    function xoaDanhMuc() {
        $pdo = connectDatabase();
    
            $iddm = $_POST['iddm'];
    
            // Kiểm tra xem danh mục có danh mục con không
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM danhmucsp WHERE loaidm = ?");
            $stmtCheck->execute([$iddm]);
            $count = $stmtCheck->fetchColumn();
    
            if ($count > 0) {
                echo "<script>alert('Không thể xóa! Hãy xóa danh mục con trước.');</script>";
                return;
            }
    
            // Xóa danh mục
            $stmt = $pdo->prepare("DELETE FROM danhmucsp WHERE iddm = ?");
            if ($stmt->execute([$iddm])) {
                return true;
            } else {
                return false;
            }
        }
    function themMGG() {
        $pdo = connectDatabase();
        $code = trim($_POST['code']);
        $phantram = (int) $_POST['phantram'];
        $ngayhieuluc = $_POST['ngayhieuluc'];
        $ngayketthuc = $_POST['ngayketthuc'];

        if (empty($code) || empty($phantram) || empty($ngayhieuluc) || empty($ngayketthuc)) {
            die("Vui lòng nhập đầy đủ thông tin!");
        }

        if ($phantram <= 0 || $phantram > 100) {
            die("Phần trăm giảm phải từ 1 đến 100!");
        }

        try {
            $sql = "INSERT INTO magiamgia (code, phantram, ngayhieuluc, ngayketthuc) VALUES (:code, :phantram, :ngayhieuluc, :ngayketthuc)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->bindParam(':phantram', $phantram, PDO::PARAM_INT);
            $stmt->bindParam(':ngayhieuluc', $ngayhieuluc, PDO::PARAM_STR);
            $stmt->bindParam(':ngayketthuc', $ngayketthuc, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
    function capnhatMGG($code, $idmgg, $phantram, $ngayhieuluc, $ngayketthuc, $giaapdung, $soluong, $iddm) {
        $pdo = connectDatabase();
        $stmt = $pdo->prepare("UPDATE magiamgia 
            SET code = :code, phantram = :phantram, ngayhieuluc = :ngayhieuluc, 
                ngayketthuc = :ngayketthuc, giaapdung = :giaapdung, 
                iddm = :iddm, soluong = :soluong 
            WHERE idmgg = :idmgg");

        $stmt->execute([
            'code' => $code,
            'phantram' => $phantram,
            'ngayhieuluc' => $ngayhieuluc,
            'ngayketthuc' => $ngayketthuc,
            'giaapdung' => $giaapdung,
            'iddm' => $iddm,
            'soluong' => $soluong,
            'idmgg' => $idmgg
        ]);
        if ($stmt->rowCount() > 0 || $stmt->errorCode() == '00000') {
            return true;
        } else 
            return false;
        
    }
?>
