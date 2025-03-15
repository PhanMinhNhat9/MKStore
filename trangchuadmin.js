function activateMenu() {
    const menuItems = document.querySelectorAll(".menu-item");
    const activeMenuId = localStorage.getItem("activeMenu");
    // Nếu đã lưu trạng thái trước đó, đặt menu active
    if (activeMenuId) {
        document.getElementById(activeMenuId)?.classList.add("active");
    }
    menuItems.forEach(item => {
        item.addEventListener("click", function () {
            // Xóa lớp active khỏi tất cả menu items
            menuItems.forEach(i => i.classList.remove("active"));

            // Thêm lớp active vào item được click
            this.classList.add("active");

            // Lưu trạng thái menu vào localStorage
            localStorage.setItem("activeMenu", this.id);
            // Trả về ID menu active
            return this.id; // Giá trị trả về
        });
    });
}
// Hàm để lấy ID menu đang active
function getActiveMenu() {
    return localStorage.getItem("activeMenu") || null;
}

function reloadCSS(file) {
    let link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = file + "?v=" + new Date().getTime();
    document.head.appendChild(link);
}
function loadPhanHoi() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "phanhoi/hienthiphanhoi.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("phanhoi/hienthiphanhoi.css");
        }
    };
    xhr.send();
}

// Chức năng quản lý người dùng
function loadDLUser() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "nguoidung/hienthinguoidung.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("nguoidung/hienthinguoidung.css");
        }
    };
    xhr.send();
}

function themnguoidung() {
    window.location.href = "nguoidung/themnguoidung.html";
}

function capnhatnguoidung(iduser) {
    let encodedId = btoa(iduser);
    window.location.href = "nguoidung/capnhatnguoidung.php?id=" + encodeURIComponent(encodedId);
}

function xoanguoidung(iduser) {
    let confirmDelete = confirm("Xác nhận xóa?");
    if (confirmDelete) {
        let encodedId = btoa(iduser);
        window.location.href = "nguoidung/xoanguoidung.php?id=" + encodeURIComponent(encodedId);
    }
}

// Chức năng quản lý sản phẩm
function loadDLSanpham() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "sanpham/hienthisanpham.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("sanpham/hienthisanpham.css");
        }
    };
    xhr.send();
}

function themsanpham() {

}

function capnhatsanpham(idsp) {
    let encodedId = btoa(idsp);
    window.location.href = "sanpham/update_product_form.php?id=" + encodeURIComponent(encodedId);
}
function chat(iduser) {
    let encodedId = btoa(iduser);
    window.location.href = "phanhoi/chat.php?id=" + encodeURIComponent(encodedId);
}
function xoasanpham(idsp) {
    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
        let encodedId = btoa(idsp);
        window.location.href = "sanpham/xoasanpham.php?id=" + encodeURIComponent(encodedId);
    }
}

function themvaogiohang(idsp) {
    let encodedId = btoa(idsp);
    let xhr = new XMLHttpRequest();
    
    xhr.open("POST", "donhang/themvaogiohang.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                let response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    showSuccessMessage(response.message);
                } else {
                    showErrorMessage("❌ " + response.message);
                }
            } catch (error) {
                console.error("Lỗi phân tích JSON:", error);
                showErrorMessage("❌ Phản hồi không hợp lệ từ server.");
            }
        }
    };

    xhr.send("id=" + encodeURIComponent(encodedId));
}

function showSuccessMessage(message) {
    let alertBox = document.getElementById("success-alert");
    alertBox.innerText = message;
    alertBox.style.display = "flex";
    alertBox.classList.add("show");
    setTimeout(() => {
        alertBox.classList.remove("show");
        setTimeout(() => {
            alertBox.style.display = "none";
        }, 300);
    }, 3000);
}

function showErrorMessage(message) {
    let alertBox = document.getElementById("error-alert");
    alertBox.innerText = message;
    alertBox.style.display = "flex";
    alertBox.classList.add("show");
    setTimeout(() => {
        alertBox.classList.remove("show");
        setTimeout(() => {
            alertBox.style.display = "none";
        }, 300);
    }, 3000);
}
// Chức năng quản lý danh mục
function loadDLDanhmuc()
{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "danhmuc/hienthidanhmuc.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("danhmuc/hienthidanhmuc.css");
        }
    };
    xhr.send();
}

function themdmcon(id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "danhmuc/themdmcon.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("danhmuc/themdmcon.css");
        }
    };
    xhr.send("id=" + encodeURIComponent(id));
}

function capnhatdanhmuc() {
    window.location.href = "danhmuc/capnhatdanhmuc.php";
}

function loadGioHang() {
    window.location.href = "donhang/hienthigiohang.php";
}
function loadDLDonhang() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "donhang/hienthidonhang.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
function loadBCTK() {
    alert ("hello");
}
function showModal(id) {
    document.getElementById('modal-' + id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById('modal-' + id).style.display = 'none';
}
function loadDLMGG() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "khuyenmai/hienthimgg.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("khuyenmai/hienthimgg.css");
        }
    };
    xhr.send();
}
function themmgg() {
    window.location.href = "khuyenmai/themmgg.php";
}
// Hàm đăng xuất
function logout() {
    window.location.href = "logout.php"; // Thay bằng trang xử lý đăng xuất
}
// Đổi icon khi hover
function changeIcon(button) {
    button.querySelector("i").classList.remove("fa-arrow-left");
    button.querySelector("i").classList.add("fa-check");
}

// Trả lại icon khi rời chuột
function resetIcon(button) {
    button.querySelector("i").classList.remove("fa-check");
    button.querySelector("i").classList.add("fa-arrow-left");
}
function goBack() {
    // window.location.replace("trangchuadmin.html?id=1");
    window.location.href = "../trangchuadmin.php";
}
function goBackHome() {
    // Xóa trạng thái menu active trong localStorage
    localStorage.removeItem("activeMenu");

    // Xóa lớp active khỏi tất cả menu items
    document.querySelectorAll(".menu-item").forEach(item => {
        item.classList.remove("active");
    });

    // Điều hướng về trang chủ (nếu cần)
    window.location.href = "trangchuadmin.php";
}

function toggleDetails(orderId) {
    var details = document.getElementById('details-' + orderId);
    if (details.style.display === 'block') {
        details.style.display = 'none';
    } else {
        details.style.display = 'block';
    }
}
function handleSessionTimeout(sessionTimeoutInSeconds) {
    let sessionTimeout = sessionTimeoutInSeconds * 1000; // Chuyển thành mili-giây
    let warningTime = sessionTimeout - 60000; // Cảnh báo trước 1 phút

    // Cảnh báo trước khi hết session
    setTimeout(function() {
        alert("Phiên làm việc sắp hết hạn. Hệ thống sẽ tự động đăng xuất sau 1 phút.");
    }, warningTime);

    // Chuyển hướng về trang đăng nhập khi hết hạn
    setTimeout(function() {
        window.location.href = 'GUI&dangnhap.php?timeout=1';
    }, sessionTimeout);
}


