<?php
// --- CẤU HÌNH PHPMAILER (Gửi Email) ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Đảm bảo thư mục PHPMailer nằm cùng cấp với index.php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendActivationEmail($email, $name, $token) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình Server Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // --- CẤU HÌNH EMAIL CỦA BẠN ---
        // Hãy thay lại thông tin thật của bạn vào đây khi chạy
        $mail->Username   = 'nguyenhoangluc.profile@gmail.com'; 
        $mail->Password   = 'ircu wqrb howm lyoj'; 
        // ------------------------------
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Người gửi & Người nhận
        $mail->setFrom('no-reply@hairsalon.com', 'AL BarberShop');
        $mail->addAddress($email, $name);

        // Nội dung Email
        $mail->isHTML(true);
        $mail->Subject = 'Kích hoạt tài khoản AL BarberShop';
        
        // Link kích hoạt (Thay localhost bằng tên miền thật khi deploy)
        $link = "http://localhost/hairsalon/verify.php?email=$email&token=$token";
        
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd;'>
                <h2 style='color: #c5a059;'>Xin chào $name!</h2>
                <p>Cảm ơn bạn đã đăng ký tài khoản tại <b>AL BarberShop</b>.</p>
                <p>Vui lòng nhấn vào nút bên dưới để kích hoạt tài khoản của bạn:</p>
                <a href='$link' style='background-color: #c5a059; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>KÍCH HOẠT TÀI KHOẢN</a>
                <p>Hoặc copy đường dẫn này: <br> $link</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; // Gửi thất bại
    }
}

// --- 3. LOGIC ADMIN ---
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    
    // A. Quản lý Sản phẩm
    if (isset($_POST['save_product'])) {
        $id = $_POST['p_id'];
        $name = $_POST['p_name'];
        $price = $_POST['p_price'];
        $img = $_POST['p_image'];
        
        if ($id) {
            $sql = "UPDATE products SET name=?, price=?, image=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $price, $img, $id]);
        } else {
            $sql = "INSERT INTO products (name, price, image) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $price, $img]);
        }
        echo "<script>alert('Đã lưu sản phẩm!'); location.href='index.php?page=admin';</script>";
        exit();
    }

    if (isset($_GET['delete_product'])) {
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$_GET['delete_product']]);
        header("Location: index.php?page=admin");
        exit();
    }
    
    // B. Quản lý Thợ (Stylist)
    if (isset($_POST['save_stylist'])) {
        $id = $_POST['s_id'];
        $name = $_POST['s_name'];
        $exp = $_POST['s_exp'];
        $ava = $_POST['s_avatar'];
        
        if ($id) {
            $sql = "UPDATE stylists SET name=?, experience=?, avatar=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $exp, $ava, $id]);
        } else {
            $sql = "INSERT INTO stylists (name, experience, avatar) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $exp, $ava]);
        }
        echo "<script>alert('Đã lưu thông tin thợ!'); location.href='index.php?page=admin';</script>";
        exit();
    }

    if (isset($_GET['delete_stylist'])) {
        $stmt = $conn->prepare("DELETE FROM stylists WHERE id=?");
        $stmt->execute([$_GET['delete_stylist']]);
        header("Location: index.php?page=admin");
        exit();
    }
    
    // C. Duyệt & Từ chối Booking
    if (isset($_GET['confirm_booking'])) { 
        $stmt = $conn->prepare("UPDATE bookings SET status='confirmed', reject_reason=NULL WHERE id=?");
        $stmt->execute([$_GET['confirm_booking']]);
        header("Location: index.php?page=admin");
        exit();
    }
    
    if (isset($_GET['reject_booking']) && isset($_GET['reason'])) {
        $stmt = $conn->prepare("UPDATE bookings SET status='rejected', reject_reason=? WHERE id=?");
        $stmt->execute([$_GET['reason'], $_GET['reject_booking']]);
        header("Location: index.php?page=admin");
        exit();
    }

    // D. Duyệt & Từ chối Đơn hàng
    if (isset($_GET['confirm_order'])) { 
        $stmt = $conn->prepare("UPDATE orders SET status='confirmed', reject_reason=NULL WHERE id=?");
        $stmt->execute([$_GET['confirm_order']]);
        header("Location: index.php?page=admin");
        exit();
    }
    
    if (isset($_GET['reject_order']) && isset($_GET['reason'])) {
        $stmt = $conn->prepare("UPDATE orders SET status='rejected', reject_reason=? WHERE id=?");
        $stmt->execute([$_GET['reason'], $_GET['reject_order']]);
        header("Location: index.php?page=admin");
        exit();
    }
}

