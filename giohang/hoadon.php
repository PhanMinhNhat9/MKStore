<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn bán hàng</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap');

        body {
            font-family: 'Roboto Mono', monospace;
            background: #e7f0fb;
            display: flex;
            justify-content: center;
            padding: 30px;
        }
        .receipt {
            background: #fff;
            width: 360px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.2);
            border: 1px solid #d0e3ff;
            color: #003366;
        }
        .receipt h2 {
            text-align: center;
            color: #0066cc;
            margin-bottom: 4px;
        }
        .receipt .info, .receipt .total {
            font-size: 14px;
            margin-top: 10px;
        }
        .receipt table {
            width: 100%;
            font-size: 14px;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .receipt table th,
        .receipt table td {
            text-align: left;
            padding: 6px 4px;
        }
        .receipt table th {
            border-bottom: 1px solid #bcd8f8;
            color: #005bb5;
        }
        .receipt table tr:nth-child(even) {
            background-color: #f5faff;
        }
        .receipt .total {
            border-top: 1px solid #bcd8f8;
            padding-top: 10px;
            margin-top: 10px;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .small {
            font-size: 12px;
            text-align: center;
            margin-top: 10px;
            color: #555;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php
require_once '../config.php';
$pdo = connectDatabase();

$iddh = isset($_GET['iddh']) ? intval($_GET['iddh']) : 0;

if ($iddh > 0) {
    $sql = "
        SELECT sp.tensp, ctdh.soluong, ctdh.gia, ctdh.giagoc, ctdh.giagiam
        FROM chitietdonhang ctdh
        JOIN sanpham sp ON ctdh.idsp = sp.idsp
        WHERE ctdh.iddh = :iddh
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':iddh', $iddh, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "ID đơn hàng không hợp lệ!";
}
?>

<div class="receipt">
    <h2>MKStore</h2>
    <div class="center">Địa chỉ:  Địa chỉ: 73 Nguyễn Huệ, phường 2, thành phố Vĩnh Long, tỉnh Vĩnh Long </div>
    <div class="center">SĐT: 0702 804 594</div>
    <hr style="border: none; border-top: 1px dashed #a2c7f5; margin: 10px 0;">

    <div class="info">
        <?php date_default_timezone_set('Asia/Ho_Chi_Minh'); ?>
        <div>📅 Ngày: <?= date('d/m/Y H:i:s') ?></div>
        <div>🧾 Mã ĐH: <?= $iddh ?></div>
        <?php 
            $stmt = $pdo->prepare("SELECT hoten FROM user WHERE iduser = :iduser");
            $stmt->bindParam(':iduser', $_SESSION['user']['iduser'], PDO::PARAM_INT);
            $stmt->execute();
        
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <div>👤 Thu ngân: <?= $row['hoten'] ?> </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>SL</th>
            <th class="right">Giá gốc</th>
            <th class="right">Giá giảm</th>
            <th class="right">Thành tiền</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row) { ?>
        <tr>
            <td><?= htmlspecialchars($row['tensp']) ?></td>
            <td><?= $row['soluong'] ?></td>
            <td class="right"><?= number_format($row['giagoc'], 0, ',', '.') ?> VNĐ</td>
            <td class="right"><?= number_format($row['giagiam'], 0, ',', '.') ?> VNĐ</td>
            <td class="right"><?= number_format($row['gia'], 0, ',', '.') ?> VNĐ</td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
        $tienmat = isset($_GET['amount']) ? intval($_GET['amount']) : 0;
        $tienthoi = isset($_GET['tienthoi']) ? intval($_GET['tienthoi']) : 0;

        $sql = "SELECT tongtien FROM donhang WHERE iddh = :iddh LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':iddh', $iddh, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="total">
        <div class="bold">Tổng cộng: <span class="right"><?= number_format($result['tongtien'], 0, ',', '.') ?> VNĐ</span></div>
        <div>Tiền mặt: <span class="right"><?= number_format($tienmat, 0, ',', '.') ?> VNĐ</span></div>
        <div>Tiền thối: <span class="right"><?= number_format($tienthoi, 0, ',', '.') ?> VNĐ</span></div>
    </div>

    <div class="small">
        --- Cảm ơn quý khách đã mua hàng tại MKStore ---<br>
        Giữ hóa đơn để đổi/trả hàng trong vòng 3 ngày.
    </div>
</div>
<?php
$idnv = $_SESSION['user']['iduser'] ?? null;
    if ($iddh > 0 && $idnv !== null) {
        $stmt = $pdo->prepare("
            INSERT INTO hoadon (iddh, idnv, tiennhan, tienthoi) 
            VALUES (:iddh, :idnv, :tiennhan, :tienthoi)
        ");
        $stmt->execute([
            ':iddh' => $iddh,
            ':idnv' => $idnv,
            ':tiennhan' => $tienmat,
            ':tienthoi' => $tienthoi
        ]);
    } else {
        echo "Thiếu thông tin để thêm hóa đơn!";
    }
?>

</body>
</html>
