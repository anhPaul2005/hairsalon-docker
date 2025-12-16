<?php
// // Khởi động session để lưu thông tin đăng nhập/giỏ hàng
// session_start();

// // Thông tin kết nối Database
// $servername = "localhost";
// $username_db = "root";
// $password_db = "";
// $dbname = "hairsalon";

// try {
//     // Tạo kết nối PDO
//     $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    
//     // Thiết lập chế độ báo lỗi: Ném ra ngoại lệ (Exception) khi có lỗi SQL
//     // Giúp dễ dàng debug và bắt lỗi bằng try-catch
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//     // Thiết lập chế độ lấy dữ liệu mặc định: Trả về mảng kết hợp (Associative Array)
//     // Giúp bạn dùng $row['name'] thay vì $row[0]
//     $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
// } catch(PDOException $e) {
//     // Nếu kết nối thất bại, dừng chương trình và báo lỗi
//     die("Kết nối thất bại: " . $e->getMessage());
// }

// Khởi động session
session_start();

// ⚠️ THÔNG TIN KẾT NỐI CHO DOCKER
$servername = "db";          // TÊN SERVICE MYSQL (docker-compose)
$username_db = "hairsalon";  // MYSQL_USER
$password_db = "hairsalon";  // MYSQL_PASSWORD
$dbname = "hairsalon_db";    // MYSQL_DATABASE

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8",
        $username_db,
        $password_db
    );

    // Báo lỗi bằng Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch kiểu associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}


?>