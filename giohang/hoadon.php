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
<div class="receipt">
    <h2>MKStore</h2>
    <div class="center">Địa chỉ: 123 Lê Lợi, Q.1, TP.HCM</div>
    <div class="center">SĐT: 0909 123 456</div>
    <hr style="border: none; border-top: 1px dashed #a2c7f5; margin: 10px 0;">

    <div class="info">
        <div>📅 Ngày: <?= date('d/m/Y H:i') ?></div>
        <div>🧾 Mã HĐ: HD20250416</div>
        <div>👤 Thu ngân: NV001</div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>SL</th>
            <th class="right">Giá</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Mì Hảo Hảo</td>
            <td>3</td>
            <td class="right">9.000đ</td>
        </tr>
        <tr>
            <td>Nước suối Lavie</td>
            <td>2</td>
            <td class="right">6.000đ</td>
        </tr>
        <tr>
            <td>Bánh Oreo</td>
            <td>1</td>
            <td class="right">12.000đ</td>
        </tr>
        </tbody>
    </table>

    <div class="total">
        <div class="bold">Tổng cộng: <span class="right">36.000đ</span></div>
        <div>Tiền mặt: <span class="right">50.000đ</span></div>
        <div>Tiền thối: <span class="right">14.000đ</span></div>
    </div>

    <div class="small">
        --- Cảm ơn quý khách đã mua hàng tại MKStore ---<br>
        Giữ hóa đơn để đổi/trả hàng trong vòng 3 ngày.
    </div>
</div>
</body>
</html>
