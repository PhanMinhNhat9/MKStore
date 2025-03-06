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
    fetch('get_users.php')
        .then(res => res.json())
        .then(users => {
            let table = `<button class="add-user-btn" onclick="themnguoidung()"><i class="fas fa-plus"></i> Thêm Người Dùng</button>
                        <div class="user-table-container">
                         <table class="user-table">
                         <thead>
                         <tr>
                            <th>ID</th>
                            <th>Họ Tên</th>
                            <th>Tên ĐN</th>
                            <th>Ảnh</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Địa Chỉ</th>
                            <th>Quyền</th>
                            <th>Thời Gian</th>
                            <th>Hành động</th>
                         </tr>
                         </thead>`;
            users.forEach(u => {
                table += `<tr>
                            <td>${u.iduser}</td>
                            <td>${u.hoten}</td>
                            <td>${u.tendn}</td>
                            <td><img src="${u.anh}" style="width:50px;height:50px;"></td>
                            <td>${u.email}</td>
                            <td>${u.sdt}</td>
                            <td>${u.diachi}</td>
                            <td>${u.quyen}</td>
                            <td>${u.thoigian}</td>
                            <td>
                              <button class="action-btn-user edit-btn-user" onclick="editUser(${u.iduser})"><i class="fas fa-edit"></i></button>
                              <button class="action-btn-user delete-btn-user" onclick="deleteUser(${u.iduser})"><i class="fas fa-trash"></i></button>
                            </td>
                          </tr>`;
            });

            document.getElementById('main-content').innerHTML = table + "</table></div>";
        })
        .catch(error => console.error('Lỗi khi lấy danh sách người dùng:', error));
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
    window.location.href = "themnguoidung.php";
}


// Hàm cập nhật người dùng
function editUser(id) {
    window.location.href = "LoadUpdateUser.php?id="+ id;
}

// Hàm xóa người dùng và cập nhật bảng mà không reload trang
function deleteUser(id) {
    let confirmDelete = confirm("Bạn có chắc chắn muốn xóa tài khoản này?");
    
    if (confirmDelete) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_user.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                loadDLUser(); 
            }
        };
        xhr.send("id=" + id); 
    } else {
        alert("Hủy thao tác!");
    }
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
