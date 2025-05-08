<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quét Sản Phẩm</title>
  <script src="https://unpkg.com/html5-qrcode"></script>
  <script src="../script.js"></script>
  <script src="../sweetalert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
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
      margin: 5px 5px;
      cursor: pointer;
      transition: 0.3s;
      display: none;
      margin: auto;
    }

    .store-button:hover {
      background-color: #218838;
    }

    .cart-button {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #ffc107;
      color: white;
      padding: 10px 20px;
      border-radius: 20px;
      border: none;
      font-size: 14px;
      cursor: pointer;
      transition: 0.3s;
      z-index: 1000;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .cart-button:hover {
      background-color: #e0a800;
    }

    .qrcode {
      width: 180px;
      height: 180px;
      margin: 15px auto;
      border-radius: 10px;
      background-color: #f2f2f2;
      overflow: hidden;
      display: none;
      border: 2px dashed #007bff;
    }

    #reader > div {
      width: 100% !important;
      height: 100% !important;
    }

    #reader video {
      object-fit: cover !important;
      width: 100% !important;
      height: 100% !important;
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
    .alert-success, .alert-error {
display: none;
position: fixed;
top: 20%;
left: 50%;
transform: translate(-50%, -50%);
padding: 10px 16px;
border-radius: 8px;
font-size: 14px;
font-weight: bold;
text-align: center;
z-index: 1000;
min-width: 180px;
transition: transform 0.3s ease, opacity 0.3s ease;
opacity: 0;
}

/* Thành công - màu xanh lá */
.alert-success {
background-color: white;
color: #28a745;
border: 2px solid #28a745;
box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
}

/* Lỗi - màu đỏ */
.alert-error {
background-color: white;
color: #dc3545;
border: 2px solid #dc3545;
box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
}

/* Hiệu ứng hiển thị */
.alert-success.show, .alert-error.show {
opacity: 1;
transform: translate(-50%, -50%) scale(1.1);
}
    .alert-success, .alert-error {
        font-size: 13px;
        padding: 8px 12px;
        min-width: 160px;
    }
  </style>
</head>
<body>
<div id="success-alert" class="alert-success"></div>
<div id="error-alert" class="alert-error"></div>
  <button id="cart-button" class="cart-button" onclick="viewCart()">🛒 Giỏ hàng</button>
  <div class="container">
    <div class="header">📷 Quét Sản Phẩm</div>

    <img id="product-image" class="product-image" src="" alt="Sản phẩm" />
    <div id="product-name" class="user-info">Tên sản phẩm</div>
    <div id="product-status" class="user-info">Trạng thái</div>
    <button id="add-button" class="store-button" onclick="handleAddProduct()">Thêm</button>

    <div class="qrcode" id="reader"></div>
    <p class="scan-text">Hãy đưa mã QR vào khung quét</p>

    <button class="scan-button" onclick="startScanner()">📷 Bắt đầu quét</button>
    <button class="stop-button" onclick="stopScanner()">🛑 Dừng quét</button>
  </div>

  <script>
    let currentProductCode = null;

    function handleAddProduct() {
      document.getElementById("product-image").style.display = "none";
      document.getElementById("add-button").style.display = "none";
      document.getElementById("product-name").textContent = "Tên sản phẩm";
      document.getElementById("product-status").textContent = "Trạng thái";
      var kq;
      if (currentProductCode) {
        themsaukhiquet(currentProductCode);
      }
      startScanner();
    }

    function viewCart() {
      window.location.href = "../trangchu.php";
    }

    let scanner = null;
    let isScanning = false;

    function startScanner() {
      const readerElement = document.getElementById("reader");
      readerElement.style.display = "block";
      document.querySelector(".scan-button").style.display = "none";
      document.querySelector(".stop-button").style.display = "inline-block";
      //document.getElementById("add-button").style.display = "none";

      if (!scanner) {
        scanner = new Html5Qrcode("reader");
      }

      isScanning = true;
      sessionStorage.setItem("isScanning", "true");

      scanner.start(
        { facingMode: "environment" },
        { fps: 5, qrbox: { width: 130, height: 130 } },
        function (decodedText) {
          currentProductCode = decodedText;
          addScannedProduct(decodedText);
          console.log("Đã quét được:", decodedText);
        },
        function (errorMessage) {
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
          document.getElementById("add-button").style.display = "none";
          isScanning = false;
          sessionStorage.removeItem("isScanning");
        }).catch(err => console.error("Lỗi dừng máy quét:", err));
      }
    }

    function addScannedProduct(productCode) {
      if (!isScanning) return;

      isScanning = false;
      scanner.stop().then(() => {
        document.getElementById("reader").style.display = "none";
        document.querySelector(".stop-button").style.display = "none";

        fetch('get_product.php?idsp=' + productCode)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              alert(data.error);
              // startScanner(); 
            } else {
              document.getElementById("product-image").src = "../" + data.image;
              document.getElementById("product-name").textContent = data.name;
              document.getElementById("product-image").style.display = "block";
              document.getElementById("add-button").style.display = "block";
              // startScanner(); 
            }
          })
          .catch(error => {
            console.error("Lỗi khi truy xuất dữ liệu:", error);
            startScanner(); 
          })
          .finally(() => {
      //startScanner(); // <-- gọi lại sau tất cả
    });
      }).catch(err => {
        console.error("Lỗi dừng máy quét:", err);
        startScanner(); // Mở lại khung quét nếu có lỗi dừng máy quét
      });
    }

    window.onload = function() {
      if (sessionStorage.getItem("isScanning") === "true") {
        startScanner();
      }
    };
  </script>
</body>
</html>