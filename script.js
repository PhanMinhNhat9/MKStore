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
    localStorage.removeItem("homeButtonClicked");
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "phanhoi/hienthiphanhoi.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100);
}

function xuatbaocao() {
    localStorage.removeItem("homeButtonClicked");
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "baocaotk/xuatbaocao.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100);
}

function loadThongBao() {
    localStorage.setItem('loadthongbao', 'true');
    const badge = document.querySelector('#menu-tb span');
    if (badge) {
        badge.style.display = 'none';
    }
    setTimeout(() => {
        const iframe = document.getElementById("Frame");
        iframe?.setAttribute('src', 'thongbao/hienthithongbao.php');
    }, 100);
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
    localStorage.removeItem("homeButtonClicked");
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
    localStorage.removeItem("homeButtonClicked");
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "sanpham/hienthisanpham.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}
function loadDLDanhmuc() {
    localStorage.removeItem("homeButtonClicked");
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
    localStorage.removeItem("homeButtonClicked");
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
    localStorage.removeItem("homeButtonClicked");
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "khuyenmai/hienthimgg.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100); // Đợi 100ms để đảm bảo iframe đã được render
}
function loadTaiKhoanCN() {
    localStorage.removeItem("homeButtonClicked");
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "nguoidung/quanlyttcanhan.php";
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
    window.location.href = "chat.php?id=" + encodeURIComponent(encodedId);
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
                showSuccessMessage("Thêm vào giỏ hàng thành công!");
                //alert("Thêm vào giỏ hàng thành công!");
            } else {
                showErrorMessage("Lỗi khi thêm vào giỏ hàng!");
            }
        })
        .catch(error => {
            console.error("❌ Lỗi khi gửi yêu cầu:", error);
            return false;
        });
}

function themvaogiohang(idsp, soluongdaban) {
    let encodedId = btoa(idsp); 
    fetch("../giohang/themvaogiohang.php?id=" + encodeURIComponent(encodedId) + "&sldaban=" + encodeURIComponent(soluongdaban))
        .then(response => response.text()) 
        .then(data => {
            console.log("Server response:", data);
            if (data.trim() === "null") {
                showErrorMessage("Bạn đã thêm quá số lượng hàng còn lại trong cửa hàng!");
            } else
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
    let audio = new Audio('../amthanh/thanhcong.mp3'); 
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
        html: `<p style="font-size:16px; margin:0;">${text}</p>`,
        iconHtml: `<img src="${icon}" style="width: 50px; height: 50px; border: none;"/>`,
        background: "rgb(180, 237, 255)",
        color: "#1565c0",
        width: "300px",
        padding: "5px",
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: "swal-custom-button",
        },
        timer: 3000, // tự động đóng sau 3 giây
        showConfirmButton: false // ẩn nút OK để không cần bấm
    });
}

const style = document.createElement("style");
style.innerHTML = `
    .swal-custom-button {
        width: auto !important;
        padding: 10px 20px !important;
        font-size: 16px !important;
        background-color:rgb(50, 145, 253) !important;
        border-radius: 5px !important;
    }
`;
document.head.appendChild(style);

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
    window.location.href = "capnhatmgg.php?id=" + encodeURIComponent(idmgg);
}
function xoamgg(idmgg) {
    if (confirm("Bạn có chắc chắn muốn xóa mã giảm giá này không?")) {
        window.location.href = "xoamgg.php?id=" + encodeURIComponent(idmgg);
    }
}
// Hàm đăng xuất
function logout() {
    window.location.href = "auth/logout.php"; 
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
        window.top.location.href = "../trangchu.php"; // Điều hướng thoát khỏi iframe
        
    } else {
       window.location.href = "../trangchu.php"; // Điều hướng thông thường
    }
}

function goBackHome() {
    localStorage.removeItem("activeMenu");
    document.querySelectorAll(".menu-item").forEach(item => {
        item.classList.remove("active");
    });
    localStorage.removeItem('profileMenuClicked');
    setTimeout(() => {
        let iframe = document.getElementById("Frame");
        if (iframe) {
            iframe.src = "thongtintrangchu.php";
        } else {
            console.error("Không tìm thấy iframe có ID 'Frame'");
        }
    }, 100);
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
        window.location.href = 'auth/logout.php?timeout=1';
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

// Hàm xóa localStorage
function clearLocalStorage() {
    localStorage.removeItem('search-model');
    localStorage.removeItem('search-vocabulary');
    alert('Đã xóa localStorage thành công!');
    location.reload(); // Tải lại trang để áp dụng thay đổi
}