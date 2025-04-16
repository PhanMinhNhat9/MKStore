<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh Toán Đơn Hàng</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      background-color: #f7fafd;
      font-family: 'Arial', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .container {
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 8px 25px rgba(0, 123, 255, 0.1);
      width: 100%;
      max-width: 750px;
      padding: 25px;
      margin: 20px;
    }

    h1 {
      color: #007BFF;
      font-size: 28px;
      text-align: center;
      margin-bottom: 20px;
    }

    .grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
    }

    .column {
      flex: 1;
      min-width: 280px;
      display: flex;
      flex-direction: column;
    }

    h2 {
      color: #007BFF;
      font-size: 22px;
      margin: 0;
    }

    .qr-section {
      display: flex;
      flex-direction: column;
      justify-content: center; /* Căn giữa theo chiều dọc */
      align-items: center; /* Căn giữa theo chiều ngang */
      text-align: center;
      padding: 15px;
      background-color: #c6f7c0;
      border: 5px dashed #28a745;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 123, 255, 0.15);
      height: 100%; /* Đảm bảo phần này có chiều cao đầy đủ */
    }

    .qr-section img {
      width: 160px;
      height: 160px;
      border: 8px solid #ffffff;
      border-radius: 12px;
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 10px;
    }

    .qr-section .scan-text {
      color: #007BFF;
      font-size: 18px;
      padding: 8px 18px;
      border-radius: 10px;
      background-color: #ffffff;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .qr-section .info {
      color: #333;
      font-size: 14px;
      text-align: left;
      line-height: 1.6;
    }

    select, input[type="number"], button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: 2px solid #007BFF;
      border-radius: 5px;
      outline: none;
      margin-top: 10px;
      box-sizing: border-box;
    }

    select:focus, input[type="number"]:focus {
      border-color: #0056b3;
    }

    label {
      color: #333;
      font-size: 16px;
      font-weight: bold;
      margin-top: 10px;
    }

    .checkbox {
      display: flex;
      align-items: center;
      margin: 12px 0;
    }

    .checkbox input {
      margin-right: 10px;
    }

    button {
      background-color: #28a745;
      color: #ffffff;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #218838;
    }

    @media (max-width: 768px) {
      .grid {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Thanh Toán Đơn Hàng</h1>
    <div class="grid">
      <!-- Cột trái: QR Code -->
      <div class="column">
        <div class="qr-section">
          <img src="../picture/macode.jpg" alt="QR Code">
          <div class="scan-text">Scan to Pay</div>
          <div class="info">
            <p><strong>Ngân hàng:</strong> Ngân hàng ABC</p>
            <p><strong>Chi nhánh:</strong> Hà Nội</p>
            <p><strong>Số tài khoản:</strong> 1234567890</p>
            <p><strong>Chủ tài khoản:</strong> Công ty XYZ</p>
          </div>
        </div>
      </div>

      <!-- Cột phải: Thông tin khách hàng -->
      <div class="column">
        <h2>Thông Tin Đơn Hàng</h2>
        <div class="info-list">
          <p><strong>Tên khách hàng:</strong> Nguyễn Văn A</p>
          <p><strong>Số điện thoại:</strong> 0123 456 789</p>
          <p><strong>Mã hóa đơn:</strong> HD20250416</p>
          <p><strong>Ngày đặt hàng:</strong> 16/04/2025</p>
          <p><strong>Số tiền phải trả:</strong> 1,500,000 VNĐ</p>

          <div>
            <label for="paymentMethod">Phương thức thanh toán:</label>
            <select id="paymentMethod" onchange="togglePaymentFields()">
              <option value="cash">Tiền mặt</option>
              <option value="bank">Chuyển khoản Ngân hàng</option>
            </select>
          </div>
<br>
          <div id="cashFields">
            <div>
              <label for="amountReceived">Số tiền nhận từ khách:</label>
              <input type="number" id="amountReceived" placeholder="Nhập số tiền" oninput="calculateChange()">
            </div>
            <p><strong>Tiền thối:</strong> <span id="changeAmount">0 VNĐ</span></p>
          </div>

          <div class="checkbox">
            <input type="checkbox" id="printInvoice">
            <label for="printInvoice">In hóa đơn</label>
          </div>
        </div>

        <button onclick="processPayment()">Thanh Toán</button>
      </div>
    </div>
  </div>

  <script>
    function togglePaymentFields() {
      const paymentMethod = document.getElementById('paymentMethod').value;
      const cashFields = document.getElementById('cashFields');
      if (paymentMethod === 'bank') {
        cashFields.style.display = 'none';
      } else {
        cashFields.style.display = 'block';
        calculateChange();
      }
    }

    function calculateChange() {
      const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
      const amountDue = 1500000;
      const change = amountReceived - amountDue;
      document.getElementById('changeAmount').textContent = change >= 0 ? `${change.toLocaleString()} VNĐ` : '0 VNĐ';
    }

    function processPayment() {
      const paymentMethod = document.getElementById('paymentMethod').value;
      const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
      const amountDue = 1500000;
      const printInvoice = document.getElementById('printInvoice').checked;

      if (paymentMethod === 'cash' && amountReceived < amountDue) {
        alert('Số tiền nhận từ khách không đủ để thanh toán!');
        return;
      }

      let message = `Thanh toán thành công! Phương thức: ${paymentMethod === 'cash' ? 'Tiền mặt' : 'Chuyển khoản Ngân hàng'}`;
      if (paymentMethod === 'cash') {
        const change = amountReceived - amountDue;
        message += `\nSố tiền nhận: ${amountReceived.toLocaleString()} VNĐ, Tiền thối: ${change.toLocaleString()} VNĐ`;
      }
      if (printInvoice) {
        message += '\nHóa đơn sẽ được in.';
        window.print();
      }
      alert(message);
    }

    togglePaymentFields();
  </script>
</body>
</html>
