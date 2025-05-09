<?php 
require_once '../config.php';
$pdo = connectDatabase();

// Lấy danh mục
$stmt_dm = $pdo->query("SELECT iddm, tendm, loaidm, icon, mota, thoigian FROM danhmucsp ORDER BY loaidm, iddm");
$categories = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);

// Lấy sản phẩm có phân trang
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$dm = isset($_GET['iddm']) ? trim($_GET['iddm']) : '';
$products = [];
$params = [];
$limit = 8; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT sp.idsp, sp.tensp, sp.giaban, sp.anh, sp.soluong, sp.iddm,
               COALESCE(SUM(ctdh.soluong), 0) AS soluong_daban,
               COALESCE(AVG(dg.sosao), 0) AS trungbinhsao,
               (sp.soluong - COALESCE(SUM(ctdh.soluong), 0)) AS soluong_conlai,
               mg.phantram AS giamgia
        FROM sanpham sp
        LEFT JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
        LEFT JOIN donhang dh ON ctdh.iddh = dh.iddh AND dh.trangthai = 'Đã thanh toán'
        LEFT JOIN danhgia dg ON sp.idsp = dg.idsp
        LEFT JOIN magiamgia mg ON sp.iddm = mg.iddm AND CURDATE() BETWEEN mg.ngayhieuluc AND mg.ngayketthuc";

if ($query !== '' && $dm !== '') {
    $sql .= " WHERE (sp.tensp LIKE :searchTerm) AND sp.iddm = :iddm";
    $params['searchTerm'] = "%$query%";
    $params['iddm'] = $dm;
} elseif ($query !== '') {
    $sql .= " WHERE sp.tensp LIKE :searchTerm";
    $params['searchTerm'] = "%$query%";
} elseif ($dm !== '') {
    $sql .= " WHERE sp.iddm = :iddm";
    $params['iddm'] = $dm;
}

$sql .= " GROUP BY sp.idsp ORDER BY sp.thoigianthemsp DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count_sql = "SELECT COUNT(DISTINCT sp.idsp) FROM sanpham sp";
if ($query !== '' && $dm !== '') {
    $count_sql .= " WHERE (sp.tensp LIKE :searchTerm) AND sp.iddm = :iddm";
} elseif ($query !== '') {
    $count_sql .= " WHERE sp.tensp LIKE :searchTerm";
} elseif ($dm !== '') {
    $count_sql .= " WHERE sp.iddm = :iddm";
}

$count_stmt = $pdo->prepare($count_sql);
if ($query !== '') {
    $count_stmt->bindValue(':searchTerm', "%$query%");
}
if ($dm !== '') {
    $count_stmt->bindValue(':iddm', $dm);
}
$count_stmt->execute();

