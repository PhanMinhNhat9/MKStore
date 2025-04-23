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
$limit = 5; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT sp.idsp, sp.tensp, sp.mota, sp.giaban, sp.anh, sp.soluong, sp.iddm,
               COALESCE(SUM(ctdh.soluong), 0) AS soluong_daban,
               COALESCE(AVG(dg.sosao), 0) AS trungbinhsao,
               (sp.soluong - COALESCE(SUM(ctdh.soluong), 0)) AS soluong_conlai,
               mg.phantram AS giamgia
        FROM sanpham sp
        LEFT JOIN chitietdonhang ctdh ON sp.idsp = ctdh.idsp
        LEFT JOIN donhang dh ON ctdh.iddh = dh.iddh AND dh.trangthai = 'Đã thanh toán'
        LEFT JOIN danhgia dg ON sp.idsp = dg.idsp
        LEFT JOIN magiamgia mg ON sp.iddm = mg.iddm AND CURDATE() BETWEEN mg.ngayhieuluc AND mg.ngayketthuc";

// Điều kiện lọc theo query và iddm
if ($query !== '' && $dm !== '') {
    $sql .= " WHERE (sp.tensp LIKE :searchTerm OR sp.mota LIKE :searchTerm1) AND sp.iddm = :iddm";
    $params['searchTerm'] = "%$query%";
    $params['searchTerm1'] = "%$query%";
    $params['iddm'] = $dm;
} elseif ($query !== '') {
    $sql .= " WHERE sp.tensp LIKE :searchTerm OR sp.mota LIKE :searchTerm1";
    $params['searchTerm'] = "%$query%";
    $params['searchTerm1'] = "%$query%";
} elseif ($dm !== '') {
    $sql .= " WHERE sp.iddm = :iddm";
    $params['iddm'] = $dm;
}

$sql .= " GROUP BY sp.idsp ORDER BY sp.thoigianthemsp DESC LIMIT :limit OFFSET :offset";

// Chuẩn bị và bind
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng số trang
$count_sql = "SELECT COUNT(DISTINCT sp.idsp) FROM sanpham sp";

if ($query !== '' && $dm !== '') {
    $count_sql .= " WHERE (sp.tensp LIKE :searchTerm OR sp.mota LIKE :searchTerm1) AND sp.iddm = :iddm";
} elseif ($query !== '') {
    $count_sql .= " WHERE sp.tensp LIKE :searchTerm OR sp.mota LIKE :searchTerm1";
} elseif ($dm !== '') {
    $count_sql .= " WHERE sp.iddm = :iddm";
}

