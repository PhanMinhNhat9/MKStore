<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh Toán Đơn Hàng</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      background: #f7fafd;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 25px rgba(0, 123, 255, 0.1);
      max-width: 700px;
      padding: 15px;
      margin: 15px;
    }
    h1 {
      color: #007BFF;
      font-size: 26px;
      text-align: center;
      margin-bottom: 15px;
      margin-top: 5px;
    }
    .grid {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .column {
      flex: 1;
      min-width: 260px;
    }
    h2 {
      color: #007BFF;
      font-size: 20px;
      margin: 0 0 10px;
    }
    .qr-section {
      background: #c6f7c0;
      border: 3px dashed #28a745;
      border-radius: 15px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 123, 255, 0.1);
    }
    .qr-section img {
      width: 140px;
      height: 140px;
      border: 5px solid #fff;
      border-radius: 10px;
      margin-bottom: 10px;
    }
    .qr-section .scan-text {
      color: #007BFF;
      font-size: 16px;
      background: #fff;
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: bold;
      margin-bottom: 8px;
    }
    .qr-section .info {
      color: #333;
      font-size: 13px;
      text-align: left;
      line-height: 1.5;
    }
    select, input[type="number"], button {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      border: 2px solid #007BFF;
      border-radius: 5px;
      margin-top: 8px;
      box-sizing: border-box;
    }
    select:focus, input[type="number"]:focus {
      border-color: #007BFF;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    label {
      color: #333;
      font-size: 15px;
      font-weight: bold;
      margin-top: 8px;
      display: block;
    }
    .checkbox {
      display: flex;
      align-items: center;
    }
    .checkbox input {
      margin-right: 8px;
    }
    button {
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 15px;
      padding: 10px;
    }
    button:hover {
      background: #218838;
    }
    @media (max-width: 768px) {
      .grid {
        flex-direction: column;
      }
    }
    .tienthoi {
      margin-top: 10px;
      margin-bottom: 5px;
    }
    select.with-margin {
  margin-bottom: 16px; /* hoặc bao nhiêu bạn cần */
}
.bank-info {
  display: flex;
  align-items: center;
  gap: 10px;
}
.bank-logo {
  width: 40px; /* Kích thước logo, điều chỉnh theo ý muốn */
  height: auto;
  border-radius: 5px;
}
  </style>
</head>
<body>
  <div class="container">
    <h1>Thanh Toán Đơn Hàng</h1>
    <div class="grid">
      <div class="column">
      <div class="qr-section">
        <img src="../picture/macode.jpg" alt="QR Code">
        <div class="scan-text">Scan to Pay</div>
        <div class="bank-info">
          <img class="bank-logo" src="../picture/vietcombank.png" alt="Vietcombank Logo">
          <div class="info">
            <p><strong>Ngân hàng:</strong> VietComBank</p>
            <p><strong>Chi nhánh:</strong> Cà Mau</p>
            <p><strong>Số tài khoản:</strong> 1026994027</p>
            <p><strong>Chủ tài khoản:</strong> NGUYEN TUAN ANH</p>
          </div>
        </div>
      </div>
      </div>
      <?php
        require_once '../config.php';
        $pdo = connectDatabase();
        $sdt = $_GET['sdt'] ?? '';
        $stmt = $pdo->prepare("SELECT iddh, tenkh, tongtien FROM donhang WHERE sdt = ?");
        $stmt->execute([$sdt]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="column">
        <h2>Thông Tin Đơn Hàng</h2>
        <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($result['tenkh'] ?? '') ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($sdt) ?></p>
        <p><strong>Mã hóa đơn:</strong> <span id="iddh"><?= htmlspecialchars($result['iddh'] ?? '') ?></span></p>
        <p><strong>Ngày đặt hàng:</strong> <?= date('d/m/Y') ?></p>
        <p id="tongtien" data-value="<?= $result['tongtien'] ?? 0 ?>">
          <strong>Số tiền phải trả:</strong> <?= number_format($result['tongtien'] ?? 0, 0, ',', '.') ?> VNĐ
        </p>
        <label for="paymentMethod">Phương thức thanh toán:</label>
        <select id="paymentMethod" onchange="togglePaymentFields()">
          <option value="cash">Tiền mặt</option>
          <option value="bank">Chuyển khoản</option>
        </select>
        <div id="cashFields">
          <label for="amountReceived">Số tiền nhận:</label>
          <input type="number" id="amountReceived" placeholder="Nhập số tiền" oninput="calculateChange()">
          <p class="tienthoi"><strong>Tiền thối:</strong> <span id="changeAmount">0 VNĐ</span></p>
        </div>
        <div class="checkbox">
          <input type="checkbox" id="printInvoice" checked>
          <span>In hóa đơn</span>
        </div>
        <button onclick="processPayment()">Thanh Toán</button>
      </div>
    </div>
  </div>
  <script>
   
function processPayment() {
  const paymentMethod = document.getElementById('paymentMethod').value;
  const iddh = document.getElementById('iddh').innerText;
  const status = 'Đã thanh toán';
  // Lấy ngày hiện tại theo định dạng YYYY-MM-DD
  const today = new Date();
  const thoigian = today.getFullYear() + "-" +
                 String(today.getMonth() + 1).padStart(2, '0') + "-" +
                 String(today.getDate()).padStart(2, '0') + " " +
                 String(today.getHours()).padStart(2, '0') + ":" +
                 String(today.getMinutes()).padStart(2, '0') + ":" +
                 String(today.getSeconds()).padStart(2, '0');
  const total = parseFloat(document.getElementById('tongtien').dataset.value) || 0;
  const amount = parseFloat(document.getElementById('amountReceived').value) || 0;
  const tienthoi = amount - total;
  //Gửi Ajax đến file xử lý PHP
  fetch('htthanhtoan.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      iddh: iddh,
      trangthai: status,
      phuongthuctt: paymentMethod,
      thoigian: thoigian
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      alert('Thanh toán thành công!');
      // Nếu cần in hóa đơn
      if (document.getElementById('printInvoice').checked) {
        const url = `hoadon.php?iddh=${iddh}&amount=${amount}&tienthoi=${tienthoi}`;
        const printWindow = window.open(url);
        printWindow.onload = function () {
          setTimeout(() => {
            //printWindow.print();
          }, 5000);
        };
      }
    } else {
      alert('Lỗi: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Lỗi:', error);
    alert('Đã xảy ra lỗi khi gửi dữ liệu.');
  });
}

    function togglePaymentFields() {
      const method = document.getElementById('paymentMethod').value;
      const cashFields = document.getElementById('cashFields');
      const productSelect = document.getElementById('paymentMethod');
      cashFields.style.display = method === 'bank' ? 'none' : 'block';
      if (method === 'cash') calculateChange();
      if (method === 'bank') {
        productSelect.classList.add('with-margin');
      } else {
        productSelect.classList.remove('with-margin');
      }
    }
    function calculateChange() {
      const amount = parseFloat(document.getElementById('amountReceived').value) || 0;
      const total = parseFloat(document.getElementById('tongtien').dataset.value) || 0;
      document.getElementById('changeAmount').textContent = `${(amount - total).toLocaleString()} VNĐ`;
    }
    togglePaymentFields();
  </script>
</body>
</html>