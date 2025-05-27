<?php
require_once '../config.php';
$pdo = connectDatabase();
$sdt = $_GET['sdt'] ?? '';
$stmt = $pdo->prepare("SELECT iddh, tenkh, tongtien FROM donhang WHERE sdt = ? ORDER BY thoigian ASC LIMIT 1");
$stmt->execute([$sdt]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh Toán Đơn Hàng</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="ttthanhtoan.css?v=<?= time(); ?>">
  <style>

  </style>
</head>
<body>
  <div class="container">
    <h1>Thanh Toán Đơn Hàng</h1>
    <hr style="">
    <div class="grid">
      <div class="column" id="qrColumn">
        <div class="qr-section bank" id="qrSection">
          <img id="qrImage" src="../picture/macode.jpg" alt="QR Code">
          <div class="scan-text">Scan to Pay</div>
          <div class="bank-info" id="bankInfo">
            <img class="bank-logo" src="../picture/vietcombank.png" alt="Vietcombank Logo">
            <div class="info">
              <p><strong>Ngân hàng:</strong> VietComBank</p>
              <p><strong>Chi nhánh:</strong> Cà Mau</p>
              <p><strong>Số tài khoản:</strong> 1026994027</p>
              <p><strong>Chủ tài khoản:</strong> NGUYEN TUAN ANH</p>
            </div>
          </div>
          <div class="wallet-info" id="walletInfo" style="display: none;">
            <img class="wallet-logo" src="../picture/vnpay.png" alt="VNPay Logo">
            <div class="info">
              <p><strong>Ví điện tử:</strong> VNPay</p>
              <p><strong>Số điện thoại:</strong> 0901234567</p>
              <p><strong>Chủ tài khoản:</strong> NGUYEN TUAN ANH</p>
              <p><strong>Ghi chú:</strong> Nhập mã đơn hàng <span id="iddhDisplay"></span></p>
            </div>
          </div>
        </div>
      </div>
      <div class="column" id="infoColumn">
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
      
          <?php if ($_SESSION['user']['quyen']!=1): ?>
            <option value="cash">Tiền mặt</option>
          <?php else: ?>
            <option value="ttkhinhanhang">Thanh toán khi nhận hàng</option>
          <?php endif; ?>
          <option value="bank">Chuyển khoản</option>
          <option value="wallet">Ví điện tử</option>

        </select>
        
        <div id="cashFields">
          <label for="amountReceived">Số tiền nhận:</label>
          <input type="number" id="amountReceived" placeholder="Nhập số tiền" oninput="calculateChange()">
          <p class="tienthoi"><strong>Tiền thối:</strong> <span id="changeAmount">0 VNĐ</span></p>
        </div>

        <div id="walletFields">
          <label for="walletProvider">Chọn ví điện tử:</label>
          <select id="walletProvider">
            <option value="vnpay">VNPay</option>
          </select>
        </div>
        <div class="checkbox">
          <input type="checkbox" id="printInvoice" checked>
          <span>Hóa đơn</span>
        </div>
        <button onclick="processPayment()">Thanh Toán</button>
      </div>
    </div>
  </div>

  <script>
    function processPayment() {
      const paymentMethod = document.getElementById('paymentMethod').value;
      const walletProvider = document.getElementById('walletProvider').value;
      const iddh = document.getElementById('iddh').innerText;
      
      let status ='';
      if (paymentMethod==='wallet' || paymentMethod==='bank' || paymentMethod==='cash')
        status = 'Đã thanh toán';
      else
        status = 'Chưa thanh toán';

      const today = new Date();
      const thoigian = today.getFullYear() + "-" +
                      String(today.getMonth() + 1).padStart(2, '0') + "-" +
                      String(today.getDate()).padStart(2, '0') + " " +
                      String(today.getHours()).padStart(2, '0') + ":" +
                      String(today.getMinutes()).padStart(2, '0') + ":" +
                      String(today.getSeconds()).padStart(2, '0');
      const total = parseFloat(document.getElementById('tongtien').dataset.value) || 0;
      const amount = parseFloat(document.getElementById('amountReceived').value) || 0;
      const tienthoi = paymentMethod === 'cash' ? amount - total : 0;

      // Gửi Ajax đến file xử lý PHP
      fetch('htthanhtoan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          iddh: iddh,
          trangthai: status,
          phuongthuctt: paymentMethod === 'wallet' ? `wallet_${walletProvider}` : paymentMethod,
          thoigian: thoigian
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Thanh toán thành công!');
          if (document.getElementById('printInvoice').checked) {
            const url = `hoadon.php?iddh=${iddh}&amount=${amount}&tienthoi=${tienthoi}`;
            const printWindow = window.open(url);
            printWindow.onload = function () {
              setTimeout(() => {
                //printWindow.print();
              }, 5000);
            };
          } else {
            window.top.location.href = "../trangchu.php";
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
      const walletFields = document.getElementById('walletFields');
      const bankInfo = document.getElementById('bankInfo');
      const walletInfo = document.getElementById('walletInfo');
      const qrImage = document.getElementById('qrImage');
      const qrSection = document.getElementById('qrSection');
      const qrColumn = document.getElementById('qrColumn');
      const infoColumn = document.getElementById('infoColumn');
      const iddh = document.getElementById('iddh').innerText;
      const productSelect = document.getElementById('paymentMethod');

      // Ẩn/hiện các trường
      cashFields.style.display = method === 'cash' ? 'block' : 'none';
      walletFields.style.display = method === 'wallet' ? 'block' : 'none';
      bankInfo.style.display = method === 'bank' ? 'flex' : 'none';
      walletInfo.style.display = method === 'wallet' ? 'flex' : 'none';

      // Ẩn/hiện khung QR code và điều chỉnh bố cục
      if (method === 'cash') {
        qrSection.classList.add('hidden');
        qrColumn.style.display = 'none';
        infoColumn.classList.add('full-width');
        productSelect.classList.remove('with-margin');
      } else {
        qrSection.classList.remove('hidden');
        qrColumn.style.display = 'block';
        infoColumn.classList.remove('full-width');
        productSelect.classList.add('with-margin');
      }

      if (method === 'ttkhinhanhang') {
        qrSection.classList.add('hidden');
        qrColumn.style.display = 'none';
        infoColumn.classList.add('full-width');
        productSelect.classList.remove('with-margin');
      } else {
        qrSection.classList.remove('hidden');
        qrColumn.style.display = 'block';
        infoColumn.classList.remove('full-width');
        productSelect.classList.add('with-margin');
      }
      
      // Cập nhật màu nền và QR code
      if (method === 'wallet') {
        qrSection.classList.remove('bank');
        qrSection.classList.add('wallet');
        updateWalletInfo('vnpay');
      } else if (method === 'bank') {
        qrSection.classList.remove('wallet');
        qrSection.classList.add('bank');
        qrImage.src = '../picture/macode.jpg';
      } else {
        // Không cần cập nhật QR code cho tiền mặt vì khung bị ẩn
        
      }

      // Cập nhật mã đơn hàng trong thông tin ví
      document.getElementById('iddhDisplay').textContent = iddh;

      // Tính toán tiền thối nếu là tiền mặt
      if (method === 'cash') calculateChange();
    }

    function updateWalletInfo(provider) {
      const qrImage = document.getElementById('qrImage');
      const walletInfo = document.getElementById('walletInfo');
      const walletLogo = walletInfo.querySelector('.wallet-logo');
      const info = walletInfo.querySelector('.info');

      // Cập nhật thông tin VNPay
      qrImage.src = '../picture/qrvnpay.png'; // Thay bằng QR thực tế của bạn
      walletLogo.src = '../picture/vnpay.png';
      info.innerHTML = `
        <p><strong>Ví điện tử:</strong> VNPay</p>
        <p><strong>Số điện thoại:</strong> 0901234567</p>
        <p><strong>Chủ tài khoản:</strong> NGUYEN TUAN ANH</p>
        <p><strong>Ghi chú:</strong> Nhập mã đơn hàng <span id="iddhDisplay"></span></p>
      `;
    }

    function calculateChange() {
      const amount = parseFloat(document.getElementById('amountReceived').value) || 0;
      const total = parseFloat(document.getElementById('tongtien').dataset.value) || 0;
      document.getElementById('changeAmount').textContent = `${(amount - total).toLocaleString()} VNĐ`;
    }

    // Khởi tạo giao diện
    togglePaymentFields();
  </script>
</body>
</html>