$count_stmt = $pdo->prepare($count_sql);
if ($query !== '') {
    $count_stmt->bindValue(':searchTerm', "%$query%");
    $count_stmt->bindValue(':searchTerm1', "%$query%");
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
    <link rel="stylesheet" href="hienthisanpham.css?v=<?= time(); ?>">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    <!-- Alerts -->
    <div id="success-alert" class="alert alert-success"></div>
    <div id="error-alert" class="alert alert-error"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-controls">
            <button class="hamburger" aria-label="Collapse Sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <h1>Hệ Thống Bán Hàng</h1>
        <button class="btn btn-add" onclick="themsanpham()" aria-label="Thêm sản phẩm">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </button>
        <nav class="category-tree">
            <ul class="tree">
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
                    <li class="folder">
                        <details id="details-<?= $parent['iddm'] ?>">
                            <summary>
                                <button 
                                    class="toggle-btn" 
                                    aria-expanded="false" 
                                    aria-controls="category-<?= $parent['iddm'] ?>" 
                                    aria-label="Toggle danh mục <?= htmlspecialchars($parent['tendm']) ?>"
                                    onclick="toggleCategory(this, <?= $parent['iddm'] ?>)"
                                >
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <i class="fas fa-folder"></i>
                                <span><?= htmlspecialchars($parent['tendm']) ?></span>
                            </summary>
                            <ul id="category-<?= $parent['iddm'] ?>">
                                <?php foreach ($parent['children'] as $child): ?>
                                    <li class="file">
                                        <a 
                                            href="?iddm=<?= $child['iddm'] ?>" 
                                            aria-label="Danh mục <?= htmlspecialchars($child['tendm']) ?>"
                                            class="<?= $selectedIddm == $child['iddm'] ? 'selected' : '' ?>"
                                        >
                                            ↳ <?= htmlspecialchars($child['tendm']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="container">
        <section class="product-grid">
            <?php if (empty($products)): ?>
                <p class="no-products">Không tìm thấy sản phẩm.</p>
            <?php else: ?>
                <?php foreach ($products as $row): ?>
                    <?php
                    $soluong_conlai = max(0, $row['soluong_conlai']);
                    $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
                    $giagoc = $row['giaban'];
                    $phantram_giam = $row['giamgia'] ?? 0;
                    $gia_sau_giam = ($phantram_giam > 0) ? $giagoc * (1 - $phantram_giam / 100) : $giagoc;
                    ?>
                    <article class="product-card <?= $classOutOfStock ?>">
                        <img 
                            src="../<?= htmlspecialchars($row['anh']) ?>" 
                            alt="Ảnh sản phẩm <?= htmlspecialchars($row['tensp']) ?>" 
                            loading="lazy"
                        >
                        <h3><?= htmlspecialchars(mb_strimwidth($row['tensp'], 0, 20, "...")) ?></h3>
                        <p class="desc"><?= htmlspecialchars(mb_strimwidth($row['mota'], 0, 50, "...")) ?></p>
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
                        <div class="btn-group">
                            <button 
                                class="btn btn-update" 
                                onclick="capnhatsanpham(<?= $row['idsp'] ?>)"
                                aria-label="Cập nhật sản phẩm"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button 
                                class="btn btn-delete" 
                                onclick="xoasanpham(<?= $row['idsp'] ?>)"
                                aria-label="Xóa sản phẩm"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <?php if ($soluong_conlai > 0): ?>
                                <button 
                                    class="btn btn-cart" 
                                    onclick="themvaogiohang(<?= (int)$row['idsp'] ?>)"
                                    aria-label="Thêm vào giỏ hàng"
                                >
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </article>
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
                <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="btn btn-nav">← Trước</a>
            <?php endif; ?>
            <span>Trang <?= $page ?> / <?= $total_pages ?></span>
            <?php if ($page < $total_pages): ?>
                <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="btn btn-nav">Sau →</a>
            <?php endif; ?>
        </nav>
    </main>

    <!-- JavaScript -->
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            const isExpanded = !sidebar.classList.contains('collapsed');
            document.querySelector('.hamburger').setAttribute('aria-expanded', isExpanded);
        }

        // Toggle Category and Save State
        function toggleCategory(button, iddm) {
            const details = button.closest('details');
            const isOpen = !details.open;
            details.open = isOpen;
            updateToggleButton(button, isOpen);
            // Save expanded state to localStorage
            let expandedCategories = JSON.parse(localStorage.getItem('expandedCategories') || '[]');
            if (isOpen) {
                if (!expandedCategories.includes(iddm)) {
                    expandedCategories.push(iddm);
                }
            } else {
                expandedCategories = expandedCategories.filter(id => id !== iddm);
            }
            localStorage.setItem('expandedCategories', JSON.stringify(expandedCategories));
        }

        // Update Toggle Button State
        function updateToggleButton(button, isOpen) {
            button.setAttribute('aria-expanded', isOpen);
            const icon = button.querySelector('i');
            icon.classList.toggle('fa-chevron-right', !isOpen);
            icon.classList.toggle('fa-chevron-down', isOpen);
        }

        // Initialize Category Tree
        document.addEventListener('DOMContentLoaded', () => {
            const selectedIddm = <?= json_encode($selectedIddm) ?>;
            const categoryTree = <?= json_encode($categoryTree) ?>;
            
            // Restore expanded state from localStorage
            let expandedCategories = JSON.parse(localStorage.getItem('expandedCategories') || '[]');
            
            // Ensure parent of selected category is expanded
            if (selectedIddm) {
                for (let parentId in categoryTree) {
                    const children = categoryTree[parentId].children;
                    if (children.some(child => child.iddm === selectedIddm)) {
                        if (!expandedCategories.includes(parseInt(parentId))) {
                            expandedCategories.push(parseInt(parentId));
                        }
                    }
                }
                localStorage.setItem('expandedCategories', JSON.stringify(expandedCategories));
            }

            // Apply expanded state
            document.querySelectorAll('.category-tree details').forEach(details => {
                const iddm = parseInt(details.id.replace('details-', ''));
                const button = details.querySelector('.toggle-btn');
                if (expandedCategories.includes(iddm)) {
                    details.open = true;
                    updateToggleButton(button, true);
                }
                // Update button state when <details> is toggled
                details.addEventListener('toggle', () => {
                    updateToggleButton(button, details.open);
                });
            });
        });

        // Placeholder for themsanpham
        function themsanpham() {
            window.location.href = "themsanpham.php";
        }
    </script>
</body>
</html>