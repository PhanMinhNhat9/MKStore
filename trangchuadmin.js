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
// Gọi hàm để kích hoạt menu khi tải trang
//document.addEventListener("DOMContentLoaded", activateMenu);

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
        }
    };
    xhr.send();
}
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
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "nguoidung/themnguoidung.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("nguoidung/themnguoidung.css");
        }
    };
    xhr.send();
}

function capnhatsanpham(idsp) {
    let encodedId = btoa(idsp); // Mã hóa Base64
    window.location.href = "sanpham/update_product_form.php?id=" + encodeURIComponent(encodedId);
}
function xoasanpham(idsp) {
    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
        let encodedId = btoa(idsp); // Mã hóa Base64
        window.location.href = "sanpham/xoasanpham.php?id=" + encodeURIComponent(encodedId);
    }
}
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
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "khuyenmai/themmgg.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
            reloadCSS("khuyenmai/themmgg.css");
        }
    };
    xhr.send();
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
