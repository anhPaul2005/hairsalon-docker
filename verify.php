<?php
require_once 'config.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

   
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND verification_token=? AND is_verified=0");
    $stmt->execute([$email, $token]);

    if ($stmt->rowCount() > 0) {
        // Kích hoạt tài khoản
        $update = $conn->prepare("UPDATE users SET is_verified=1, verification_token=NULL WHERE email=?");
        if ($update->execute([$email])) {
            echo "
            <div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h1 style='color:green'>Kích hoạt thành công! 🎉</h1>
                <p>Tài khoản của bạn đã được xác thực.</p>
                <a href='index.php?page=login' style='background:#c5a059; color:black; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:5px;'>Đăng nhập ngay</a>
            </div>";
        } else {
            echo "Lỗi hệ thống.";
        }
    } else {
        echo "
        <div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h1 style='color:red'>Liên kết không hợp lệ hoặc đã hết hạn! ❌</h1>
            <p>Vui lòng kiểm tra lại.</p>
        </div>";
    }
} else {
    header("Location: index.php");
}
?>