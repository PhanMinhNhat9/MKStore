<?php 
require_once '../config.php';
$pdo = connectDatabase();

// Lấy danh mục
$stmt_dm = $pdo->query("SELECT iddm, tendm, loaidm, icon, mota, thoigian FROM danhmucsp ORDER BY loaidm, iddm");
$categories = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);

// Lấy sản phẩm có phân trang và lọc giá
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$dm = isset($_GET['iddm']) ? trim($_GET['iddm']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$products = [];
$params = [];
$limit = 10; // Số sản phẩm mỗi trang
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

$conditions = [];
if ($query !== '') {
    $conditions[] = "sp.tensp LIKE :searchTerm";
    $params['searchTerm'] = "%$query%";
}
if ($dm !== '') {
    $conditions[] = "sp.iddm = :iddm";
    $params['iddm'] = $dm;
}
if ($min_price !== '') {
    $conditions[] = "sp.giaban >= :min_price";
    $params['min_price'] = $min_price;
}
if ($max_price !== '') {
    $conditions[] = "sp.giaban <= :max_price";
    $params['max_price'] = $max_price;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
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
if (!empty($conditions)) {
    $count_sql .= " WHERE " . implode(" AND ", $conditions);
}

$count_stmt = $pdo->prepare($count_sql);
if ($query !== '') {
    $count_stmt->bindValue(':searchTerm', "%$query%");
}
if ($dm !== '') {
    $count_stmt->bindValue(':iddm', $dm);
}
if ($min_price !== '') {
    $count_stmt->bindValue(':min_price', $min_price);
}
if ($max_price !== '') {
    $count_stmt->bindValue(':max_price', $max_price);
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
    <link href="https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap" rel="stylesheet">
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        /* Header */
        header {
            background-color: #3b82f6; /* Bright blue for a vibrant look */
            color: white;
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: 1px;
        }


        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .category-select {
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            background-color: white;
            font-size: 0.9rem;
            max-width: 250px;
            outline: none;
        }

        .category-select option {
            padding: 0.5rem;
        }

        .category-select option.child {
            padding-left: 1.5rem;
        }

        .price-filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .price-filter input {
            width: 110px;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            font-size: 0.85rem;
            outline: none;
        }

        .price-filter button, .clear-filter {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .price-filter button:hover, .clear-filter:hover {
            background-color: #1e40af;
        }

        .btnthem {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s ease;
        }

        .btnthem:hover {
            background-color: #059669;
        }

        /* Main Content */
        main {
            padding: 1rem;
        }
        .product-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            margin-bottom: 0.5rem;
        }
        
        .product-card.out-of-stock {
            opacity: 0.7;
            background: #fee2e2;
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .product-card-body {
            padding: 0.5rem;
        }

        .product-name {
            font-size: 0.9rem;
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
            background-color:rgb(86, 120, 131);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            max-width: 200px;
            word-wrap: break-word;
            white-space: normal;
            z-index: 1000;
            text-align: center;
        }

        .price {
            margin-bottom: 0.5rem;
        }

        .new-price {
            font-size: 1rem;
            color: #dc2626;
            font-weight: bold;
        }

        .old-price {
            font-size: 0.8rem;
            color: #6b7280;
            text-decoration: line-through;
            margin-right: 0.5rem;
        }

        .discount {
            font-size: 0.8rem;
            color: #16a34a;
            font-weight: bold;
        }

        .info p {
            font-size: 0.75rem;
            color: #4b5563;
            margin-bottom: 0.25rem;
        }

        .info i {
            margin-right: 0.5rem;
            color: #2563eb;
        }

        .action-button {
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: none;
            background: #ffffff;
        }

        .action-button:hover {
            background-color: #e5e7eb;
            transform: translateY(-1px);
        }

        .no-products {
            text-align: center;
            color: #4b5563;
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
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-nav:hover {
            background: #1e40af;
        }

        .pagination span {
            font-size: 0.9rem;
            color: #1f2937;
        }

        /* Alerts */
        .alert {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 0.75rem;
            border-radius: 4px;
            z-index: 1000;
            display: none;
        }

        .alert-success {
            background: #16a34a;
            color: white;
        }

        .alert-error {
            background: #dc2626;
            color: white;
        }

        @media (max-width: 768px) {
    .header-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .filter-group {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }

    .category-select {
        width: 100%;
        max-width: none;
    }

    .price-filter input {
        width: 100%;
    }

    /* Điều chỉnh lưới sản phẩm */
    .row-cols-1 {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* Hiển thị 2 cột trên di động */
        gap: 0.5rem; /* Giảm khoảng cách giữa các ô */
    }

    .product-card {
        font-size: 0.8rem; /* Giảm kích thước chữ */
    }

    .product-card img {
        height: 100px; /* Giảm chiều cao ảnh */
    }

    .product-card-body {
        padding: 0.4rem; /* Giảm padding */
    }

    .product-name {
        font-size: 0.8rem; /* Giảm kích thước tên sản phẩm */
    }

    .price {
        font-size: 0.75rem; /* Giảm kích thước giá */
    }

    .new-price {
        font-size: 0.85rem; /* Giảm kích thước giá mới */
    }

    .old-price, .discount {
        font-size: 0.7rem; /* Giảm kích thước giá cũ và giảm giá */
    }

    .info p {
        font-size: 0.65rem; /* Giảm kích thước thông tin */
    }

    .action-button {
        padding: 0.2rem 0.4rem; /* Giảm kích thước nút */
        font-size: 0.7rem; /* Giảm kích thước chữ nút */
    }
}
    </style>
</head>
<body>
    <!-- Alerts -->
    <div id="success-alert" class="alert alert-success"></div>
    <div id="error-alert" class="alert alert-error"></div>

    <!-- Header -->
    <header>
        <div class="container-fluid">
            <div class="header-top">
                <h1 class="header-title">Quản lý sản phẩm</h1>
                <div class="filter-group">
                    <select class="category-select" onchange="filterByCategory(this.value)" aria-label="Chọn danh mục">
                        <option value="">Tất cả danh mục</option>
                        <?php
                        $categoryTree = [];
                        foreach ($categories as $category) {
                            if ($category['loaidm'] == 0) {
                                $categoryTree[$category['iddm']] = $category;
                                $categoryTree[$category['iddm']]['children'] = [];
                            } else {
                                $categoryTree[$category['loaidm']]['children'][] = $category;
                            }
                        }
                        foreach ($categoryTree as $parent): ?>
                            <option value="<?= $parent['iddm'] ?>" <?= $dm == $parent['iddm'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($parent['tendm']) ?>
                            </option>
                            <?php foreach ($parent['children'] as $child): ?>
                                <option value="<?= $child['iddm'] ?>" class="child" <?= $dm == $child['iddm'] ? 'selected' : '' ?>>
                                    &nbsp;&nbsp;└ <?= htmlspecialchars($child['tendm']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                    <div class="price-filter">
                        <input type="number" id="min-price" placeholder="Giá tối thiểu" value="<?= $min_price !== '' ? $min_price : '' ?>" aria-label="Giá tối thiểu">
                        <input type="number" id="max-price" placeholder="Giá tối đa" value="<?= $max_price !== '' ? $max_price : '' ?>" aria-label="Giá tối đa">
                        <button onclick="filterByPrice()">Lọc</button>
                    </div>
                    <button class="clear-filter" onclick="clearFilters()">Xóa lọc</button>
                    <?php if ($_SESSION['user']['quyen'] != 1): ?>
                        <button class="btnthem" onclick="themsanpham()" aria-label="Thêm sản phẩm">
                            <i class="fas fa-plus-circle"></i> Thêm Sản Phẩm
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container-fluid">
            <section class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
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
                $urlParams = [];
                if ($query !== '') {
                    $urlParams[] = 'query=' . urlencode($query);
                }
                if ($dm !== '') {
                    $urlParams[] = 'iddm=' . urlencode($dm);
                }
                if ($min_price !== '') {
                    $urlParams[] = 'min_price=' . urlencode($min_price);
                }
                if ($max_price !== '') {
                    $urlParams[] = 'max_price=' . urlencode($max_price);
                }
                if (!empty($urlParams)) {
                    $baseUrl .= implode('&', $urlParams) . '&';
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

        function filterByCategory(iddm) {
            const url = new URL(window.location);
            if (iddm) {
                url.searchParams.set('iddm', iddm);
            } else {
                url.searchParams.delete('iddm');
            }
            url.searchParams.delete('page'); // Reset to page 1
            window.location.href = url.toString();
        }

        function filterByPrice() {
            const minPrice = document.getElementById('min-price').value;
            const maxPrice = document.getElementById('max-price').value;
            const url = new URL(window.location);
            
            if (minPrice) {
                url.searchParams.set('min_price', minPrice);
            } else {
                url.searchParams.delete('min_price');
            }
            if (maxPrice) {
                url.searchParams.set('max_price', maxPrice);
            } else {
                url.searchParams.delete('max_price');
            }
            url.searchParams.delete('page'); // Reset to page 1
            window.location.href = url.toString();
        }

        function clearFilters() {
            const url = new URL(window.location);
            url.searchParams.delete('iddm');
            url.searchParams.delete('min_price');
            url.searchParams.delete('max_price');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
    </script>
</body>
</html>