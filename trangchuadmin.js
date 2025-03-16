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
    window.location.href = "nguoidung/themnguoidung.html";
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

// Chức năng quản lý sản phẩm
// function loadDLSanpham() {
//     let xhr = new XMLHttpRequest();
//     xhr.open("GET", "sanpham/hienthisanpham.php", true);
//     xhr.onreadystatechange = function () {
//         if (xhr.readyState == 4 && xhr.status == 200) {
//             document.getElementById("main-content").innerHTML = xhr.responseText;
//             reloadCSS("sanpham/hienthisanpham.css");
//         }
//     };
//     xhr.send();
// }
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

}

function capnhatsanpham(idsp) {
    let encodedId = btoa(idsp);
    window.location.href = "update_product_form.php?id=" + encodeURIComponent(encodedId);
}

function chat(iduser) {
    let encodedId = btoa(iduser);
    window.location.href = "phanhoi/chat.php?id=" + encodeURIComponent(encodedId);
}

function xoasanpham(idsp) {
    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
        let encodedId = btoa(idsp);
        window.location.href = "xoasanpham.php?id=" + encodeURIComponent(encodedId);
    }
}

function themvaogiohang(idsp) {
    let encodedId = btoa(idsp);
    let xhr = new XMLHttpRequest();
    
    xhr.open("POST", "themvaogiohang.php", true);
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


function themdmcon(id) {
    let encodedId = btoa(id);
    window.location.href = "themdmcon.php?id=" + encodeURIComponent(encodedId);
}

function capnhatdanhmuc() {
    window.top.location.href = "capnhatdanhmuc.php";
}



function loadGioHang() {
    window.location.href = "donhang/hienthigiohang.php";
}

function loadBCTK() {
    alert ("hello");
}
// function showModal(id) {
//     document.getElementById('modal-' + id).style.display = 'flex';
// }
// function closeModal(id) {
//     document.getElementById('modal-' + id).style.display = 'none';
// }

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
    window.location.href = "trangchuadmin.php";
}

// function toggleDetails(orderId) {
//     var details = document.getElementById('details-' + orderId);
//     if (details.style.display === 'block') {
//         details.style.display = 'none';
//     } else {
//         details.style.display = 'block';
//     }
// }
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

        function uploadNewImage() {
            const fileInput = document.getElementById('fileInput');
            if (fileInput.files.length === 0) {
                alert("Vui lòng chọn một ảnh mới.");
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('iduser', selectedUserId);

            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
                location.reload(); // Tải lại trang để cập nhật ảnh mới
            })
            .catch(error => console.error('Lỗi:', error));
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
        