// --- 4. LOGIC USER ---

// Đăng Ký (Có gửi Email xác thực & Kiểm tra mật khẩu mạnh)
if (isset($_POST['register'])) {
    $u = $_POST['reg_username'];
    $p = $_POST['reg_password']; 
    $e = $_POST['reg_email'];
    $fn = $_POST['reg_fullname'];
    $token = bin2hex(random_bytes(16)); // Tạo token xác thực

    // [QUAN TRỌNG] Kiểm tra mật khẩu mạnh (Server Side)
    $uppercase = preg_match('@[A-Z]@', $p);
    $lowercase = preg_match('@[a-z]@', $p);
    $number    = preg_match('@[0-9]@', $p);
    $special   = preg_match('@[^\w]@', $p);

    if(!$uppercase || !$lowercase || !$number || !$special || strlen($p) < 8) {
        echo "<script>
            alert('Mật khẩu yếu! Phải có ít nhất 8 ký tự, bao gồm: Chữ hoa, Chữ thường, Số và Ký tự đặc biệt.');
            window.history.back();
        </script>";
        exit(); 
    }

    // Kiểm tra trùng username hoặc email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->execute([$u, $e]);
    
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Tên tài khoản hoặc Email đã tồn tại!');</script>";
    } else {
        // Lưu user với trạng thái chưa kích hoạt (is_verified = 0)
        $sql = "INSERT INTO users (username, password, email, fullname, role, verification_token, is_verified) VALUES (?, ?, ?, ?, 'user', ?, 0)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$u, $p, $e, $fn, $token])) {
            // Gửi Email
            if (sendActivationEmail($e, $fn, $token)) {
                echo "<script>alert('Đăng ký thành công! Vui lòng kiểm tra Email để kích hoạt tài khoản.'); window.location.href='index.php?page=login';</script>";
            } else {
                echo "<script>alert('Đăng ký thành công nhưng không gửi được email. Vui lòng liên hệ Admin.'); window.location.href='index.php?page=login';</script>";
            }
            exit();
        } else {
            echo "<script>alert('Lỗi hệ thống: " . $stmt->errorInfo()[2] . "');</script>";
        }
    }
}

// Đăng Nhập (Có kiểm tra xác thực)
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    
    // Tài khoản cứng (Luôn vào được)
    if (($u == 'admin' && $p == '123') || ($u == 'demo' && $p == '123')) {
        $_SESSION['user'] = ($u == 'admin') ? 'Admin' : 'Khách Demo';
        $_SESSION['fullname'] = ($u == 'admin') ? 'Quản Trị Viên' : 'Khách Demo';
        $_SESSION['role'] = ($u == 'admin') ? 'admin' : 'user';
        header("Location: index.php?page=home"); 
        exit();
    } else {
        // Kiểm tra Database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->execute([$u, $p]);
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            // Kiểm tra trạng thái kích hoạt
            if ($row['is_verified'] == 0) {
                echo "<script>alert('Tài khoản chưa được kích hoạt. Vui lòng kiểm tra Email!');</script>";
            } else {
                $_SESSION['user'] = $row['username']; 
                $_SESSION['role'] = $row['role'];
                $_SESSION['fullname'] = !empty($row['fullname']) ? $row['fullname'] : $row['username'];
                header("Location: index.php?page=home"); 
                exit();
            }
        } else { 
            echo "<script>alert('Sai thông tin đăng nhập!');</script>"; 
        }
    }
}

