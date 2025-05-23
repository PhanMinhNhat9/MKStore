<?php
    // Cấu hình session an toàn
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
    if (isset($_POST['contactInfo']))
    {
        $_SESSION['user'] = [
            'tele' => $_POST['contactInfo']
        ];
    }

    function connectDatabase(): PDO {
        $host = "localhost";  
        $dbname = "u797172436_qua"; 
        $username = "u797172436_qua";   
        $password = "Nhta@22004335";    
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
    // Khởi tạo biến session nếu chưa có
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if (!isset($_SESSION['lock_time']) || !is_numeric($_SESSION['lock_time'])) {
        $_SESSION['lock_time'] = 0;
    }

    function dangnhap($tendn, $matkhau) {
        $pdo = connectDatabase(); 

        if (empty($tendn) || empty($matkhau)) {
            return "Vui lòng nhập đầy đủ thông tin";
        }
       
        if ($_SESSION['login_attempts'] >= 2 && time() < $_SESSION['lock_time']) { 
            return "Bạn đã nhập sai quá nhiều lần, hãy thử lại sau 10 giây.";
        }
       
        $stmt = $pdo->prepare("SELECT * FROM user WHERE tendn = :tendn LIMIT 1");
        $stmt->bindParam(':tendn', $tendn, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($matkhau, trim($user['matkhau']))) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lock_time'] = 0; 
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
            
            if (in_array($_SESSION['user']['quyen'], [0, 1, 2589])) {
                header("Location: ../trangchu.php");
                exit();
            } 

        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 2) {
                $_SESSION['lock_time'] = time() + 10;
                $_SESSION['login_attempts'] = 0;
                return "Bạn đã nhập sai quá nhiều lần, hãy thử lại sau 10 giấy.";
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
    
    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';
    
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
            $mail->Username = 'monkeystore.hotro.4335@gmail.com'; // Email của bạn
            $mail->Password = 'ofkv yzxx ovkt jgqw'; // Mật khẩu ứng dụng Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Cấu hình người gửi và người nhận
            $mail->setFrom('monkeystore.hotro.4335@gmail.com', 'MonkeyStore Support');
            $mail->addAddress($email);
            $mail->addReplyTo('monkeystore.hotro.4335@gmail.com', 'MonkeyStore Support');
    
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Xác nhận đăng ký tài khoản MonkeyStore';
            $mail->Body = '
                <!DOCTYPE html>
                <html lang="vi">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Xác nhận đăng ký</title>
                    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
                    <style>
                        body {
                            font-family: "Poppins", sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                            color: #333;
                        }
                        .container {
                            background-color: #fff;
                            max-width: 600px;
                            margin: 30px auto;
                            padding: 30px;
                            border-radius: 10px;
                            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                        }
                        h3 {
                            color: #2d89ef;
                        }
                        .code-box {
                            margin: 20px 0;
                            padding: 15px;
                            background-color: #e8f0fe;
                            border-left: 6px solid #2d89ef;
                            font-size: 24px;
                            font-weight: bold;
                            color: #2d89ef;
                            text-align: center;
                            border-radius: 6px;
                        }
                        .footer {
                            font-size: 12px;
                            color: #777;
                            margin-top: 30px;
                            border-top: 1px solid #ddd;
                            padding-top: 15px;
                            text-align: center;
                        }
                        a {
                            color: #2d89ef;
                            text-decoration: none;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h3>Chào bạn,</h3>
                        <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>MonkeyStore</strong>! Vui lòng sử dụng mã xác nhận dưới đây để hoàn tất quá trình đăng ký:</p>
                        <div class="code-box">' . htmlspecialchars($verificationCode) . '</div>
                        <p>Mã xác nhận chỉ có hiệu lực trong vòng <strong>5 phút</strong>. Nếu bạn không yêu cầu đăng ký, bạn có thể bỏ qua email này.</p>
                        <p>Nếu cần hỗ trợ, hãy liên hệ chúng tôi qua email: <a href="mailto:monkeystore.hotro.4335@gmail.com">monkeystore.hotro.4335@gmail.com</a></p>
                        <div class="footer">
                            <p>Trân trọng,<br>
                            Đội ngũ MonkeyStore<br>
                            Website: <a href="https://monkeystore.com">monkeystore.com</a></p>
                            <p>Bạn nhận được email này vì đã thực hiện đăng ký tại MonkeyStore.</p>
                        </div>
                    </div>
                </body>
                </html>
                ';    
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
            $sql = "UPDATE sanpham SET tensp = :tensp, giaban = :giaban, soluong = :soluong, anh = :anh, iddm = :iddm WHERE idsp = :idsp";
        } else {
            $sql = "UPDATE sanpham SET tensp = :tensp, giaban = :giaban, soluong = :soluong, iddm = :iddm WHERE idsp = :idsp";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tensp', $tensp, PDO::PARAM_STR);
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
            $stmt = $pdo->prepare("INSERT INTO sanpham (tensp, giaban, soluong, anh, iddm) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tensp, $giaban, $soluong, $anh, $iddm]);
            if ($stmt->rowcount()>0) {
                $idsp = $pdo->lastInsertId();
                if (taoMaQR($idsp)) {
                    $kq = themMaQR($idsp);
                    if ($kq) return true; 
                    else 
                        return false;
                } else 
                    return false;
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
    
    function themDMcon($tendm, $loaidm, $mota, $icon) {
        try {
            // Kết nối cơ sở dữ liệu
            $pdo = connectDatabase();
            $icon = str_replace("../", "", $icon);

            // Kiểm tra xem tên danh mục đã tồn tại trong cùng loaidm hay chưa
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM danhmucsp WHERE tendm = :tendm AND loaidm = :loaidm");
            $stmt_check->execute(['tendm' => $tendm, 'loaidm' => $loaidm]);
            if ($stmt_check->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'Tên danh mục đã tồn tại trong loại danh mục này.'
                ];
            }

            // Chuẩn bị câu lệnh SQL để thêm danh mục con
            $sql = "INSERT INTO danhmucsp (tendm, loaidm, mota, icon, thoigian) 
                    VALUES (:tendm, :loaidm, :mota, :icon, NOW())";
            $stmt = $pdo->prepare($sql);

            // Gán giá trị cho các tham số
            $stmt->bindValue(':tendm', $tendm, PDO::PARAM_STR);
            $stmt->bindValue(':loaidm', $loaidm, PDO::PARAM_INT);
            $stmt->bindValue(':mota', $mota ? $mota : null, PDO::PARAM_STR);
            $stmt->bindValue(':icon', $icon ? $icon : null, PDO::PARAM_STR);

            // Thực thi câu lệnh
            $stmt->execute();

            // Trả về kết quả thành công
            return [
                'success' => true,
                'message' => 'Thêm danh mục con thành công.'
            ];
        } catch (PDOException $e) {
            // Xử lý lỗi nếu có
            return [
                'success' => false,
                'message' => 'Lỗi khi thêm danh mục: ' . $e->getMessage()
            ];
        }
    }
    
    function capnhatDanhMuc($iddm, $tendm, $loaidm, $mota, $icon) {
    try {
        $pdo = connectDatabase();

        // Kiểm tra tên danh mục trùng lặp
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM danhmucsp WHERE tendm = :tendm AND loaidm = :loaidm AND iddm != :iddm");
        $stmt_check->execute(['tendm' => $tendm, 'loaidm' => $loaidm, 'iddm' => $iddm]);
        if ($stmt_check->fetchColumn() > 0) {
            return [
                'success' => false,
                'message' => 'Tên danh mục đã tồn tại trong loại danh mục này.'
            ];
        }

        // Cập nhật danh mục
        $sql = "UPDATE danhmucsp SET tendm = :tendm, loaidm = :loaidm, mota = :mota, icon = :icon WHERE iddm = :iddm";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tendm', $tendm, PDO::PARAM_STR);
        $stmt->bindValue(':loaidm', $loaidm, PDO::PARAM_INT);
        $stmt->bindValue(':mota', $mota ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':icon', $icon ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':iddm', $iddm, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'success' => true,
            'message' => 'Cập nhật danh mục thành công.'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Lỗi khi cập nhật danh mục: ' . $e->getMessage()
        ];
    }
}

function xoaDanhMuc($iddm) {
    try {
        $pdo = connectDatabase();

        // Kiểm tra xem danh mục có danh mục con hoặc sản phẩm liên quan không
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM danhmucsp WHERE loaidm = :iddm");
        $stmt_check->execute(['iddm' => $iddm]);
        if ($stmt_check->fetchColumn() > 0) {
            return [
                'success' => false,
                'message' => 'Không thể xóa danh mục vì có danh mục con liên quan.'
            ];
        }

        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM sanpham WHERE iddm = :iddm");
        $stmt_check->execute(['iddm' => $iddm]);
        if ($stmt_check->fetchColumn() > 0) {
            return [
                'success' => false,
                'message' => 'Không thể xóa danh mục vì có sản phẩm liên quan.'
            ];
        }

        // Xóa danh mục
        $sql = "DELETE FROM danhmucsp WHERE iddm = :iddm";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['iddm' => $iddm]);

        return [
            'success' => true,
            'message' => 'Xóa danh mục thành công.'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Lỗi khi xóa danh mục: ' . $e->getMessage()
        ];
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