$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm Theo Danh Mục - Hệ Thống Bán Hàng</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f4f6f9;
        }

        /* Header */
        header {
            background-color: #5b8be3;
            color: white;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .category-menu {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background 0.2s;
        }

        .dropdown-toggle i {
            margin-right: 0.5rem;
        }

        .dropdown-menu {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            margin-top: 0.5rem;
            display: none;
            position: absolute;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
            opacity: 1;
            top: 40px;
        }

        .dropdown-item {
            color: #333;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .dropdown-item:hover,
        .dropdown-item.selected {
            background-color:rgba(0, 68, 255, 0.51);
            color: white;
        }

        /* Main Content */
        main {
            padding: 1rem;
        }

        .product-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            margin-bottom: 1rem;
        }

        .product-card.out-of-stock {
            opacity: 0.7;
            background: #f8d7da;
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .product-card-body {
            padding: 0.75rem;
        }

        .product-name {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .tooltip-custom {
            position: relative;
        }


        .tooltip-custom:hover:after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1e3a8a;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            max-width: 200px; /* Giới hạn chiều rộng */
            word-wrap: break-word; /* Ngắt từ nếu cần */
            white-space: normal; /* Cho phép xuống hàng */
            z-index: 1000;
            text-align: center; /* Tuỳ chọn: căn giữa nội dung */
        }

        .price {
            margin-bottom: 0.5rem;
        }

        .new-price {
            font-size: 1.1rem;
            color: #dc3545;
            font-weight: bold;
        }

        .old-price {
            font-size: 0.9rem;
            color: #999;
            text-decoration: line-through;
            margin-right: 0.5rem;
        }

        .discount {
            font-size: 0.9rem;
            color: #28a745;
            font-weight: bold;
        }

        .info p {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .info i {
            margin-right: 0.5rem;
            color: #007bff;
        }

        .action-button {
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: none;
            background: #ffffff;
        }

        .action-button:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
        }

        .no-products {
            text-align: center;
            color: #666;
            font-size: 1rem;
            padding: 1rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-nav {
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-nav:hover {
            background: #0056b3;
        }

        .pagination span {
            font-size: 0.9rem;
            color: #333;
        }

        /* Alerts */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.75rem;
            border-radius: 4px;
            z-index: 1000;
            display: none;
        }

        .alert-success {
            background: #28a745;
            color: white;
        }

        .alert-error {
            background: #dc3545;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-card-body {
                font-size: 0.8rem;
            }

            .category-menu {
                flex-direction: column;
                gap: 0.5rem;
            }

            .dropdown-toggle {
                width: 100%;
                justify-content: space-between;
            }

            /* Vô hiệu hóa hover trên mobile, sử dụng hành vi nhấp của Bootstrap */
            .dropdown:hover .dropdown-menu {
                display: none;
            }

            .dropdown.show .dropdown-menu {
                display: block;
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btnthem {
            background-color: #ffffff;
            color: #5b8be3;
            border: 2px solid #ffffff;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btnthem:hover {
            background-color: #5b8be3;
            color: #ffffff;
            border-color: #ffffff;
        }
    </style>
</head>
<body>
    <!-- Alerts -->
    <div id="success-alert" class="alert alert-success"></div>
    <div id="error-alert" class="alert alert-error"></div>

    <!-- Header with Dropdown Menus -->
    <header>
        <div class="container-fluid">
            <h1 class="h4 mb-3">Hệ Thống Bán Hàng</h1>
            <?php if ($_SESSION['user']['quyen'] != 1): ?>
                <button class="btnthem btn-primary btn-sm mb-3" onclick="themsanpham()" aria-label="Thêm sản phẩm">
                    <i class="fas fa-plus-circle me-1"></i> Thêm Sản Phẩm
                </button>
            <?php endif; ?>
            <nav class="category-menu">
                <?php
                $categoryTree = [];
                $selectedIddm = isset($_GET['iddm']) ? intval($_GET['iddm']) : 0;
                foreach ($categories as $category) {
                    if ($category['loaidm'] == 0) {
                        $categoryTree[$category['iddm']] = $category;
                        $categoryTree[$category['iddm']]['children'] = [];
                    } else {
                        $categoryTree[$category['loaidm']]['children'][] = $category;
                    }
                }
                ?>
                <?php foreach ($categoryTree as $parent): ?>
                    <div class="dropdown">
                        <button class="dropdown-toggle" type="button" aria-expanded="false">
                            <i class="fas fa-folder"></i>
                            <?= htmlspecialchars($parent['tendm']) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($parent['children'] as $child): ?>
                                <li>
                                    <a 
                                        class="dropdown-item <?= $selectedIddm == $child['iddm'] ? 'selected' : '' ?>" 
                                        href="?iddm=<?= $child['iddm'] ?>" 
                                        aria-label="Danh mục <?= htmlspecialchars($child['tendm']) ?>"
                                    >
                                        <?= htmlspecialchars($child['tendm']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container-fluid">
            <h2 class="h5 mb-3 fw-bold text-primary">Sản Phẩm Theo Danh Mục</h2>
            <section class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                <?php if (empty($products)): ?>
                    <div class="col">
                        <div class="no-products">Không tìm thấy sản phẩm.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $row): ?>
                        <?php
                        $soluong_conlai = max(0, $row['soluong_conlai']);
                        $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
                        $giagoc = $row['giaban'];
                        $phantram_giam = $row['giamgia'] ?? 0;
                        $gia_sau_giam = ($phantram_giam > 0) ? $giagoc * (1 - $phantram_giam / 100) : $giagoc;
                        ?>
                        <div class="col">
                            <article class="product-card <?= $classOutOfStock ?>">
                                <img 
                                    src="../<?= htmlspecialchars($row['anh']) ?>" 
                                    alt="Ảnh sản phẩm" 
                                    loading="lazy"
                                >
                                <div class="product-card-body">
                                    <h3 class="product-name tooltip-custom" data-tooltip="<?= htmlspecialchars($row['tensp']) ?>">
                                        <?= htmlspecialchars(mb_strimwidth($row['tensp'], 0, 50, "...")) ?>
                                    </h3>
                                    <div class="price">
                                        <?php if ($phantram_giam > 0): ?>
                                            <span class="old-price"><?= number_format($giagoc, 0, ",", ".") ?>đ</span>
                                            <span class="discount">-<?= (int)$phantram_giam ?>%</span>
                                            <span class="new-price"><?= number_format($gia_sau_giam, 0, ",", ".") ?>đ</span>
                                        <?php else: ?>
                                            <span class="new-price"><?= number_format($giagoc, 0, ",", ".") ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info">
                                        <p><i class="fas fa-box"></i> SL: <strong><?= (int)$row['soluong'] ?></strong></p>
                                        <p><i class="fas fa-shopping-cart"></i> Đã bán: <strong><?= (int)$row['soluong_daban'] ?></strong></p>
                                        <p><i class="fas fa-warehouse"></i> Còn lại: <strong><?= ($soluong_conlai > 0 ? $soluong_conlai : 'Hết hàng') ?></strong></p>
                                        <p><i class="fas fa-star"></i> <?= round($row['trungbinhsao'], 1) ?> sao</p>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <?php if ($_SESSION['user']['quyen'] != 1): ?>
                                            <button 
                                                class="action-button text-primary tooltip-custom"
                                                onclick="capnhatsanpham(<?= $row['idsp'] ?>)"
                                                data-tooltip="Cập nhật sản phẩm"
                                                aria-label="Cập nhật sản phẩm"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                class="action-button text-danger tooltip-custom"
                                                onclick="xoasanpham(<?= $row['idsp'] ?>)"
                                                data-tooltip="Xóa sản phẩm"
                                                aria-label="Xóa sản phẩm"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($soluong_conlai > 0): ?>
                                            <button 
                                                class="action-button text-success tooltip-custom"
                                                onclick="themvaogiohang(<?= (int)$row['idsp'] ?>, <?= (int)$row['soluong_daban'] ?>)"
                                                data-tooltip="Thêm vào giỏ hàng"
                                                aria-label="Thêm vào giỏ hàng"
                                            >
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <!-- Pagination -->
            <nav class="pagination">
                <?php
                $baseUrl = '?';
                if ($query !== '') {
                    $baseUrl .= 'query=' . urlencode($query) . '&';
                }
                if ($dm !== '') {
                    $baseUrl .= 'iddm=' . urlencode($dm) . '&';
                }
                ?>
                <?php if ($page > 1): ?>
                    <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="btn-nav">← Trước</a>
                <?php endif; ?>
                <span>Trang <?= $page ?> / <?= $total_pages ?></span>
                <?php if ($page < $total_pages): ?>
                    <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="btn-nav">Sau →</a>
                <?php endif; ?>
            </nav>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        function themsanpham() {
            window.location.href = "themsanpham.php";
        }
    </script>
</body>
</html>