// Đổi Mật Khẩu (Có kiểm tra mật khẩu mạnh)
if (isset($_POST['change_password']) && isset($_SESSION['user'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $u = $_SESSION['user'];

    // Kiểm tra độ mạnh mật khẩu mới
    $uppercase = preg_match('@[A-Z]@', $new_pass);
    $lowercase = preg_match('@[a-z]@', $new_pass);
    $number    = preg_match('@[0-9]@', $new_pass);
    $special   = preg_match('@[^\w]@', $new_pass);

    if(!$uppercase || !$lowercase || !$number || !$special || strlen($new_pass) < 8) {
        echo "<script>alert('Mật khẩu mới yếu! Cần 8 ký tự gồm Hoa, Thường, Số, Ký tự đặc biệt.'); window.location.href='index.php?page=profile';</script>"; 
        exit();
    }

    if ($new_pass != $confirm_pass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->execute([$u, $old_pass]);
        
        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $stmt->execute([$new_pass, $u]);
            echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='index.php?page=profile';</script>";
            exit();
        } else {
            echo "<script>alert('Mật khẩu cũ không đúng!');</script>";
        }
    }
}

// Đăng Xuất
if (isset($_GET['action']) && $_GET['action'] == 'logout') { 
    session_destroy(); 
    header("Location: index.php"); 
    exit(); 
}

// --- GIỎ HÀNG & MUA SẮM ---

// Thêm vào giỏ
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    $id = $_POST['product_id']; 
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($qty < 1) $qty = 1;

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) { 
            $item['quantity'] += $qty; 
            $found = true; 
            break; 
        }
    }
    
    if (!$found) {
        array_push($_SESSION['cart'], [
            'id' => $_POST['product_id'], 
            'name' => $_POST['product_name'], 
            'price' => $_POST['product_price'], 
            'image' => $_POST['product_image'], 
            'quantity' => $qty 
        ]);
    }
    
    // Mở giỏ hàng ngay sau khi thêm
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'home';
    header("Location: index.php?page=" . $current_page . "&open_cart=1");
    exit();
}

// Cập nhật số lượng (+/-)
if (isset($_GET['update_qty'])) {
    $index = $_GET['update_qty'];
    $type = $_GET['type'];

    if (isset($_SESSION['cart'][$index])) {
        if ($type == 'inc') {
            $_SESSION['cart'][$index]['quantity']++;
        } elseif ($type == 'dec') {
            if ($_SESSION['cart'][$index]['quantity'] > 1) {
                $_SESSION['cart'][$index]['quantity']--;
            }
        }
    }
    
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'home';
    header("Location: index.php?page=" . $current_page . "&open_cart=1"); 
    exit();
}

// Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove_item'])) {
    $index = $_GET['remove_item'];
    if (isset($_SESSION['cart'][$index])) { 
        array_splice($_SESSION['cart'], $index, 1); 
    }
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'home';
    header("Location: index.php?page=" . $current_page . "&open_cart=1"); 
    exit();
}

// Thanh toán (Lưu đơn + Địa chỉ)
if (isset($_GET['checkout']) && isset($_SESSION['cart']) && isset($_SESSION['user'])) {
    $user = $_SESSION['user']; 
    $total = 0;
    
    // Nhận địa chỉ từ URL
    $address = isset($_GET['address']) ? urldecode($_GET['address']) : 'Tại quán';

    foreach($_SESSION['cart'] as $c) { 
        $qty = isset($c['quantity']) ? $c['quantity'] : 1;
        $total += ($c['price'] * $qty); 
    }
    
    $items_json = json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE);
    
    $sql = "INSERT INTO orders (username, items, total_price, address, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$user, $items_json, $total, $address])) { 
        unset($_SESSION['cart']); 
        echo "<script>alert('Thanh toán thành công! Đơn hàng sẽ giao đến: $address'); location.href='index.php?page=history';</script>"; 
        exit();
    }
}

// --- ĐẶT LỊCH (BOOKING) ---
if (isset($_POST['book_now'])) {
    if (!isset($_SESSION['user'])) { 
        echo "<script>alert('Vui lòng đăng nhập!');</script>"; 
    } else {
        $date = $_POST['date']; 
        $time = $_POST['time']; 
        $stylist = $_POST['stylist'];
        $phone = $_POST['phone']; 
        $customer = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['user'];
        
        $h = (int)date('H', strtotime($time));
        $booking_timestamp = strtotime("$date $time");
        $current_timestamp = time();

        if ($h < 8 || $h >= 20) { 
            echo "<script>alert('Quán chỉ mở từ 8h - 20h!');</script>"; 
        } elseif ($booking_timestamp < $current_timestamp) {
            echo "<script>alert('Lỗi: Bạn không thể đặt lịch trong quá khứ!');</script>";
        } else {
            $sql = "INSERT INTO bookings (customer_name, book_date, book_time, stylist, phone, status) VALUES (?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            
            if($stmt->execute([$customer, $date, $time, $stylist, $phone])) {
                echo "<script>alert('Đã nhận được yêu cầu, bạn có thể check trong app hoặc nhân viên liên hệ sau.'); location.href='index.php?page=history';</script>";
                exit();
            } else {
                echo "<script>alert('Lỗi đặt lịch!');</script>";
            }
        }
    }
}
?>