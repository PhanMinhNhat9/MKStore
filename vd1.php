<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .profile-container {
    padding: 20px;
    font-family: 'Segoe UI', sans-serif;
    background: #f9f9f9;
}

.profile-section {
    background: white;
    padding: 16px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.profile-section h3 {
    margin-bottom: 12px;
    font-size: 18px;
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 6px;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 10px 20px;
}

.profile-grid label {
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
}

.profile-grid input {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: #f2f2f2;
    color: #333;
}

    </style>
</head>
<body>
    
<main class="profile-container">
    <!-- Thông tin cá nhân -->
    <section class="profile-section">
        <h3>Thông tin cá nhân</h3>
        <div class="profile-grid">
            <div><label>MSSV</label><input type="text" value="22004335" readonly></div>
            <div><label>Họ tên</label><input type="text" value="Nguyễn Tuấn Anh" readonly></div>
            <div><label>Giới tính</label><input type="text" value="Nam" readonly></div>
            <div><label>Ngày sinh</label><input type="text" value="2004-09-21" readonly></div>
            <div><label>Địa chỉ</label><input type="text" value="" readonly></div>
            <div><label>Nơi sinh</label><input type="text" value="Cà Mau" readonly></div>
            <div><label>CCCD</label><input type="text" value="" readonly></div>
            <div><label>Email</label><input type="text" value="22004335@st.vlute.edu.vn" readonly></div>
            <div><label>SDT</label><input type="text" value="" readonly></div>
            <div><label>Trạng thái</label><input type="text" value="Đang học" readonly></div>
        </div>
    </section>

    <!-- Thông tin lớp chuyên ngành -->
    <section class="profile-section">
        <h3>Thông tin lớp chuyên ngành</h3>
        <div class="profile-grid">
            <div><label>Mã lớp</label><input type="text" value="1CTT22A3" readonly></div>
            <div><label>Tên lớp</label><input type="text" value="ĐH Công nghệ thông tin 2022 (lớp 3)" readonly></div>
            <div><label>Tên ngành</label><input type="text" value="Công nghệ thông tin" readonly></div>
        </div>
    </section>

    <!-- Thông tin liên hệ -->
    <section class="profile-section">
        <h3>Thông tin liên hệ</h3>
        <div class="profile-grid">
            <div><label>CVHT</label><input type="text" value="Nguyễn Ngọc Hoàng Quyên" readonly></div>
            <div><label>Email CVHT</label><input type="text" value="quyenminh@vlute.edu.vn" readonly></div>
            <div><label>SDT CVHT</label><input type="text" value="" readonly></div>
            <div><label>GVQL</label><input type="text" value="GVQL" readonly></div>
            <div><label>Email GVQL</label><input type="text" value="" readonly></div>
            <div><label>SDT GVQL</label><input type="text" value="" readonly></div>
        </div>
    </section>
</main>

</body>
</html>