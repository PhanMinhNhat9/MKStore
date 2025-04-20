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
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "phanhoi/hienthiphanhoi.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}


function themnguoidung() {
    window.location.href = "themnguoidung.php";
}

function capnhatnguoidung(iduser) {
    let encodedId = btoa(iduser);
    window.location.href = "capnhatnguoidung.php?id=" + encodeURIComponent(encodedId);
}

function xoanguoidung(iduser) {
    let confirmDelete = confirm("Xác nhận xóa?");
    if (confirmDelete) {
        let encodedId = btoa(iduser);
        window.location.href = "xoanguoidung.php?id=" + encodeURIComponent(encodedId);
    }
}

function loadDLUser() {
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "nguoidung/hienthinguoidung.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}
function loadDLSanpham() {
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "sanpham/hienthisanpham.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}
function loadDLDanhmuc()
{
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "danhmuc/hienthidanhmuc.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}
function loadDLDonhang() {
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "donhang/hienthidonhang.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}

function loadDLMGG() {
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "khuyenmai/hienthimgg.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}
function themsanpham() {
    window.location.href = "themsanpham.php";
}

function capnhatsanpham(idsp) {
    let encodedId = btoa(idsp);
    window.location.href = "capnhatsanpham.php?id=" + encodeURIComponent(encodedId);
}

function xoasanpham(idsp) {
    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
        let encodedId = btoa(idsp);
        window.location.href = "xoasanpham.php?id=" + encodeURIComponent(encodedId);
    }
}

function chat(iduser) {
    let encodedId = btoa(iduser);
    window.top.location.href = "chat.php?id=" + encodeURIComponent(encodedId);
}


function quetma() {
    window.top.location.href = "quetmaqrcode.php";

}
function themsaukhiquet(idsp) {
    let encodedId = btoa(idsp); 
    fetch("../giohang/themvaogiohang.php?id=" + encodeURIComponent(encodedId))
        .then(response => response.text()) 
        .then(data => {
            console.log("Server response:", data); 

            if (data.trim() === "success") {
                return true;
            } else {
                return false;
            }
        })
        .catch(error => {
            console.error("❌ Lỗi khi gửi yêu cầu:", error);
            showErrorMessage("Không thể kết nối đến server!");
        });
}

function themvaogiohang(idsp) {
    let encodedId = btoa(idsp); 
    fetch("../giohang/themvaogiohang.php?id=" + encodeURIComponent(encodedId))
        .then(response => response.text()) 
        .then(data => {
            console.log("Server response:", data); 

            if (data.trim() === "success") {
                showSuccessMessage("Thêm vào giỏ hàng thành công! 🛒");
            } else {
                showErrorMessage("Lỗi khi thêm vào giỏ hàng!");
            }
        })
        .catch(error => {
            console.error("❌ Lỗi khi gửi yêu cầu:", error);
            showErrorMessage("Không thể kết nối đến server!");
        });
}


function showSuccessMessage(message) {
    let alertBox = document.getElementById("success-alert");
    alertBox.innerText = message;
    alertBox.style.display = "flex";
    alertBox.classList.add("show");
    // Phát âm thanh thông báo thành công
    let audio = new Audio('../amthanh/thanhcong.mp3'); // Đặt đường dẫn file âm thanh
    audio.play();
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

    function showCustomAlert(title = "Thông báo!", text = "Nội dung thông báo.", icon = "info") {
        Swal.fire({
            title: `<strong style="font-size:18px;">${title}</strong>`,
            html: `<p style="font-size:16px; margin:0;">${text}</p>`, // dùng html để tùy chỉnh font nhỏ
            iconHtml: `<img src="${icon}" style="width: 50px; height: 50px; border: none;"/>`,
            background: "#e0f7fa",
            color: "#1565c0",
            width: "300px", 
            padding: "5px",
            customClass: {
                confirmButton: "swal-custom-button",
            }
        });
    }

    const style = document.createElement("style");
    style.innerHTML = `
        .swal-custom-button {
            width: auto !important;
            padding: 10px 20px !important;
            font-size: 16px !important;
            background-color: #4a90e2 !important;
            border-radius: 5px !important;
            display: none !important; /* Ẩn nút xác nhận */
        }
    }`;
    document.head.appendChild(style);
    
function themdmcha(id) {
    let encodedId = btoa(id);
    window.top.location.href = "themdm.php?id=" + encodeURIComponent(encodedId);
}
function themdmcon(id) {
    let encodedId = btoa(id);
    window.top.location.href = "themdm.php?id=" + encodeURIComponent(encodedId);
}

function capnhatdanhmuc() {
    window.location.href = "capnhatdanhmuc.php";
}

function loadGH() {
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "giohang/hienthigiohang.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render

}

function themmgg() {
    window.location.href = "themmgg.php";
}
function capnhatmgg(idmgg) {
    let encodedId = btoa(idmgg);
    window.location.href = "capnhatmgg.php?id=" + encodeURIComponent(encodedId);
}
function xoamgg(idmgg) {
    if (confirm("Bạn có chắc chắn muốn xóa mã giảm giá này không?")) {
        let encodedId = btoa(idmgg);
        window.location.href = "xoamgg.php?id=" + encodeURIComponent(encodedId);
    }
}
// Hàm đăng xuất
function logout() {
    window.location.href = "logout.php"; 
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
    if (window.top !== window.self) {
        window.top.location.href = "../trangchuadmin.php"; // Điều hướng thoát khỏi iframe
        
    } else {
       window.location.href = "../trangchuadmin.php"; // Điều hướng thông thường
       
    }
}

function goBackHome() {
    // Xóa trạng thái menu active trong localStorage
    localStorage.removeItem("activeMenu");

    // Xóa lớp active khỏi tất cả menu items
    document.querySelectorAll(".menu-item").forEach(item => {
        item.classList.remove("active");
    });

    // Điều hướng về trang chủ (nếu cần)
    //window.location.href = "trangchuadmin.php";
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "thongtintrangchu.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
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
        window.location.href = 'logout.php?timeout=1';
    }, sessionTimeout);
}


let selectedUserId = null;

function openModal(imageSrc, userId) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').style.display = "flex";
    selectedUserId = userId;
    document.getElementById('iduser').value = selectedUserId;

}

function closeModal() {
    document.getElementById('imageModal').style.display = "none";
}


function loadanh() {
    const fileInput = document.getElementById('fileInput');
    const modalImage = document.getElementById('modalImage');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            modalImage.src = e.target.result; // Cập nhật ảnh hiển thị
        };
        reader.readAsDataURL(fileInput.files[0]); // Đọc file ảnh
    }
}