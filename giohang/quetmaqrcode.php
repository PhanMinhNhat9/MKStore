<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quét Sản Phẩm</title>
  <script src="https://unpkg.com/html5-qrcode"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #69b7f0, #b6d5fa);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background: white;
      border-radius: 20px;
      width: 320px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      text-align: center;
      padding: 20px 15px;
      position: relative;
    }

    .header {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-bottom: 10px;
    }

    .product-image {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 12px;
      margin: 10px auto;
      display: none;
    }

    .user-info {
      margin: 6px 0;
      color: #333;
      font-size: 14px;
    }

    .store-button {
      background-color: #28a745;
      color: white;
      padding: 8px 20px;
      border-radius: 20px;
      border: none;
      font-size: 14px;
      margin-top: 10px;
      cursor: pointer;
      display: none;
    }

    .qrcode {
      width: 180px;
      height: 180px;
      margin: 15px auto;
      border-radius: 10px;
      background-color: #f2f2f2;
      overflow: hidden;
      display: none;
    }

    .scan-text {
      font-size: 14px;
      color: #666;
      margin-top: 5px;
    }

    .scan-button, .stop-button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 14px;
      cursor: pointer;
      border-radius: 20px;
      margin-top: 10px;
      transition: 0.3s;
    }

    .scan-button:hover, .stop-button:hover {
      background-color: #0056b3;
    }

    .stop-button {
      background-color: #dc3545;
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">📷 Quét Sản Phẩm</div>

    <img id="product-image" class="product-image" src="" alt="Sản phẩm" />
    <div id="product-name" class="user-info">Tên sản phẩm</div>
    <div id="product-status" class="user-info">Trạng thái</div>
    <button id="add-button" class="store-button">➕ Thêm</button>

    <div class="qrcode" id="reader"></div>
    <p class="scan-text">Hãy đưa mã QR vào khung quét</p>

    <button class="scan-button" onclick="startScanner()">📷 Bắt đầu quét</button>
    <button class="stop-button" onclick="stopScanner()">🛑 Dừng quét</button>
  </div>

  <script>
    let scanner = null;
    let isScanning = false;

    function startScanner() {
      const readerElement = document.getElementById("reader");
      readerElement.style.display = "block";
      document.querySelector(".scan-button").style.display = "none";
      document.querySelector(".stop-button").style.display = "inline-block";

      if (!scanner) {
        scanner = new Html5Qrcode("reader");
      }

      isScanning = true;
      sessionStorage.setItem("isScanning", "true");

      scanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 160, height: 160 } },
        function (decodedText) {
          addScannedProduct(decodedText);
          console.log("Đã quét được:", decodedText);
        },
        function (errorMessage) {
          // console.warn("Lỗi quét:", errorMessage);
        }
      ).catch(err => {
        console.error("Không thể khởi động máy quét:", err);
      });
    }

    function stopScanner() {
      if (scanner) {
        scanner.stop().then(() => {
          document.getElementById("reader").style.display = "none";
          document.querySelector(".scan-button").style.display = "inline-block";
          document.querySelector(".stop-button").style.display = "none";
          isScanning = false;
          sessionStorage.removeItem("isScanning");
        }).catch(err => console.error("Lỗi dừng máy quét:", err));
      }
    }

    function addScannedProduct(productCode) {
      if (!isScanning) return;

      isScanning = false;
      document.getElementById("reader").style.display = "none";
      document.querySelector(".stop-button").style.display = "none";

      // Hiển thị giả lập dữ liệu sản phẩm (demo)
      const name = "Sản phẩm #" + productCode.slice(-4);
      const img = "https://via.placeholder.com/100?text=SP" + productCode.slice(-2);
      const status = "Đang có hàng";

      document.getElementById("product-image").src = img;
      document.getElementById("product-name").textContent = name;
      document.getElementById("product-status").textContent = status;

      document.getElementById("product-image").style.display = "block";
      document.getElementById("add-button").style.display = "inline-block";

      // Tự động quét lại sau 5s (nếu cần)
      // setTimeout(() => { isScanning = true; startScanner(); }, 5000);
    }

    window.onload = function() {
      if (sessionStorage.getItem("isScanning") === "true") {
        startScanner();
      }
    };
  </script>
</body>
</html>
