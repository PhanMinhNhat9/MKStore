function activateMenu() {
    const menuItems = document.querySelectorAll(".menu-item");
    menuItems.forEach(item => {
        item.addEventListener("click", function () {
            menuItems.forEach(i => i.classList.remove("active"));
            this.classList.add("active");
        });
    });
}

function loadDLUser() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "hienthinguoidung.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
function capnhatsanpham(idsp) {
    let encodedId = btoa(idsp); // Mã hóa Base64
    window.location.href = "update_product_form.php?id=" + encodeURIComponent(encodedId);
}
function xoasanpham(idsp) {
    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
        let encodedId = btoa(idsp); // Mã hóa Base64
        window.location.href = "xoasanpham.php?id=" + encodeURIComponent(encodedId);
    }
}
function loadDLSanpham() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_products.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
function loadDLDanhmuc()
{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "hienthidanhmuc.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
function loadDLDonhang() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "hienthidonhang.php", true);
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
    xhr.open("POST", "hienthimgg.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
// Hàm xử lý khi nhấn nút "Thêm Người Dùng"
function themnguoidung() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "themnguoidung.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

function goBack() {
    window.location.replace("trangchuadmin.html?id=1");
}

function toggleDetails(orderId) {
    var details = document.getElementById('details-' + orderId);
    if (details.style.display === 'block') {
        details.style.display = 'none';
    } else {
        details.style.display = 'block';
    }
}
function themdanhmuc(id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "themdmcon.php", true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;
        }
    };

    // Gửi dữ liệu `iddm` đến server
    xhr.send("id=" + encodeURIComponent(id));
}

function capnhatdanhmuc() {
    alert ("cập nhật danh mục");
}
function xoadanhmuc() {
    alert ("xóa danh mục");
}