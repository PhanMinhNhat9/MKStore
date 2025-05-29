<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn bán hàng</title>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <script src="https://unpkg.com/docx@7.8.2/build/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
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
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0 0;
        }
        .back-button, .export-button {
            padding: 8px 16px;
            background-color: #0066cc;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .back-button:hover, .export-button:hover {
            background-color: #005bb5;
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
    <div class="center">Địa chỉ: 73 Nguyễn Huệ, phường 2, thành phố Vĩnh Long, tỉnh Vĩnh Long </div>
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
        <div>👤 Thu ngân: <?= htmlspecialchars($row['hoten']) ?> </div>
    </div>
    <?php
        $hoten=$row['hoten'];
    ?>
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

    <div class="button-container">
        <button class="back-button">Trở về</button>
        <button class="export-button">Xuất hóa đơn</button>
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

<script>
// Chuyển dữ liệu PHP sang JavaScript
const invoiceData = {
    iddh: <?= json_encode($iddh) ?>,
    hoten: <?= json_encode($hoten ?? 'Không xác định') ?>,
    ngay: <?= json_encode(date('d/m/Y H:i:s')) ?>,
    tongtien: <?= json_encode(number_format($result['tongtien'] ?? 0, 0, ',', '.')) ?>,
    tienmat: <?= json_encode(number_format($tienmat, 0, ',', '.')) ?>,
    tienthoi: <?= json_encode(number_format($tienthoi, 0, ',', '.')) ?>,
    items: [
        <?php foreach ($data as $row) { ?>
        {
            tensp: <?= json_encode($row['tensp']) ?>,
            soluong: <?= json_encode($row['soluong']) ?>,
            giagoc: <?= json_encode(number_format($row['giagoc'], 0, ',', '.')) ?>,
            giagiam: <?= json_encode(number_format($row['giagiam'], 0, ',', '.')) ?>,
            gia: <?= json_encode(number_format($row['gia'], 0, ',', '.')) ?>
        },
        <?php } ?>
    ]
};

// Gỡ lỗi: Kiểm tra dữ liệu invoiceData
console.log('invoiceData:', invoiceData);

// Xử lý nút Trở về
document.querySelector('.back-button').addEventListener('click', function() {
    Swal.fire({
        title: 'Xác nhận',
        text: 'Bạn có muốn quay về không?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Hủy',
        buttonsStyling: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.top.location.href = 'baocaodh.php';
        }
    });
});

// Xử lý nút Xuất hóa đơn
document.querySelector('.export-button').addEventListener('click', function() {
    const { Document, Packer, Paragraph, Table, TableRow, TableCell, WidthType, TextRun, AlignmentType } = docx;

    // Tạo tài liệu Word
    const doc = new Document({
        sections: [{
            properties: {},
            children: [
                // Tiêu đề
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "MKStore",
                            bold: true,
                            size: 32,
                            color: "0066CC"
                        })
                    ],
                    alignment: AlignmentType.CENTER
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Địa chỉ: 73 Nguyễn Huệ, phường 2, thành phố Vĩnh Long, tỉnh Vĩnh Long",
                            size: 24
                        })
                    ],
                    alignment: AlignmentType.CENTER
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "SĐT: 0702 804 594",
                            size: 24
                        })
                    ],
                    alignment: AlignmentType.CENTER
                }),
                new Paragraph({ text: "" }),

                // Thông tin hóa đơn
                new Paragraph({
                    children: [
                        new TextRun({
                            text: `📅 Ngày: ${invoiceData.ngay}`,
                            size: 24
                        })
                    ]
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: `🧾 Mã ĐH: ${invoiceData.iddh}`,
                            size: 24
                        })
                    ]
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: `👤 Thu ngân: ${invoiceData.hoten || 'Không xác định'}`,
                            size: 24
                        })
                    ]
                }),
                new Paragraph({ text: "" }),

                // Bảng sản phẩm
                new Table({
                    width: {
                        size: 100,
                        type: WidthType.PERCENTAGE
                    },
                    rows: [
                        new TableRow({
                            children: [
                                new TableCell({
                                    children: [new Paragraph({ text: "Sản phẩm", bold: true })],
                                    width: { size: 40, type: WidthType.PERCENTAGE }
                                }),
                                new TableCell({
                                    children: [new Paragraph({ text: "SL", bold: true })],
                                    width: { size: 10, type: WidthType.PERCENTAGE }
                                }),
                                new TableCell({
                                    children: [new Paragraph({ text: "Giá gốc", bold: true, alignment: AlignmentType.RIGHT })],
                                    width: { size: 15, type: WidthType.PERCENTAGE }
                                }),
                                new TableCell({
                                    children: [new Paragraph({ text: "Giá giảm", bold: true, alignment: AlignmentType.RIGHT })],
                                    width: { size: 15, type: WidthType.PERCENTAGE }
                                }),
                                new TableCell({
                                    children: [new Paragraph({ text: "Thành tiền", bold: true, alignment: AlignmentType.RIGHT })],
                                    width: { size: 20, type: WidthType.PERCENTAGE }
                                })
                            ]
                        }),
                        ...invoiceData.items.map(item => 
                            new TableRow({
                                children: [
                                    new TableCell({
                                        children: [new Paragraph({ text: item.tensp || '' })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ text: item.soluong.toString() })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ text: `${item.giagoc} VNĐ`, alignment: AlignmentType.RIGHT })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ text: `${item.giagiam} VNĐ`, alignment: AlignmentType.RIGHT })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ text: `${item.gia} VNĐ`, alignment: AlignmentType.RIGHT })]
                                    })
                                ]
                            })
                        )
                    ]
                }),

                // Tổng tiền
                new Paragraph({ text: "" }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Tổng cộng: ",
                            bold: true,
                            size: 24
                        }),
                        new TextRun({
                            text: `${invoiceData.tongtien} VNĐ`,
                            size: 24
                        })
                    ],
                    alignment: AlignmentType.RIGHT
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Tiền mặt: ",
                            size: 24
                        }),
                        new TextRun({
                            text: `${invoiceData.tienmat} VNĐ`,
                            size: 24
                        })
                    ],
                    alignment: AlignmentType.RIGHT
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Tiền thối: ",
                            size: 24
                        }),
                        new TextRun({
                            text: `${invoiceData.tienthoi} VNĐ`,
                            size: 24
                        })
                    ],
                    alignment: AlignmentType.RIGHT
                }),

                // Chân trang
                new Paragraph({ text: "" }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "--- Cảm ơn quý khách đã mua hàng tại MKStore ---",
                            size: 20
                        })
                    ],
                    alignment: AlignmentType.CENTER
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Giữ hóa đơn để đổi/trả hàng trong vòng 3 ngày.",
                            size: 20
                        })
                    ],
                    alignment: AlignmentType.CENTER
                })
            ]
        }]
    });

    // Tạo và tải file Word
    Packer.toBlob(doc).then(blob => {
        saveAs(blob, `HoaDon_MaDH_${invoiceData.iddh}.docx`);
        Swal.fire({
            title: 'Thành công',
            text: 'Hóa đơn đã được xuất thành công!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }).catch(error => {
        console.error('Lỗi khi xuất file Word:', error);
        Swal.fire({
            title: 'Lỗi',
            text: 'Không thể xuất hóa đơn. Vui lòng thử lại!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});
</script>
</body>
</html>