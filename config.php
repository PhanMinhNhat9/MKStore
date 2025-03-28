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
    // Đăng nhâp
    function dangnhap($tendn, $matkhau) {
        if (empty($tendn) || empty($matkhau)) {
            return "Vui lòng nhập đầy đủ thông tin";
        }
        $pdo = connectDatabase();
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        if (!isset($_SESSION['last_attempt_time'])) {
            $_SESSION['last_attempt_time'] = time();
        }
        if (!isset($_SESSION['lock_time'])) {
            $_SESSION['lock_time'] = 0;
        }
        // Nếu đã quá 10 phút từ lần nhập sai đầu tiên, reset lại số lần nhập
        if (time() - $_SESSION['last_attempt_time'] > 120) {
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
            
            if ($_SESSION['user']['quyen']==0) {
                header("Location: trangchuadmin.php");
                exit();
            }
            else {           
                header("Location: trangchunguoidung.php");
                exit();
            }
            
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            if ($_SESSION['login_attempts'] >= 2) {
                $_SESSION['lock_time'] = time() + 120; // Khóa trong 2 phút
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

    function themNguoiDung() {
        $pdo = connectDatabase();
        try {
            // Lấy dữ liệu từ form
            $fullname = $_POST['fullname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
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
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Thêm thành công!');
                        goBack();
                    </script>";
            } else {
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Thêm không thành công!');
                        goBack();
                    </script>";
            }
        } catch (PDOException $e) {
            echo "Lỗi kết nối CSDL: " . $e->getMessage();
        }
    }
    function themSanPham() {
        $tensp = $_POST['tensp'];
        $mota = $_POST['mota'];
        $giaban = $_POST['giaban'];
        $soluong = $_POST['soluong'];

        $iddm = $_POST['iddm'];

        // Xử lý upload ảnh
        $anh = "";
        if (!empty($_FILES['anh']['name'])) {
            $targetDir = "picture/";
            $fileName = time() . "_" . basename($_FILES["anh"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($_FILES["anh"]["tmp_name"], $targetFilePath)) {
                    $anh = "picture/".$fileName;
                } else {
                    echo "Lỗi khi tải ảnh lên!";
                    exit;
                }
            } else {
                echo "Chỉ hỗ trợ định dạng JPG, JPEG, PNG, GIF!";
                exit;
            }
        }
        // Thêm vào database
        try {
            $pdo = connectDatabase();
            $stmt = $pdo->prepare("INSERT INTO sanpham (tensp, mota, giaban, soluong, anh, iddm) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tensp, $mota, $giaban, $soluong, $anh, $iddm]);
            if ($stmt->rowcount()>0) {
                echo "<script> 
                        alert('Thêm thành công!');
                        window.top.location.href = 'trangchuadmin.php';
                      </script>";
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
    function capnhatNguoiDung() {
        $pdo = connectDatabase(); // Kết nối PDO
        try {
            $iduser  = intval($_POST['iduser']);
            $hoten   = $_POST['hoten'];
            $tendn   = $_POST['tendn'];
            $email   = $_POST['email'];
            $sdt     = $_POST['sdt'];
            $diachi  = $_POST['diachi'];
            $matkhau  = $_POST['matkhau'];
            $quyen   = $_POST['quyen'];
            $file    = $_FILES['anh'];
            // Xử lý ảnh nếu có tải lên
            if (!empty($file['name'])) {
                $target_dir = "picture/";
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
            $sql = "UPDATE user SET 
                    hoten = :hoten, tendn = :tendn, email = :email, 
                    sdt = :sdt, diachi = :diachi, quyen = :quyen, anh = :anh";
            $params = [
                ':iduser'  => $iduser,
                ':hoten'   => $hoten,
                ':tendn'   => $tendn,
                ':email'   => $email,
                ':sdt'     => $sdt,
                ':diachi'  => $diachi,
                ':quyen'   => $quyen,
                ':anh'     => $anh
            ];
            if (!empty($matkhau)) {
                $sql .= ", matkhau = :matkhau"; 
                $params[':matkhau'] = $matkhau; 
            }
            $sql .= " WHERE iduser = :iduser";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                echo "<script> 
                        alert('Cập nhật thành công!');
                        window.top.location.href='trangchuadmin.php';
                    </script>";
            } else {
            }
        } catch (PDOException $e) {
            return "Lỗi cập nhật: " . $e->getMessage();
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

    function themDMcon() {
        $pdo = connectDatabase(); 
        $tendm = trim($_POST['tendm'] ?? '');
        $mota = trim($_POST['mota'] ?? '');
        $loaidm = trim($_POST['loaidm'] ?? '');
        // Kiểm tra dữ liệu đầu vào
        if (empty($tendm) || empty($loaidm)) {
            echo "<script>alert('Tên danh mục và loại danh mục không được để trống!');</script>";
            return;
        }
        // Xử lý upload icon
        $icon = '';
        if (!empty($_FILES['icon']['name'])) {
            $target_dir = "icon/";
            $file_name = basename($_FILES["icon"]["name"]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_ext, $allowed_types)) {
                echo "<script>alert('Chỉ chấp nhận các file JPG, JPEG, PNG, GIF!');</script>";
                return;
            }
            $target_file = $target_dir . $file_name;
            if (!move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file)) {
                echo "<script>alert('Lỗi khi tải lên tệp!');</script>";
                return;
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
    
            echo "<script> 
                    alert('Thêm thành công!');
                    window.top.location.href='trangchuadmin.php';
                  </script>";
        } catch (PDOException $e) {
            echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
        }
    }
    function capnhatAnhUser() {
        $pdo = connectDatabase();
            $iduser = intval($_POST['iduser']);
            $file = $_FILES['fileInput'];
            // Xử lý ảnh nếu có tải lên
            if (!empty($file['name'])) {
                $target_dir = "picture/";
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
            $updateSql = "UPDATE user SET anh = :anh WHERE iduser = :iduser";
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([
                'anh' => $anh,
                'iduser' => $iduser
            ]);
            echo "<script> 
                        alert('Cập nhật thành công!');
                        window.top.location.href='trangchuadmin.php';
                    </script>";
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
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Xóa thành công!');
                        goBack();
                      </script>";
            } else {
                echo "<script src='../trangchuadmin.js'></script>";
            echo "<script> 
                    alert('Lỗi cập nhật!');
                    goBack();
                  </script>";
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
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Xóa thành công!');
                        goBack();
                      </script>";
            } else {
                echo "<script>alert('Xóa danh mục thất bại!');</script>";
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
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Thêm thành công!');
                        goBack();
                      </script>";            
            } else {
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Thêm không thành công!');
                        goBack();
                    </script>";
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
    function capnhatMGG() {
        $pdo = connectDatabase();
        $code = !empty($_POST['code']) ? $_POST['code'] : taoMaGiamGia();
        $idmgg = $_POST['idmgg'];
        $phantram = $_POST['phantram'];
        $ngayhieuluc = $_POST['ngayhieuluc'];
        $ngayketthuc = $_POST['ngayketthuc'];
        $giaapdung = $_POST['giaapdung'];
        $soluong = $_POST['soluong'];
        $iddm = $_POST['iddm'];
        $stmt = $pdo->prepare("UPDATE magiamgia SET code = :code, phantram = :phantram, ngayhieuluc = :ngayhieuluc, 
                            ngayketthuc = :ngayketthuc, giaapdung = :giaapdung, iddm = :iddm, soluong = :soluong WHERE idmgg = :id");
        $stmt->execute([
            'code' => $code,
            'phantram' => $phantram,
            'ngayhieuluc' => $ngayhieuluc,
            'ngayketthuc' => $ngayketthuc,
            'giaapdung' => $giaapdung,
            'soluong' => $soluong,
            'id' => $idmgg,
            'iddm' => $iddm
        ]);
        if ($stmt->rowCount() > 0) {
            echo "<script> 
                    alert('Cập nhật thành công!');
                  </script>";
        } else {
            echo "<script> 
                    alert('Lỗi cập nhật!');
                  </script>";
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['themnd'])) {
            themNguoiDung();
        }
        if (isset($_POST['capnhatnd'])) {
            capnhatNguoiDung();
        }
        if (isset($_POST['themdmcon'])) {
            themDMcon();
        }
        if (isset($_POST['capnhatdm'])) {
            capnhatDanhMuc();
        }
        if (isset($_POST['xoadm'])) {
            xoaDanhMuc();
        }
        if (isset($_POST['themmgg'])) {
            themMGG();
        }
        if (isset($_POST['capnhatmgg'])) {
            capnhatMGG();
        }
        if (isset($_POST['capnhatanhuser'])) {
            capnhatAnhUser();
        }
        if (isset($_POST['themsp'])) {
            themSanPham();
        }
    }
?>
