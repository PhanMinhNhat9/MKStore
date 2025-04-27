<?php
include "config.php";
$pdo = connectDatabase();

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem admin đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    header("Location: GUI&dangnhap.php");
    exit();
}

// Kiểm tra trạng thái tài khoản bị xóa
$stmt = $pdo->prepare("SELECT iduser, trangthai, ngaykh FROM khxoatk WHERE iduser = :iduser");
$stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "<script>
        alert('Tài khoản của bạn đã bị xóa và sẽ được xóa hoàn toàn sau 30 ngày!');
        window.location.href = 'logout.php';
    </script>";
    exit();
}

define('SESSION_TIMEOUT', 1800);

// Kiểm tra thời gian không hoạt động
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header("Location: GUI&dangnhap.php");
    exit();
}

// Cập nhật thời gian hoạt động cuối cùng
$_SESSION['last_activity'] = time();

// Làm mới session ID để tăng cường bảo mật
if (session_status() == PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

session_write_close(); // Đảm bảo session được ghi lại ngay lập tức

// Lấy thông tin admin từ session
$admin_name = htmlspecialchars($_SESSION['user']['hoten'], ENT_QUOTES, 'UTF-8');
$admin_email = htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8');
$admin_phone = htmlspecialchars($_SESSION['user']['sdt'], ENT_QUOTES, 'UTF-8');
$admin_avatar = !empty($_SESSION['user']['anh']) ? htmlspecialchars($_SESSION['user']['anh'], ENT_QUOTES, 'UTF-8') : "https://i.pravatar.cc/100";

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
try {
    $sql = "SELECT idsp, tensp, iddm FROM sanpham";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $products = [];
}

// Xóa tài khoản người dùng bị đánh dấu xóa sau 30 ngày
try {
    $sql = "DELETE FROM user WHERE iduser IN (
        SELECT iduser FROM khxoatk WHERE DATEDIFF(CURDATE(), ngaykh) >= 30
    )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    error_log("Error deleting users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="trangchuadmin.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="sweetalert2/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="sweetalert2/sweetalert2.min.js"></script>
    <script src="trangchuadmin.js"></script>
</head>
<body>
    <!-- Thanh navbar -->
    <nav class="navbar">
        <div class="logo-container">
            <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="logo">
            <span class="store-name">M'K STORE</span>
        </div>

        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Tìm kiếm..." onkeyup="handleSearch(this.value)">
            <button class="mic-btn"><i class="fas fa-microphone"></i></button>
            <button class="search-btn" onclick="handleSearch(document.querySelector('.search-bar').value)">Tìm kiếm</button>
        </div>
        <div id="search-results" hidden></div>

        <div class="nav-buttons">
            <?php if ($_SESSION['user']['quyen'] != 1): ?>
                <button class="btn clear-storage-btn" onclick="clearLocalStorage()"><i class="fas fa-eraser"></i> Model</button>
            <?php endif; ?>
            <button class="btn trangchu" onclick="goBackHome()"><i class="fas fa-home"></i> Trang chủ</button>
            <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM yeucaudonhang WHERE trangthai = 0");
                $stmt->execute();
                $thongbaoCount = (int) $stmt->fetchColumn();
            ?>
            <?php if ($_SESSION['user']['quyen'] != 1): ?>
                <button class="btn thongbao" id="menu-tb" onclick="loadThongBao()">
                    <i class="fas fa-bell"></i> Thông báo
                    <?php if ($thongbaoCount > 0): ?>
                        <span id="listThongBao" style="
                            position: absolute;
                            top: 20px;
                            background: red;
                            color: white;
                            border-radius: 50%;
                            padding: 2px 6px;
                            font-size: 12px;
                        ">
                            <?= $thongbaoCount ?>
                        </span>
                    <?php endif; ?>
                </button>
            <?php endif; ?>

            <!-- Nút Admin với dropdown -->
            <div style="position: relative;">
                <button class="btn taikhoan" onclick="ddadmin()">
                    <i class="fas fa-user"></i> <?= !empty($admin_name) ? $admin_name : "Admin"; ?>
                </button>
                <div class="dropdowntk" id="adminDropdown">
                    <div class="profile">
                        <img src="<?= $admin_avatar ?>" alt="Avatar">
                        <p><strong><?= $admin_name ?></strong></p>
                        <p><?= $admin_phone ?></p>
                        <p><?= $admin_email ?></p>
                    </div>
                    <div class="logout" onclick="logout()">Đăng xuất</div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Thanh menu -->
    <nav class="menu">
        <div class="menu-item" id="menu-user" onclick="loadDLUser()"><i class="fas fa-users"></i> Quản lý người dùng</div>
        <div class="menu-item" id="menu-product" onclick="loadDLSanpham()"><i class="fas fa-box"></i> Quản lý sản phẩm</div>
        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="menu-item" id="menu-category" onclick="loadDLDanhmuc()">
                <i class="fas fa-list"></i> Quản lý danh mục
            </div>
            <div class="menu-item" id="menu-discount" onclick="loadDLMGG()">
                <i class="fas fa-tags"></i> Quản lý khuyến mãi
            </div>
        <?php endif; ?>
        <div class="menu-item" id="menu-order" onclick="loadDLDonhang()"><i class="fas fa-chart-bar"></i> Quản lý đơn hàng</div>
        <div class="menu-item" id="menu-gh" onclick="loadGH()"><i class="fas fa-shopping-cart"></i> Giỏ hàng</div>
        <div class="menu-item" id="menu-support" onclick="loadPhanHoi()"><i class="fas fa-headset"></i> Hỗ trợ khách hàng</div>
    </nav>

    <!-- Nội dung trang web -->
    <div class="main-content" id="main-content">
        <iframe id="Frame" src="" scrolling="no"></iframe>
    </div>

    <!-- Chân web -->
    <footer class="footer">
        <div class="footer-content">
            <div class="copyright">
                <span>© 2025 M'K STORE - All rights reserved.</span>
            </div>
            <div class="contact-info">
                <span><i class="fas fa-map-marker-alt"></i> Địa chỉ: 73 Nguyễn Huệ, phường 2, thành phố Vĩnh Long, tỉnh Vĩnh Long </span>
                <span><i class="fas fa-phone"></i> 0702 8045 94</span>
            </div>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-facebook-messenger"></i> Zalo</a>
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Hàm xóa localStorage
        function clearLocalStorage() {
            localStorage.removeItem('search-model');
            localStorage.removeItem('search-vocabulary');
            Swal.fire({
                title: 'Thành công!',
                text: 'Đã xóa localStorage thành công!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        }

        // Lấy products từ PHP
        const rawProducts = <?php echo json_encode($products); ?>;
        console.log("Raw Products:", rawProducts);
        const products = rawProducts.map(p => ({
            id: parseInt(p.idsp, 10) || 0,
            name: p.tensp || 'unknown',
            category: String(p.iddm ?? 'unknown')
        })).filter(p => p.id > 0);
        console.log("Processed Products:", products);

        // Kiểm tra nếu products rỗng (chỉ áp dụng cho menu-product)
        function checkEmptyProducts() {
            if (products.length === 0) {
                console.warn("No products found. Disabling TensorFlow.js search.");
                document.getElementById('search-results').innerHTML = '<div>Không có sản phẩm để tìm kiếm. Vui lòng thêm sản phẩm.</div>';
                return true;
            }
            return false;
        }

        // Chuẩn hóa từ khóa
        const keywordMapping = {
            'backpack': 'balo',
            'cặp': 'balo',
            'túi đeo lưng': 'balo',
            'túi đi học': 'balo',
            'ba lô': 'balo',
            'bag': 'túi',
            'túi xách': 'túi',
            'túi đeo': 'túi',
            'wallet': 'ví',
            'clutch': 'ví',
            'ví tiền': 'ví',
            'watch': 'đồng hồ',
            'đồng hồ đeo tay': 'đồng hồ',
            'đồng hồ thời trang': 'đồng hồ',
            'leather': 'da',
            'đồ da': 'da',
            'phụ kiện da': 'da',
            'eye': 'mắt',
            'mỹ phẩm mắt': 'mắt',
            'phấn mắt': 'mắt',
            'lip': 'môi',
            'son môi': 'môi',
            'mỹ phẩm môi': 'môi',
            'tool': 'dụng cụ',
            'phụ kiện': 'dụng cụ',
            'đồ dùng': 'dụng cụ',
            'necklace': 'vòng cổ',
            'dây chuyền': 'vòng cổ',
            'chuỗi ngọc': 'vòng cổ',
            'vongco': 'vòng cổ',
            'bracelet': 'vòng lắc',
            'vòng tay': 'vòng lắc',
            'lắc': 'vòng lắc',
            'ring': 'nhẫn',
            'nhẫn đính hôn': 'nhẫn',
            'nhẫn cưới': 'nhẫn',
            'nhẫn vàng 18k': 'nhẫn',
            'earring': 'bông tai',
            'khuyên tai': 'bông tai',
            'hoa tai': 'bông tai',
            'teddy bear': 'gấu bông',
            'gấu teddy': 'gấu bông',
            'thú nhồi bông': 'gấu bông'
        };

        // Tạo trainingData động với từ khóa phong phú (chỉ khi menu-product)
        let trainingData = [];
        let vocabulary = [];
        function prepareTrainingData() {
            trainingData = products.map(product => {
                console.log("Processing product:", product);
                const categoryKeywords = (product.category && product.category.trim() !== '')
                    ? product.category.toLowerCase().split(/\s+/)
                    : [];
                const productNameWords = product.name.toLowerCase().split(/\s+/).filter(word => !/^\d+$/.test(word));
                return {
                    keywords: [
                        ...productNameWords,
                        ...categoryKeywords,
                        ...(product.name.toLowerCase().includes("balo") ? ["cặp", "backpack", "túi đeo lưng", "túi đi học", "ba lô"] : []),
                        ...(product.name.toLowerCase().includes("túi") ? ["bag", "túi xách", "túi đeo"] : []),
                        ...(product.name.toLowerCase().includes("ví") ? ["wallet", "clutch", "ví tiền"] : []),
                        ...(product.name.toLowerCase().includes("đồng hồ") ? ["watch", "đồng hồ đeo tay", "đồng hồ thời trang"] : []),
                        ...(product.name.toLowerCase().includes("da") ? ["leather", "đồ da", "phụ kiện da"] : []),
                        ...(product.name.toLowerCase().includes("mắt") ? ["eye", "mỹ phẩm mắt", "phấn mắt"] : []),
                        ...(product.name.toLowerCase().includes("môi") ? ["lip", "son môi", "mỹ phẩm môi"] : []),
                        ...(product.name.toLowerCase().includes("dụng cụ") ? ["tool", "phụ kiện", "đồ dùng"] : []),
                        ...(product.name.toLowerCase().includes("vòng cổ") ? ["dây chuyền", "chuỗi ngọc", "vongco", "necklace"] : []),
                        ...(product.name.toLowerCase().includes("vòng lắc") ? ["vòng tay", "lắc", "bracelet"] : []),
                        ...(product.name.toLowerCase().includes("nhẫn") ? ["nhẫn đính hôn", "nhẫn cưới", "nhẫn vàng 18k", "ring"] : []),
                        ...(product.name.toLowerCase().includes("bông tai") ? ["khuyên tai", "hoa tai", "earring"] : []),
                        ...(product.name.toLowerCase().includes("gấu bông") ? ["gấu teddy", "thú nhồi bông", "teddy bear"] : [])
                    ],
                    productId: product.id
                };
            });
            console.log("Training Data:", trainingData);

            vocabulary = products.length > 0 ? [...new Set(trainingData.flatMap(item => item.keywords))] : [];
            console.log("Vocabulary size:", vocabulary.length);
            console.log("Vocabulary:", vocabulary);

            const currentVocabulary = JSON.stringify(vocabulary);
            const savedVocabulary = localStorage.getItem('search-vocabulary');
            if (savedVocabulary !== currentVocabulary) {
                console.log("Vocabulary changed, clearing old model");
                localStorage.removeItem('search-model');
                localStorage.setItem('search-vocabulary', currentVocabulary);
            }
        }

        function keywordsToVector(keywords) {
            const vector = vocabulary.map(word => keywords.includes(word) ? 1 : 0);
            console.log("Input vector size:", vector.length);
            console.log("Input vector:", vector);
            return vector;
        }

        let xs = null;
        let ys = null;
        function prepareTrainingTensors() {
            xs = products.length > 0 ? tf.tensor2d(trainingData.map(item => keywordsToVector(item.keywords))) : null;
            ys = products.length > 0 ? tf.tensor2d(trainingData.map(item => [item.productId]), [trainingData.length, 1]) : null;
        }

        async function trainModel() {
            if (products.length === 0) {
                console.warn("Cannot train model: No products available.");
                return null;
            }

            console.log("Training new model with vocabulary size:", vocabulary.length);
            const model = tf.sequential();
            model.add(tf.layers.dense({
                units: 32,
                activation: 'relu',
                inputShape: [vocabulary.length]
            }));
            model.add(tf.layers.dense({
                units: 16,
                activation: 'relu'
            }));
            model.add(tf.layers.dense({
                units: Math.max(...products.map(p => p.id)) + 1 || 1,
                activation: 'softmax'
            }));

            model.compile({
                optimizer: tf.train.adam(0.005),
                loss: 'sparseCategoricalCrossentropy',
                metrics: ['accuracy']
            });

            await model.fit(xs, ys, {
                epochs: 150,
                verbose: 0,
                callbacks: {
                    onEpochEnd: (epoch, logs) => {
                        console.log(`Epoch ${epoch}: loss = ${logs.loss}, accuracy = ${logs.acc}`);
                    }
                }
            });

            await model.save('localstorage://search-model');
            console.log("Model saved");
            return model;
        }

        async function loadModel() {
            if (products.length === 0) {
                console.warn("Cannot load model: No products available.");
                return null;
            }

            try {
                console.log("Loading model...");
                const model = await tf.loadLayersModel('localstorage://search-model');
                const expectedShape = model.layers[0].input.shape[1];
                if (expectedShape !== vocabulary.length) {
                    console.log("Input shape mismatch, retraining model");
                    localStorage.removeItem('search-model');
                    return await trainModel();
                }
                return model;
            } catch (e) {
                console.error("Error loading model:", e);
                return await trainModel();
            }
        }

        async function predictProduct(searchQuery) {
            if (checkEmptyProducts()) return null;

            let normalizedQuery = searchQuery.toLowerCase();
            for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                if (normalizedQuery.includes(keyword)) {
                    normalizedQuery = normalizedQuery.replace(keyword, mapped);
                }
            }
            const keywords = normalizedQuery.split(/\s+/);
            console.log("Normalized query keywords:", keywords);

            // Fallback cho từ khóa cụ thể
            if (keywords.includes("ta2589")) {
                const vongCoProducts = products.filter(p => p.name.toLowerCase().includes("vòng cổ"));
                if (vongCoProducts.length > 0) {
                    console.log("Fallback: Found products with 'vòng cổ':", vongCoProducts);
                    return vongCoProducts[0];
                }
            }

            const inputVector = tf.tensor2d([keywordsToVector(keywords)]);
            const model = await loadModel();
            if (!model) return null;
            const prediction = model.predict(inputVector);
            const predictedId = tf.argMax(prediction, axis=1).dataSync()[0];
            console.log("Predicted ID:", predictedId);
            let product = products.find(p => p.id === predictedId) || null;
            console.log("Predicted product:", product);

            // Fallback nếu dự đoán không chính xác
            if (!product) {
                for (let keyword of keywords) {
                    const matchedProduct = products.find(p => p.name.toLowerCase().includes(keyword));
                    if (matchedProduct) {
                        console.log("Fallback: Found product with keyword", keyword, ":", matchedProduct);
                        return matchedProduct;
                    }
                }
            }

            return product;
        }

        let searchTimeout;
        async function handleSearch(query) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                query = query.trim();
                const resultsDiv = document.getElementById('search-results');
                resultsDiv.innerHTML = '';
                const activeMenu = getActiveMenu();

                if (activeMenu === "menu-product") {
                    prepareTrainingData();
                    prepareTrainingTensors();

                    if (query) {
                        if (checkEmptyProducts()) {
                            searchProducts(query);
                            return;
                        }
                        try {
                            const predictedProduct = await predictProduct(query);
                            if (predictedProduct) {
                                resultsDiv.innerHTML = `
                                    <div class="search-result">
                                        <span>${predictedProduct.name}</span>
                                    </div>
                                `;
                                let normalizedQuery = query.toLowerCase();
                                for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                                    if (normalizedQuery.includes(keyword)) {
                                        normalizedQuery = normalizedQuery.replace(keyword, mapped);
                                        break;
                                    }
                                }
                                searchProducts(normalizedQuery);
                            } else {
                                resultsDiv.innerHTML = '<div>Không tìm thấy sản phẩm</div>';
                                searchProducts(query);
                            }
                        } catch (e) {
                            console.error("Error in handleSearch:", e);
                            resultsDiv.innerHTML = '<div>Lỗi khi tìm kiếm. Vui lòng thử lại.</div>';
                            searchProducts(query);
                        }
                    } else {
                        searchProducts(query);
                    }
                } else {
                    if (activeMenu === "menu-user") searchUsers(query);
                    if (activeMenu === "menu-order") searchDonHang(query);
                    if (activeMenu === "menu-support") searchPhanHoi(query);
                }
            }, 300);
        }

        function searchUsers(query) {
            let iframe = document.getElementById("Frame");
            if (iframe) {
                iframe.src = "nguoidung/hienthinguoidung.php?query=" + encodeURIComponent(query);
                console.log("Iframe updated (users):", iframe.src);
            } else {
                console.error("Không tìm thấy iframe có ID 'Frame'");
            }
        }

        function searchProducts(query) {
            let iframe = document.getElementById("Frame");
            if (iframe) {
                iframe.src = "sanpham/hienthisanpham.php?query=" + encodeURIComponent(query);
                console.log("Iframe updated (products):", iframe.src);
                iframe.onload = function() {
                    console.log("Iframe loaded successfully");
                    if (iframe.contentDocument && iframe.contentDocument.body.innerHTML.trim() === "") {
                        console.warn("Iframe content is empty");
                        iframe.contentDocument.body.innerHTML = '<div class="no-data-message">Không có sản phẩm nào để hiển thị.</div>';
                    }
                };
            } else {
                console.error("Không tìm thấy iframe có ID 'Frame'");
            }
        }

        function searchDonHang(query) {
            let iframe = document.getElementById("Frame");
            if (iframe) {
                iframe.src = "donhang/hienthidonhang.php?query=" + encodeURIComponent(query);
                console.log("Iframe updated (orders):", iframe.src);
            } else {
                console.error("Không tìm thấy iframe có ID 'Frame'");
            }
        }

        function searchPhanHoi(query) {
            let iframe = document.getElementById("Frame");
            if (iframe) {
                iframe.src = "phanhoi/hienthiphanhoi.php?query=" + encodeURIComponent(query);
                console.log("Iframe updated (support):", iframe.src);
            } else {
                console.error("Không tìm thấy iframe có ID 'Frame'");
            }
        }

        // Voice Search
        document.addEventListener("DOMContentLoaded", function() {
            const micButton = document.querySelector(".mic-btn");
            const searchBar = document.querySelector(".search-bar");

            if (!('webkitSpeechRecognition' in window)) {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Trình duyệt không hỗ trợ tìm kiếm bằng giọng nói.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                const recognition = new webkitSpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.lang = "vi-VN";

                micButton.addEventListener("click", function() {
                    micButton.classList.add("listening");
                    recognition.start();
                });

                recognition.onresult = function(event) {
                    micButton.classList.remove("listening");
                    const speechResult = event.results[0][0].transcript;
                    searchBar.value = speechResult;
                    handleSearch(speechResult);
                };

                recognition.onerror = function(event) {
                    micButton.classList.remove("listening");
                    console.error("Lỗi nhận dạng giọng nói: ", event.error);
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Không thể nhận dạng giọng nói. Vui lòng thử lại.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                };

                recognition.onend = function() {
                    micButton.classList.remove("listening");
                };
            }

            // Khởi tạo iframe mặc định khi tải trang
            const activeMenu = getActiveMenu();
            if (activeMenu === "menu-product") {
                searchProducts("");
            }
        });

        // Menu và Dropdown Logic
        activateMenu();
        let id = getActiveMenu();
        if (id === "menu-user") loadDLUser();
        else if (id === "menu-product") loadDLSanpham();
        else if (id === "menu-category") loadDLDanhmuc();
        else if (id === "menu-order") loadDLDonhang();
        else if (id === "menu-discount") loadDLMGG();
        else if (id === "menu-support") loadPhanHoi();
        else if (id === "menu-gh") loadGH();
        else goBackHome();

        function ddadmin() {
            document.getElementById("adminDropdown").classList.toggle("active");
        }

        document.addEventListener("click", function(event) {
            const dropdown = document.getElementById("adminDropdown");
            const button = document.querySelector(".taikhoan");
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });

        handleSessionTimeout(<?= SESSION_TIMEOUT ?>);

        // Xử lý thông báo trạng thái
        <?php
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            $messages = [
                'cnuserT' => ['Thành Công!', 'Thông tin đã được cập nhật.', 'success'],
                'cnuserF' => ['Thất bại!', 'Cập nhật thông tin thất bại.', 'error'],
                'themuserT' => ['Thành Công!', 'Người dùng đã được thêm vào danh sách!', 'success'],
                'themuserF' => ['Thất bại!', 'Thêm người dùng thất bại.', 'error'],
                'cnspT' => ['Thành Công!', 'Thông tin sản phẩm đã được cập nhật thành công!', 'success'],
                'cnspF' => ['Thất bại!', 'Cập nhật sản phẩm thất bại.', 'error'],
                'themspT' => ['Thành Công!', 'Sản phẩm đã được thêm thành công!', 'success'],
                'themspF' => ['Thất bại!', 'Thêm sản phẩm thất bại.', 'error'],
                'xoaspT' => ['Thành Công!', 'Xóa sản phẩm thành công!', 'success'],
                'xoaspF' => ['Thất bại!', 'Xóa sản phẩm thất bại.', 'error'],
                'cnanhuserT' => ['Thành Công!', 'Cập nhật ảnh thành công!', 'success'],
                'cnanhuserF' => ['Thất bại!', 'Cập nhật ảnh thất bại.', 'error']
            ];
            if (isset($messages[$status])) {
                echo "Swal.fire({
                    title: '{$messages[$status][0]}',
                    text: '{$messages[$status][1]}',
                    icon: '{$messages[$status][2]}',
                    confirmButtonText: 'OK'
                });";
            }
        }
        ?>
    </script>
</body>
</html>