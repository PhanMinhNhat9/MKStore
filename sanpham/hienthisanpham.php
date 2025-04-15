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
$limit = 4; // Số sản phẩm mỗi trang
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
    <title>Sản phẩm theo danh mục</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../trangchuadmin.js"></script>
    <link rel="stylesheet" href="hienthisanpham.css?v=<? time();?>">
</head>
<body>
<div id="success-alert" class="alert-success"></div>
<div id="error-alert" class="alert-error"></div>

<div class="sidebar">
    <h3><i class="fas fa-sitemap"></i> Cây danh mục</h3>
    <ul class="tree">
        <?php
       

$categoryTree = [];
foreach ($categories as $category) {
    if ($category['loaidm'] == 0) {
        // Danh mục cha
        $categoryTree[$category['iddm']] = $category;
        $categoryTree[$category['iddm']]['children'] = [];
    } else {
        // Danh mục con
        $categoryTree[$category['loaidm']]['children'][] = $category;
    }
}
            
        ?>
           <!-- Hiển thị danh mục -->
<?php foreach ($categoryTree as $parent): ?>
    <li class="folder">
        <i class="fas fa-folder"></i> <?= htmlspecialchars($parent['tendm']) ?>
        <ul style="display: block;">
            <?php foreach ($parent['children'] as $child): ?>
                <li class="file">
                    <a href="?iddm=<?= $child['iddm'] ?>">
                        <img style="width:20px; height:20px; border-radius:50%" src="../<?= htmlspecialchars($child['icon']) ?>">
                        <?= htmlspecialchars($child['tendm']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </li>
<?php endforeach; ?>
    </ul>
</div>

    <div class="product-wrapper">
        <?php foreach ($products as $row): ?>
            <?php
                $soluong_conlai = max(0, $row['soluong_conlai']);
                $classOutOfStock = ($soluong_conlai <= 0) ? "out-of-stock" : "";
                $giagoc = $row['giaban'];
                $phantram_giam = $row['giamgia'] ?? 0;
                $gia_sau_giam = ($phantram_giam > 0) ? $giagoc * (1 - $phantram_giam / 100) : $giagoc;
            ?>
            <div class="product-card <?= $classOutOfStock ?>">
                <img src="../<?= htmlspecialchars($row['anh']) ?>" alt="<?= htmlspecialchars($row['tensp']) ?>">
                <h3><?= htmlspecialchars(mb_strimwidth($row['tensp'], 0, 20, "...")) ?></h3>
                <p class="desc"><?= htmlspecialchars(mb_strimwidth($row['mota'], 0, 50, "...")) ?></p>
                <div class="price">
                    <?php if ($phantram_giam > 0): ?>
                        <span class="old-price"><?= number_format($giagoc, 0, ",", ".") ?>đ</span>
                        <span class="discount">-<?= (int)$phantram_giam ?>%</span><br>
                        <span class="new-price" style="color:red;">
                            <?= number_format($gia_sau_giam, 0, ",", ".") ?>đ
                        </span>
                    <?php else: ?>
                        <span class="new-price"><?= number_format($giagoc, 0, ",", ".") ?>đ</span>
                    <?php endif; ?>
                </div>
                <div class="info">
                    📦 SL: <strong><?= (int)$row['soluong'] ?></strong> | 
                    Đã bán: <strong><?= (int)$row['soluong_daban'] ?></strong><br>
                    Còn lại: <strong><?= ($soluong_conlai > 0 ? $soluong_conlai : 'Hết hàng') ?></strong><br>
                    ⭐ <?= round($row['trungbinhsao'], 1) ?> sao
                </div>
                <div class="btn-group-sp">
                    <button class="btn btn-update" onclick="capnhatsanpham(<?=$row['idsp']?>)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-delete" onclick="xoasanpham(<?=$row['idsp']?>)"><i class="fas fa-trash-alt"></i></button>
                    <?php if ($soluong_conlai > 0): ?>
                        <button class="btn btn-giohang" onclick="themvaogiohang(<?= (int)$row['idsp'] ?>)"><i class="fas fa-cart-plus"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="phantrang" style="margin-top: 20px; text-align: center;">
            <?php
            // Tạo chuỗi query giữ nguyên các tham số
            $baseUrl = '?';  
            if ($query !== '') {
                $baseUrl .= 'query=' . urlencode($query) . '&';
            }
            if ($dm !== '') {
                $baseUrl .= 'iddm=' . urlencode($dm) . '&';
            }
            ?>

            <?php if ($page > 1): ?>
                <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="btn btn-giohang">← Trước</a>
            <?php endif; ?>

            <span style="margin: 0 10px;">Trang <?= $page ?> / <?= $total_pages ?></span>

            <?php if ($page < $total_pages): ?>
                <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="btn btn-giohang">Sau →</a>
            <?php endif; ?>
        </div>

    </div>


<button class="floating-btn" onclick="themsanpham()">
    <i class="fas fa-plus"></i>
</button>

<script>
    function themsanpham() {
        window.location.href = "themsanpham.php";
    }
</script>
</body>
</html>
