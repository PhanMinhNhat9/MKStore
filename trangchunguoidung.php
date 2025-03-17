<?php
    require_once 'config.php'; // Kết nối cơ sở dữ liệu

    // Xử lý tìm kiếm sản phẩm
    $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

    $productsPerPage = 20; // Số sản phẩm trên mỗi trang
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Trang hiện tại
    $offset = ($currentPage - 1) * $productsPerPage; // Tính toán offset

    function getProducts($keyword = '', $offset = 0, $limit = 20) {
        $pdo = connectDatabase();
        if ($keyword !== '') {
            $stmt = $pdo->prepare("SELECT * FROM sanpham WHERE tensp LIKE ? OR mota LIKE ? LIMIT ?, ?");
            $stmt->execute(['%' . $keyword . '%', '%' . $keyword . '%', $offset, $limit]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM sanpham LIMIT ?, ?");
            $stmt->execute([$offset, $limit]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $products = getProducts($keyword, $offset, $productsPerPage);
    function getTotalProducts($keyword = '') {
        $pdo = connectDatabase();
        if ($keyword !== '') {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sanpham WHERE tensp LIKE ?");
            $stmt->execute(['%' . $keyword . '%']);
        } else {
            $stmt = $pdo->query("SELECT COUNT(*) FROM sanpham");
        }
        return $stmt->fetchColumn();
    }

    $totalProducts = getTotalProducts($keyword);
    $totalPages = ceil($totalProducts / $productsPerPage);

    // Bắt đầu session nếu chưa có
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['user'])) {
        header("Location: GUI&dangnhap.php");
        exit();
    }

    // Lấy danh sách sản phẩm từ cơ sở dữ liệu
    // $products = getProducts();

    // Xử lý thêm sản phẩm vào giỏ hàng
    if (isset($_POST['add_to_cart'])) {
        $idsp = $_POST['idsp'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$idsp])) {
            $_SESSION['cart'][$idsp]++;
        } else {
            $_SESSION['cart'][$idsp] = 1;
        }

        // Chuyển hướng về chính trang này để tránh việc gửi lại form khi refresh
        header("Location: trangchunguoidung.php");
        exit();
    }

    // Xử lý thanh toán
    if (isset($_POST['checkout'])) {
        $pdo = connectDatabase();
        $cart = $_SESSION['cart'] ?? [];
        $idkh = $_SESSION['user']['iduser'];
        $tongtien = 0;

        foreach ($cart as $idsp => $quantity) {
            $stmt = $pdo->prepare("SELECT giaban FROM sanpham WHERE idsp = ?");
            $stmt->execute([$idsp]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $tongtien += $product['giaban'] * $quantity;
            }
        }

        // Tạo đơn hàng mới
        $stmt = $pdo->prepare("INSERT INTO donhang (idkh, tongtien, trangthai, phuongthuctt) VALUES (?, ?, 'Chờ xử lý', 'Tiền mặt')");
        $stmt->execute([$idkh, $tongtien]);

        // Lấy ID đơn hàng vừa tạo
        $iddh = $pdo->lastInsertId();

        // Thêm chi tiết đơn hàng
        foreach ($cart as $idsp => $quantity) {
            $stmt = $pdo->prepare("INSERT INTO chitietdonhang (iddh, idsp, soluong, gia) VALUES (?, ?, ?, (SELECT giaban FROM sanpham WHERE idsp = ?))");
            $stmt->execute([$iddh, $idsp, $quantity, $idsp]);
        }

        // Xóa giỏ hàng sau khi thanh toán
        unset($_SESSION['cart']);

        // Chuyển hướng về trang cảm ơn
        header("Location: trangchunguoidung.php?thank_you=1");
        exit();
    }

    // Lấy thông tin sản phẩm từ giỏ hàng
    function getCartProducts() {
        $pdo = connectDatabase();
        $cart = $_SESSION['cart'] ?? [];
        $products = [];

        foreach ($cart as $idsp => $quantity) {
            $stmt = $pdo->prepare("SELECT * FROM sanpham WHERE idsp = ?");
            $stmt->execute([$idsp]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $product['quantity'] = $quantity;
                $products[] = $product;
            }
        }

        return $products;
    }

    $cartProducts = getCartProducts();
    ?>

    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cửa Hàng Phụ Kiện</title>
        <link rel="stylesheet" href="trangchucss.css">
    </head>

    <div id="toast" class="toast-message"></div>
    <script>
    function addToCart(idsp) {
        const formData = new URLSearchParams();
        formData.append('idsp', idsp);

        fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Server trả về lỗi HTTP: " + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log("Phản hồi thô từ PHP:", text);

        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showToast(data.message);
            } else {
                alert("Lỗi: " + data.message);
            }
        } catch (err) {
            console.error("Lỗi khi phân tích JSON:", err);
            alert("Lỗi JSON không hợp lệ từ máy chủ:\n" + text);
        }
    })
    .catch(error => {
        console.error("Lỗi fetch:", error);
        alert("Lỗi khi gửi yêu cầu đến server: " + error.message);
    });
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.style.display = 'block';
        toast.style.opacity = '1';

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 500);
        }, 2000);
    }
    </script>

    <body>
        <!-- Thanh navbar -->
        <nav class="navbar">
            <div class="logo-container">
                <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="logo">
                <span class="store-name"> M'K STORE</span>
            </div>
            
            <div class="search-container">
                <form method="GET" action="trangchunguoidung.php" style="display: flex;">
                    <input type="text" name="search" class="search-bar" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="search-btn">Tìm kiếm</button>
                </form>
            </div>
            

            
            <div class="nav-buttons">
                <button class="btn trangchu" onclick="window.location.href='trangchunguoidung.php'">
                    <i class="fas fa-home"></i> Trang chủ
                </button>
                <button class="btn giohang" onclick="window.location.href='giohang.php'">
                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                </button>
                <button class="btn thongbao"><i class="fas fa-bell"></i> Thông báo</button>
                <!-- Nút Tài khoản -->
                <div style="position: relative;">
                    <button class="btn taikhoan" onclick="toggleAccountDropdown()">
                        <i class="fas fa-user"></i> 
                        <?= htmlspecialchars($_SESSION['user']['hoten'], ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                    <div class="dropdown" id="accountDropdown">
                        <div class="logout" onclick="logout()">Đăng xuất</div>
                    </div>
                </div>
            </div>
        </nav>

        <main>

        <h2>Sản Phẩm Nổi Bật</h2>
        <?php if ($keyword !== ''): ?>
                <p style="margin-left: 16px;">Kết quả tìm kiếm cho: <strong><?= htmlspecialchars($keyword) ?></strong></p>
            <?php endif; ?>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="<?php echo $product['anh']; ?>" alt="<?php echo $product['tensp']; ?>">
                <h3><?php echo $product['tensp']; ?></h3>
                <p><?php echo $product['mota']; ?></p>
                <p class="price">Giá: <?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</p>
                <button onclick="addToCart(<?php echo $product['idsp']; ?>)" class="buy-button">Thêm vào giỏ</button>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a class="arrow" href="?page=<?= $currentPage - 1 ?>&search=<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">« Trước</a>
        <?php endif; ?>

        <?php
        // Số lượng trang muốn hiển thị xung quanh trang hiện tại
        $range = 2;

        // Hiển thị trang 1 luôn
        if ($currentPage > $range + 2) {
            echo '<a href="?page=1&search=' . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . '">1</a>';
            echo '<span class="dots">...</span>';
        }

        // Các trang ở giữa
        for ($i = max(1, $currentPage - $range); $i <= min($totalPages, $currentPage + $range); $i++) {
            $activeClass = ($i === $currentPage) ? 'active' : '';
            echo '<a class="' . $activeClass . '" href="?page=' . $i . '&search=' . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . '">' . $i . '</a>';
        }

        // Hiển thị trang cuối nếu cách xa trang hiện tại
        if ($currentPage < $totalPages - ($range + 1)) {
            echo '<span class="dots">...</span>';
            echo '<a href="?page=' . $totalPages . '&search=' . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . '">' . $totalPages . '</a>';
        }
        ?>

        <?php if ($currentPage < $totalPages): ?>
            <a class="arrow" href="?page=<?= $currentPage + 1 ?>&search=<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">Sau »</a>
        <?php endif; ?>
    </div>
        </div>
    </main>

        

        <footer>
            <p>&copy; 2025 Cửa Hàng Phụ Kiện. Tất cả quyền được bảo lưu.</p>
        </footer>

        <script>
            function toggleAccountDropdown() {
                var dropdown = document.getElementById("accountDropdown");
                if (dropdown) {
                    dropdown.classList.toggle("active");
                }
            }

            function logout() {
                window.location.href = "logout.php"; // Thay bằng trang xử lý đăng xuất
            }

            document.addEventListener("click", function(event) {
                var dropdown = document.getElementById("accountDropdown");
                var button = document.querySelector(".taikhoan");

                if (dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.remove("active");
                }
            });
        </script>
    </body>
    </html>

    <?php
    // Hiển thị thông báo cảm ơn nếu thanh toán thành công
    if (isset($_GET['thank_you'])) {
        echo "<script>alert('Cảm ơn bạn đã mua hàng! Đơn hàng của bạn đang được xử lý.');</script>";
    }
    ?>