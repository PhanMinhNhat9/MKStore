<?php
    include "config.php";
    $pdo = connectDatabase();
    // Bắt đầu session nếu chưa có
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Kiểm tra xem admin đã đăng nhập chưa
    if (isset($_SESSION['user'])) {

    } else {
        header("Location: auth/dangnhap.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT iduser, trangthai, ngaykh FROM khxoatk WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "
        <script>
            alert('Tài khoản của bạn đã bị xóa và sẽ được xóa hoàn toàn sau 30 ngày!');
            window.location.href = 'logout.php';
        </script>";
    }

    define('SESSION_TIMEOUT', 1800);

    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        error_log("Session inactive time: $inactive_time seconds"); // Debug log
    }
    // Cập nhật lại thời gian hoạt động cuối cùng
    $_SESSION['last_activity'] = time();
    // Làm mới session ID để tăng cường bảo mật (kiểm tra session trước khi gọi)
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    session_write_close(); // Đảm bảo session được ghi lại ngay lập tức

    // Lấy thông tin admin từ session
    $stmt = $pdo->prepare("SELECT `hoten`, `anh`, `email`, `sdt` FROM `user` WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $admin_name = htmlspecialchars($result['hoten'], ENT_QUOTES, 'UTF-8');
    $admin_email = htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8');
    $admin_phone = htmlspecialchars($result['sdt'], ENT_QUOTES, 'UTF-8');
    $admin_avatar = !empty($result['anh']) ? htmlspecialchars($result['anh'], ENT_QUOTES, 'UTF-8') : "https://i.pravatar.cc/100";
    
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
        $sql = "DELETE FROM user
                WHERE iduser IN (
                    SELECT iduser
                    FROM khxoatk
                    WHERE DATEDIFF(CURDATE(), ngaykh) >= 30
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
    <script src="script.js"></script>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="trangchu.css?v=<?= time(); ?>">
    <script src="sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="sweetalert2/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest"></script>
    <link rel="icon" href="picture/logoTD.png" type="image/png">
</head>

<body>
    <!-- Thanh navbar -->
    <nav class="navbar">
        <div class="logo-container">
            <img src="picture/logoTD.png" alt="Logo Cửa Hàng" class="logo">
            <span class="store-name"> M'K STORE</span>
        </div>

        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Tìm kiếm..." onkeyup="handleSearch(this.value)">
            <button class="mic-btn"><i class="fas fa-microphone"></i></button>
            <button class="search-btn" onclick="handleSearch(document.querySelector('.search-bar').value)">Tìm kiếm</button>
        </div>
        <div id="search-results" hidden></div>
        <!-- JavaScript -->
    <script>

            // Lấy products từ PHP
            const rawProducts = <?php echo json_encode($products); ?>;
            console.log("Raw Products:", rawProducts); // Debug
            const products = rawProducts.map(p => ({
                id: parseInt(p.idsp, 10) || 0,
                name: p.tensp || 'unknown',
                category: String(p.iddm ?? 'unknown')
            })).filter(p => p.id > 0);
            console.log("Processed Products:", products); // Debug
            
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
                    console.log("Processing product:", product); // Debug
                    const categoryKeywords = (product.category && product.category.trim() !== '') 
                        ? product.category.toLowerCase().split(/\s+/) 
                        : [];
                    const productNameWords = product.name.toLowerCase().split(/\s+/).filter(word => !/^\d+$/.test(word));
                    return {
                        keywords: [
                            ...productNameWords,
                            ...categoryKeywords,
                            ...(product.name.toLowerCase().includes("balo") ? ["cặp", "backpack", "túi đeo lưng", "túi đi học"] : []),
                            ...(product.name.toLowerCase().includes("túi") ? ["bag", "túi xách", "túi đeo"] : []),
                            ...(product.name.toLowerCase().includes("ví") ? ["wallet", "clutch", "ví tiền"] : []),
                            ...(product.name.toLowerCase().includes("đồng hồ") ? ["watch", "đồng hồ đeo tay", "đồng hồ thời trang"] : []),
                            ...(product.name.toLowerCase().includes("da") ? ["leather", "đồ da", "phụ kiện da"] : []),
                            ...(product.name.toLowerCase().includes("mắt") ? ["eye", "mỹ phẩm mắt", "phấn mắt"] : []),
                            ...(product.name.toLowerCase().includes("môi") ? ["lip", "son môi", "mỹ phẩm môi"] : []),
                            ...(product.name.toLowerCase().includes("dụng cụ") ? ["tool", "phụ kiện", "đồ dùng"] : []),
                            ...(product.name.toLowerCase().includes("vòng cổ") ? ["dây chuyền", "chuỗi ngọc", "ta2589", "vongco", "necklace"] : []),
                            ...(product.name.toLowerCase().includes("vòng lắc") ? ["vòng tay", "lắc", "bracelet"] : []),
                            ...(product.name.toLowerCase().includes("nhẫn") ? ["nhẫn đính hôn", "nhẫn cưới", "nhẫn vàng 18k", "ring"] : []),
                            ...(product.name.toLowerCase().includes("bông tai") ? ["khuyên tai", "hoa tai", "earring"] : []),
                            ...(product.name.toLowerCase().includes("gấu bông") ? ["gấu teddy", "thú nhồi bông", "teddy bear"] : [])
                        ],
                        productId: product.id
                    };
                });
                console.log("Training Data:", trainingData); // Debug

                // Tạo vocabulary
                vocabulary = products.length > 0 ? [...new Set(trainingData.flatMap(item => item.keywords))] : [];
                console.log("Vocabulary size:", vocabulary.length); // Debug
                console.log("Vocabulary:", vocabulary); // Debug

                // Kiểm tra và xóa mô hình cũ nếu vocabulary thay đổi
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
                console.log("Input vector size:", vector.length); // Debug
                console.log("Input vector:", vector); // Debug
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
                    units: 32, // Tăng số units
                    activation: 'relu',
                    inputShape: [vocabulary.length]
                }));
                model.add(tf.layers.dense({
                    units: 16, // Thêm tầng ẩn
                    activation: 'relu'
                }));
                model.add(tf.layers.dense({
                    units: Math.max(...products.map(p => p.id)) + 1 || 1,
                    activation: 'softmax'
                }));

                model.compile({
                    optimizer: tf.train.adam(0.005), // Giảm learning rate
                    loss: 'sparseCategoricalCrossentropy',
                    metrics: ['accuracy']
                });

                await model.fit(xs, ys, {
                    epochs: 150, // Tăng số epoch
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
                if (checkEmptyProducts()) {
                    console.warn("Cannot predict: No products available.");
                    return null;
                }

                // Chuẩn hóa query trước khi dự đoán
                let normalizedQuery = searchQuery.toLowerCase();
                for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                    if (normalizedQuery.includes(keyword)) {
                        normalizedQuery = normalizedQuery.replace(keyword, mapped);
                    }
                }
                const keywords = normalizedQuery.split(/\s+/);
                console.log("Normalized query keywords:", keywords); // Debug

                const inputVector = tf.tensor2d([keywordsToVector(keywords)]);
                const model = await loadModel();
                if (!model) return null;
                const prediction = model.predict(inputVector);
                const predictedId = tf.argMax(prediction, axis=1).dataSync()[0];
                console.log("Predicted ID:", predictedId); // Debug
                const product = products.find(p => p.id === predictedId) || null;
                console.log("Predicted product:", product); // Debug

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
                        // Chuẩn bị dữ liệu huấn luyện và mô hình chỉ khi ở menu-product
                        prepareTrainingData();
                        prepareTrainingTensors();
                        if (query) {
                            if (checkEmptyProducts()) {
                                console.log("Đang tìm kiếm theo key (query) khi (products.length === 0): " + query);
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
                                    // Truyền từ khóa chuẩn hóa thay vì tên sản phẩm
                                    let normalizedQuery = query.toLowerCase();
                                    for (let [keyword, mapped] of Object.entries(keywordMapping)) {
                                        if (normalizedQuery.includes(keyword)) {
                                            normalizedQuery = normalizedQuery.replace(keyword, mapped);
                                            break;
                                        }
                                    }
                                    console.log("Đang tìm kiếm theo kết quả dự đoán: " + normalizedQuery);
                                    searchProducts(normalizedQuery);
                                } else {
                                    resultsDiv.innerHTML = '<div>Không tìm thấy sản phẩm</div>';
                                    console.log("Đang tìm kiếm theo key (query) khi (predictedProduct là false): " + query);
                                    searchProducts(query);
                                }
                            } catch (e) {
                                console.error("Error in handleSearch:", e);
                                resultsDiv.innerHTML = '<div>Lỗi khi tìm kiếm. Vui lòng thử lại.</div>';
                                console.log("Đang tìm kiếm theo key (query): " + query);
                                searchProducts(query);
                            }
                        } else {
                            console.log("Đang tìm kiếm theo key (query) khi lỗi (predictedProduct): " + query);
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
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "nguoidung/hienthinguoidung.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (users):", iframe.src); // Debug
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
                }
            }, 100);
        }

        function searchProducts(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "sanpham/hienthisanpham.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (products):", iframe.src); // Debug
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
            }, 100);
        }

        function searchDonHang(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "donhang/hienthidonhang.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (orders):", iframe.src); // Debug
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
                }
            }, 100);
        }

        function searchPhanHoi(query) {
            setTimeout(() => {
                let iframe = document.getElementById("Frame");
                if (iframe) {
                    iframe.src = "phanhoi/hienthiphanhoi.php?query=" + encodeURIComponent(query);
                    console.log("Iframe updated (support):", iframe.src); // Debug
                } else {
                    console.error("Không tìm thấy iframe có ID 'Frame'");
                }
            }, 100);
        }

        // Voice Search
        document.addEventListener("DOMContentLoaded", function() {
            const micButton = document.querySelector(".mic-btn");
            const searchBar = document.querySelector(".search-bar");

            if (!('webkitSpeechRecognition' in window)) {
                alert("Trình duyệt không hỗ trợ tìm kiếm bằng giọng nói.");
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
    </script>
        
        <div class="nav-buttons">
            <?php if ($_SESSION['user']['quyen'] != 1): ?>
                <button class="btn clear-storage-btn" onclick="clearLocalStorage()"><i class="fas fa-eraser"></i> Model</button>
            <?php endif; ?>
            <button class="btn trangchu" onclick="handleHomeClick()"><i class="fas fa-home"></i> Trang chủ</button>
            <script>
                // Khi bấm nút Trang chủ
                function handleHomeClick() {
                    goBackHome();
                    localStorage.setItem('homeButtonClicked', 'true');
                }

                // Khi load lại trang
                window.addEventListener('load', function() {
                    if (localStorage.getItem('homeButtonClicked') === 'true') {
                        goBackHome();
                    }
                });
                </script>
            <?php
                // Đếm số yêu cầu chưa xem (trangthai = 0) của user
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
                        <img src="<?= $admin_avatar ?>" alt="Avatar" id="menu-ttcn" onclick="taikhoancn()">
                        <p><strong><?= $admin_name ?></strong></p>
                        <p><?= $admin_phone ?></p>
                        <p><?= $admin_email ?></p>
                    </div>
                    <div class="logout" onclick="logout()">Đăng xuất</div>
                </div>
            </div>
        </div>
    </nav>
<script>
    // Khi bấm vào ảnh
function taikhoancn() {
    loadTaiKhoanCN();
    localStorage.setItem('profileMenuClicked', 'true');
    // Xóa trạng thái menu active trong localStorage
    localStorage.removeItem("activeMenu");
    localStorage.removeItem("homeButtonClicked");
    // Xóa lớp active khỏi tất cả menu items
    document.querySelectorAll(".menu-item").forEach(item => {
        item.classList.remove("active");
    });
}

// Khi load lại trang
window.addEventListener('load', function() {
    if (localStorage.getItem('profileMenuClicked') === 'true') {
        loadTaiKhoanCN();
    }
});
</script>
    <!-- Thanh menu -->
    <nav class="menu">

        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="menu-item" id="menu-user" onclick="loadDLUser()"><i class="fas fa-users"></i> Quản lý người dùng</div>
        <?php else: ?>
            <div class="menu-item" id="menu-user" onclick="loadDLUser()"><i class="fas fa-users"></i> Quản lý tài khoản cá nhân</div>
        <?php endif; ?>

        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="menu-item" id="menu-product" onclick="loadDLSanpham()"><i class="fas fa-box"></i> Quản lý sản phẩm</div>
        <?php else: ?>
            <div class="menu-item" id="menu-product" onclick="loadDLSanpham()"><i class="fas fa-box"></i> Quản lý mua sắm</div>
        <?php endif; ?>

        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="menu-item" id="menu-category" onclick="loadDLDanhmuc()">
                <i class="fas fa-list"></i> Quản lý danh mục
            </div>
            <div class="menu-item" id="menu-discount" onclick="loadDLMGG()">
                <i class="fas fa-tags"></i> Quản lý khuyến mãi
            </div>
        <?php endif; ?>
        <?php if ($_SESSION['user']['quyen'] != 1): ?>
            <div class="menu-item" id="menu-order" onclick="loadDLDonhang()"><i class="fas fa-chart-bar"></i> Quản lý đơn hàng</div>
        <?php else: ?>
            <div class="menu-item" id="menu-order" onclick="loadDLDonhang()"><i class="fas fa-chart-bar"></i> Quản lý đơn mua hàng</div>
        <?php endif; ?>
        
        <div class="menu-item" id="menu-gh" onclick="loadGH()"><i class="fas fa-shopping-cart"></i> Giỏ hàng</div>
        <div class="menu-item" id="menu-support" onclick="loadPhanHoi()"><i class="fas fa-headset"></i> Hỗ trợ khách hàng</div>
    </nav>
    <script>
        activateMenu();
        setTimeout(() => {
            let id = getActiveMenu();
            if (id === "menu-user") {
                loadDLUser(); 
                localStorage.removeItem('profileMenuClicked');
            } else
            if (id === "menu-product") {
                loadDLSanpham();
                localStorage.removeItem('profileMenuClicked');
            } else
            if (id === "menu-category") {
                loadDLDanhmuc(); 
                localStorage.removeItem('profileMenuClicked');
            } else
            if (id === "menu-order") { 
                loadDLDonhang();
                localStorage.removeItem('profileMenuClicked');
            } else
            if (id === "menu-discount") {
                loadDLMGG();
                localStorage.removeItem('profileMenuClicked');
            } else
            if (id === "menu-support") { 
                loadPhanHoi();
                localStorage.removeItem('profileMenuClicked');
            } else
            if (id === "menu-gh") {
                loadGH();
                localStorage.removeItem('profileMenuClicked');
            }

        }, 100); // Đợi 100ms để cập nhật menu
        
        function ddadmin() {
            document.getElementById("adminDropdown").classList.toggle("active");
        }
        document.addEventListener("click", function(event) {
            var dropdown = document.getElementById("adminDropdown");
            var button = document.querySelector(".taikhoan");

            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });
        handleSessionTimeout(<?= SESSION_TIMEOUT ?>);
    </script>

    <!-- Nội dung trang web -->
        <div class="main-content" id="main-content">
            <iframe id="Frame" src="" scrolling="no"></iframe>
        </div>

    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'cnuserT') {
                echo "<script>showCustomAlert('Thành Công!', 'Thông tin đã được cập nhật.', 'picture/success.png');</script>";
            } 
            else
            if ($_GET['status'] === 'cnuserF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else
            if ($_GET['status'] === 'themuserT') {
                echo "<script>showCustomAlert('Thành Công!', 'Người dùng đã được thêm vào danh sách!', 'picture/success.png');</script>";
            } 
            else
            if ($_GET['status'] === 'themuserF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else
            if ($_GET['status'] === 'cnspT') {
                echo "<script>showCustomAlert('Thành Công!', 'Thông tin sản phẩm đã được cập nhật thành công!', 'picture/success.png');</script>";
            } 
            else
            if ($_GET['status'] === 'cnspF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else
            if ($_GET['status'] === 'themspT') {
                echo "<script>showCustomAlert('Thành Công!', 'Sản phẩm đã được thêm thành công!', 'picture/success.png');</script>";
            } 
            else
            if ($_GET['status'] === 'themspF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else
            if ($_GET['status'] === 'xoaspT') {
                echo "<script>showCustomAlert('Thành Công!', 'Xóa sản phẩm thành công!', 'picture/success.png');</script>";
            } 
            else
            if ($_GET['status'] === 'xoaspF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
            else
            if ($_GET['status'] === 'cnanhuserT') {
                echo "<script>showCustomAlert('Thành Công!', 'Cập nhật ảnh thành công!', 'picture/success.png');</script>";
            } 
            else
            if ($_GET['status'] === 'cnanhuserF') {
                echo "<script>showCustomAlert('Thất bại!', '', 'picture/error.png');</script>";
            }
        }
    ?>
    <!-- Chân web -->
    <footer class="footer">
        <div class="footer-content">
            <div class="copyright">
                <span>© 2025 M'K STORE - All rights reserved.</span>
            </div>

            <div class="contact-info">
                <span><i class="fas fa-map-marker-alt"></i> Địa chỉ: 73 Nguyễn Huệ, phường 2, thành phố Vĩnh Long, tỉnh Vĩnh Long </span>
                <span><i class="fas fa-phone"></i> 0702 8045 94</span>
            </div>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-facebook-messenger"></i> Zalo</a>
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
            </div>
        </div>
    </footer>
</body>
</html>
