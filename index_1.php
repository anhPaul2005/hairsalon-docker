<?php
session_start();

// --- 1. KẾT NỐI DATABASE ---
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "hairsalon";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { die("Kết nối thất bại: " . $conn->connect_error); }
$conn->set_charset("utf8");

// --- 2. XỬ LÝ LOGIC CHUNG ---
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$highlight_id = isset($_GET['highlight']) ? $_GET['highlight'] : null; 

// --- 3. LOGIC ADMIN ---
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    // A. Sản phẩm
    if (isset($_POST['save_product'])) {
        $id = $_POST['p_id']; $name = $_POST['p_name']; $price = $_POST['p_price']; $img = $_POST['p_image'];
        $sql = $id ? "UPDATE products SET name='$name', price='$price', image='$img' WHERE id=$id" : "INSERT INTO products (name, price, image) VALUES ('$name', '$price', '$img')";
        $conn->query($sql); echo "<script>alert('Đã lưu!'); location.href='index.php?page=admin';</script>";
    }
    if (isset($_GET['delete_product'])) { $conn->query("DELETE FROM products WHERE id=".$_GET['delete_product']); header("Location: index.php?page=admin"); }
    
    // B. Thợ (Stylist)
    if (isset($_POST['save_stylist'])) {
        $id = $_POST['s_id']; $name = $_POST['s_name']; $exp = $_POST['s_exp']; $ava = $_POST['s_avatar'];
        $sql = $id ? "UPDATE stylists SET name='$name', experience='$exp', avatar='$ava' WHERE id=$id" : "INSERT INTO stylists (name, experience, avatar) VALUES ('$name', '$exp', '$ava')";
        $conn->query($sql); echo "<script>alert('Đã lưu!'); location.href='index.php?page=admin';</script>";
    }
    if (isset($_GET['delete_stylist'])) { $conn->query("DELETE FROM stylists WHERE id=".$_GET['delete_stylist']); header("Location: index.php?page=admin"); }
    
    // C. Duyệt & Từ chối Booking
    if (isset($_GET['confirm_booking'])) { 
        $id = $_GET['confirm_booking'];
        $conn->query("UPDATE bookings SET status='confirmed', reject_reason=NULL WHERE id=$id"); 
        header("Location: index.php?page=admin"); 
    }
    if (isset($_GET['reject_booking']) && isset($_GET['reason'])) {
        $id = $_GET['reject_booking'];
        $reason = $conn->real_escape_string($_GET['reason']);
        $conn->query("UPDATE bookings SET status='rejected', reject_reason='$reason' WHERE id=$id");
        header("Location: index.php?page=admin");
    }
}

// --- 4. LOGIC USER ---

// Đăng Ký
if (isset($_POST['register'])) {
    $u = $conn->real_escape_string($_POST['reg_username']);
    $p = $_POST['reg_password']; 
    $e = $conn->real_escape_string($_POST['reg_email']);
    $fn = $conn->real_escape_string($_POST['reg_fullname']);

    $check = $conn->query("SELECT * FROM users WHERE username='$u'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Tên tài khoản đã tồn tại!');</script>";
    } else {
        $sql = "INSERT INTO users (username, password, email, fullname, role) VALUES ('$u', '$p', '$e', '$fn', 'user')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Đăng ký thành công!'); window.location.href='index.php?page=login';</script>";
        } else { echo "<script>alert('Lỗi: " . $conn->error . "');</script>"; }
    }
}

// Đăng Nhập
if (isset($_POST['login'])) {
    $u = $conn->real_escape_string($_POST['username']); $p = $_POST['password'];
    if (($u == 'admin' && $p == '123') || ($u == 'demo' && $p == '123')) {
        $_SESSION['user'] = ($u == 'admin') ? 'Admin' : 'Khách Demo';
        $_SESSION['fullname'] = ($u == 'admin') ? 'Quản Trị Viên' : 'Khách Demo';
        $_SESSION['role'] = ($u == 'admin') ? 'admin' : 'user';
        header("Location: index.php?page=home"); exit();
    } else {
        $res = $conn->query("SELECT * FROM users WHERE username='$u' AND password='$p'");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $_SESSION['user'] = $row['username']; 
            $_SESSION['role'] = $row['role'];
            $_SESSION['fullname'] = !empty($row['fullname']) ? $row['fullname'] : $row['username'];
            header("Location: index.php?page=home"); exit();
        } else { echo "<script>alert('Sai thông tin!');</script>"; }
    }
}

// Đổi Mật Khẩu
if (isset($_POST['change_password']) && isset($_SESSION['user'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $u = $_SESSION['user'];

    if ($new_pass != $confirm_pass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    } else {
        $res = $conn->query("SELECT * FROM users WHERE username='$u' AND password='$old_pass'");
        if ($res->num_rows > 0) {
            $conn->query("UPDATE users SET password='$new_pass' WHERE username='$u'");
            echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='index.php?page=profile';</script>";
        } else {
            echo "<script>alert('Mật khẩu cũ không đúng!');</script>";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') { session_destroy(); header("Location: index.php"); }

// Giỏ hàng
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $id = $_POST['product_id']; $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) { $item['quantity']++; $found = true; break; }
    }
    if (!$found) {
        array_push($_SESSION['cart'], ['id' => $_POST['product_id'], 'name' => $_POST['product_name'], 'price' => $_POST['product_price'], 'image' => $_POST['product_image'], 'quantity' => 1]);
    }
    echo "<script>alert('Đã thêm vào giỏ!');</script>";
}
if (isset($_GET['remove_item'])) {
    $index = $_GET['remove_item'];
    if (isset($_SESSION['cart'][$index])) { array_splice($_SESSION['cart'], $index, 1); }
    header("Location: index.php?page=" . $page); exit();
}

// Thanh toán
if (isset($_GET['checkout']) && isset($_SESSION['cart']) && isset($_SESSION['user'])) {
    $user = $_SESSION['user']; $total = 0;
    foreach($_SESSION['cart'] as $c) { $qty = isset($c['quantity']) ? $c['quantity'] : 1; $total += ($c['price'] * $qty); }
    $items_json = json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE);
    $stmt = $conn->prepare("INSERT INTO orders (username, items, total_price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $user, $items_json, $total);
    if($stmt->execute()) { unset($_SESSION['cart']); echo "<script>alert('Thanh toán thành công!'); location.href='index.php?page=history';</script>"; }
}

// [CẬP NHẬT] Đặt lịch (Thêm SĐT + Thông báo mới)
if (isset($_POST['book_now'])) {
    if (!isset($_SESSION['user'])) { echo "<script>alert('Vui lòng đăng nhập!');</script>"; } 
    else {
        $date = $_POST['date']; 
        $time = $_POST['time']; 
        $stylist = $_POST['stylist'];
        $phone = $_POST['phone']; // Lấy SĐT từ form
        $customer = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['user'];
        
        $h = (int)date('H', strtotime($time));
        $booking_timestamp = strtotime("$date $time");
        $current_timestamp = time();

        if ($h < 8 || $h >= 20) { 
            echo "<script>alert('Quán chỉ mở 8h-20h!');</script>"; 
        } elseif ($booking_timestamp < $current_timestamp) {
            echo "<script>alert('Lỗi: Bạn không thể đặt lịch trong quá khứ!');</script>";
        } else {
            // Lưu SĐT vào DB
            $stmt = $conn->prepare("INSERT INTO bookings (customer_name, book_date, book_time, stylist, phone, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("sssss", $customer, $date, $time, $stylist, $phone);
            
            if($stmt->execute()) {
                // Thông báo đúng yêu cầu
                echo "<script>alert('Đã nhận được yêu cầu, bạn có thể check trong app hoặc nhân viên liên hệ sau.'); location.href='index.php?page=history';</script>";
            } else {
                echo "<script>alert('Lỗi đặt lịch!');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AL BarberShop | Luxury & Style</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* --- CSS GIỮ NGUYÊN --- */
        :root { --gold: #c5a059; --gold-light: #e6c888; --black: #111111; --dark: #1a1a1a; --gray: #2d2d2d; --text: #e0e0e0; --white: #ffffff; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--black); color: var(--text); overflow-x: hidden; }
        a { text-decoration: none; color: inherit; transition: 0.4s ease; }
        ul { list-style: none; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulseGlow { 0% { box-shadow: 0 0 0 0 rgba(197, 160, 89, 0.4); } 70% { box-shadow: 0 0 0 15px rgba(197, 160, 89, 0); } 100% { box-shadow: 0 0 0 0 rgba(197, 160, 89, 0); } }
        .animate-fade { animation: fadeIn 1s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.2s; } .delay-2 { animation-delay: 0.4s; } .delay-3 { animation-delay: 0.6s; }
        header { background: rgba(17, 17, 17, 0.95); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 1000; border-bottom: 1px solid #333; }
        .inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; }
        .logo { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--gold); letter-spacing: 2px; display: flex; align-items: center; gap: 10px; }
        .logo i { color: var(--white); }
        .nav-link { color: #aaa; margin-left: 25px; font-weight: 500; font-size: 0.95rem; position: relative; }
        .nav-link:hover, .nav-link.active { color: var(--gold); }
        .btn-cart { position: relative; color: var(--white); margin-left: 20px; }
        .cart-badge { position: absolute; top: -8px; right: -8px; background: var(--gold); color: var(--black); font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 50%; }
        .hero { height: 60vh; background: linear-gradient(to bottom, rgba(0,0,0,0.6), var(--black)), url('https://images.unsplash.com/photo-1621605815971-fbc98d6d4e59?q=80&w=2070'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: 4rem; color: var(--gold); margin-bottom: 10px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .btn-cta { padding: 12px 40px; background: var(--gold); color: var(--black); font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-radius: 50px; transition: 0.3s; animation: pulseGlow 2s infinite; }
        .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; min-height: 60vh; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--gold); border-bottom: 2px solid var(--gray); padding-bottom: 10px; display: inline-block; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background: var(--gray); border-radius: 15px; overflow: hidden; border: 1px solid #333; transition: 0.4s; position: relative; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.4); border-color: var(--gold); }
        .card img { width: 100%; height: 260px; object-fit: cover; }
        .card-body { padding: 20px; text-align: center; }
        .card-title { font-size: 1.2rem; font-weight: 600; color: var(--white); margin-bottom: 10px; }
        .card-price { color: var(--gold); font-size: 1.3rem; font-weight: bold; margin-bottom: 15px; display: block; }
        .btn-block { width: 100%; padding: 12px; background: transparent; border: 1px solid var(--gold); color: var(--gold); font-weight: 600; cursor: pointer; border-radius: 5px; }
        .btn-block:hover { background: var(--gold); color: var(--black); }
        .services-layout { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 50px; }
        .service-menu { background: #222; padding: 40px; border-radius: 20px; border: 1px solid #333; }
        .menu-item { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 25px; position: relative; }
        .item-name { flex: 1; font-size: 1.1rem; color: #eee; background: #222; z-index: 2; padding-right: 10px; }
        .item-dots { flex: 1; border-bottom: 2px dotted #444; position: relative; bottom: 5px; }
        .item-price { font-size: 1.2rem; font-weight: bold; color: var(--gold); background: #222; z-index: 2; padding-left: 10px; }
        .booking-box { background: linear-gradient(145deg, #1a1a1a, #252525); padding: 40px; border-radius: 20px; border: 2px solid var(--gold); }
        .booking-input { background: rgba(0,0,0,0.3); border: 1px solid #444; color: white; padding: 15px; margin-bottom: 20px; width: 100%; border-radius: 8px; }
        .btn-book { width: 100%; padding: 15px; background: var(--gold); color: black; font-weight: bold; border: none; border-radius: 50px; cursor: pointer; }
        .admin-wrap { background: var(--white); color: var(--black); padding: 30px; border-radius: 10px; margin-bottom: 30px; }
        .admin-header { border-bottom: 2px solid #eee; margin-bottom: 20px; padding-bottom: 10px; display:flex; justify-content:space-between; align-items:center; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--black); color: var(--gold); }
        .btn-action { padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; color: white; border: none; cursor: pointer; }
        .btn-edit { background: #2980b9; } .btn-delete { background: #c0392b; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 2000; }
        .modal-content { background: var(--white); color: var(--black); margin: 5% auto; padding: 30px; width: 400px; border-radius: 10px; text-align: center; }
        .highlight-item { border: 2px solid var(--gold); box-shadow: 0 0 20px rgba(197, 160, 89, 0.3); }
        .login-box { max-width: 400px; margin: 10vh auto; background: var(--gray); padding: 40px; border-radius: 15px; border: 1px solid #333; text-align: center; position: relative; overflow: hidden; }
        .login-box input { background: var(--dark); border: 1px solid #444; color: white; margin-bottom: 15px; }
        .login-box button { background: var(--gold); color: black; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; margin-top: 10px; border-radius: 5px; }
        .login-box .toggle-link { color: var(--gold); cursor: pointer; font-size: 0.9rem; display: block; margin-top: 15px; text-decoration: underline; }
        .form-section { transition: 0.3s; }
        .hidden { display: none; }
        .profile-wrap { display: flex; gap: 30px; flex-wrap: wrap; }
        .profile-info { flex: 1; background: var(--gray); padding: 30px; border-radius: 10px; border: 1px solid #333; }
        .change-pass-form { flex: 1; background: var(--dark); padding: 30px; border-radius: 10px; border: 1px solid var(--gold); }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header>
        <div class="inner">
            <a href="index.php" class="logo"><i class="fas fa-cut"></i> AL Barber</a>
            <nav>
                <a href="index.php?page=home" class="nav-link <?php echo $page=='home'?'active':''; ?>">Trang chủ</a>
                <a href="index.php?page=services" class="nav-link <?php echo $page=='services'?'active':''; ?>">Dịch vụ</a>
                <a href="index.php?page=products" class="nav-link <?php echo $page=='products'?'active':''; ?>">Sản phẩm</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="index.php?page=history" class="nav-link <?php echo $page=='history'?'active':''; ?>">Lịch sử</a>
                    <a href="index.php?page=profile" class="nav-link <?php echo $page=='profile'?'active':''; ?>" style="color:var(--gold)">Tài khoản</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="index.php?page=admin" class="nav-link" style="color:#e74c3c">Admin</a>
                    <?php endif; ?>
                    <a href="#" onclick="document.getElementById('cartModal').style.display='block'" class="btn-cart">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                        <span class="cart-badge"><?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?></span>
                    </a>
                    <a href="index.php?action=logout" class="nav-link">Thoát</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="nav-link" style="border: 1px solid var(--gold); padding: 5px 15px; border-radius: 20px; color: var(--gold);">Đăng nhập</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- TRANG CHỦ -->
    <?php if ($page == 'home'): ?>
        <div class="hero animate-fade">
            <h1 class="animate-fade delay-1">AL BARBERSHOP</h1>
            <p class="animate-fade delay-2">Khẳng định bản lĩnh - Định hình phong cách</p>
            <a href="index.php?page=services" class="btn-cta animate-fade delay-3">ĐẶT LỊCH NGAY</a>
        </div>
        <div class="container">
            <div class="section-title animate-fade"><h2>Về Chúng Tôi</h2></div>
            <div style="display:flex; gap:40px; flex-wrap:wrap; align-items:center;">
                <div style="flex:1" class="animate-fade delay-1"><img src="https://dongtaybarbershop.com/wp-content/uploads/2023/08/IMG_3943-e1692170675504.jpg" style="width:100%; border-radius:15px; filter:sepia(30%)"></div>
                <div style="flex:1" class="animate-fade delay-2">
                    <h3 style="color:var(--gold); font-size:1.8rem; margin-bottom:15px">Không Gian Quý Ông</h3>
                    <p style="color:#bbb; line-height:1.8">Trải nghiệm dịch vụ cắt tóc thượng hạng trong không gian cổ điển pha lẫn hiện đại.</p>
                </div>
            </div>
        </div>

    <!-- SẢN PHẨM -->
    <?php elseif ($page == 'products'): ?>
        <div class="container animate-fade">
            <div class="section-title"><h2>Sản Phẩm</h2></div>
            <div class="grid">
                <?php $res = $conn->query("SELECT * FROM products"); while($row = $res->fetch_assoc()): $is_hl = ($highlight_id == $row['id']); ?>
                <div id="p-<?php echo $row['id']; ?>" class="card animate-fade <?php echo $is_hl ? 'highlight-item' : ''; ?>">
                    <?php if($is_hl): ?><div style="position:absolute; top:10px; right:10px; background:var(--gold); color:black; padding:5px 10px; font-size:12px; border-radius:20px; z-index:10">MUA LẠI</div><?php endif; ?>
                    <img src="<?php echo $row['image']; ?>" onerror="this.src='https://placehold.co/400'">
                    <div class="card-body">
                        <div class="card-title"><?php echo $row['name']; ?></div>
                        <span class="card-price"><?php echo number_format($row['price']); ?>đ</span>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                            <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
                            <button type="submit" name="add_to_cart" class="btn-block">THÊM VÀO GIỎ</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php if($highlight_id): ?><script>setTimeout(()=>document.getElementById('p-<?php echo $highlight_id; ?>').scrollIntoView({behavior:'smooth', block:'center'}), 500);</script><?php endif; ?>
        </div>

    <!-- DỊCH VỤ (ĐÃ THÊM SĐT VÀ THÔNG BÁO MỚI) -->
    <?php elseif ($page == 'services'): ?>
        <div class="container animate-fade">
            <div class="section-title"><h2>Dịch Vụ & Đặt Lịch</h2></div>
            <div class="services-layout">
                <div class="service-menu animate-fade delay-1">
                    <div class="menu-head"><h3>MENU DỊCH VỤ</h3><p>Bảng giá niêm yết</p></div>
                    <div class="menu-item"><span class="item-name">Cắt tóc Combo</span><div class="item-dots"></div><span class="item-price">80.000đ</span></div>
                    <div class="menu-item"><span class="item-name">Uốn Premlock</span><div class="item-dots"></div><span class="item-price">500.000đ</span></div>
                    <div class="menu-item"><span class="item-name">Nhuộm thời trang</span><div class="item-dots"></div><span class="item-price">350.000đ</span></div>
                </div>
                <div class="booking-box animate-fade delay-2">
                    <h3 class="booking-title">ĐẶT LỊCH GIỮ CHỖ</h3>
                    <form method="POST">
                        <div style="display:flex; gap:15px;">
                            <input type="date" name="date" required class="booking-input" min="<?php echo date('Y-m-d'); ?>">
                            <input type="time" name="time" required class="booking-input">
                        </div>
                        <!-- Ô NHẬP SỐ ĐIỆN THOẠI -->
                        <div style="margin-bottom:15px;">
                            <input type="text" name="phone" placeholder="Số điện thoại liên hệ" required class="booking-input" pattern="[0-9]{10,11}">
                        </div>
                        <div>
                            <select name="stylist" class="booking-input">
                                <option value="Ngẫu nhiên">Chọn Stylist ngẫu nhiên</option>
                                <?php $s = $conn->query("SELECT * FROM stylists"); while($r=$s->fetch_assoc()){ echo "<option value='".$r['name']."'>".$r['name']."</option>"; } ?>
                            </select>
                        </div>
                        <button type="submit" name="book_now" class="btn-book">XÁC NHẬN ĐẶT LỊCH</button>
                    </form>
                </div>
            </div>
        </div>

    <!-- PROFILE -->
    <?php elseif ($page == 'profile' && isset($_SESSION['user'])): ?>
        <div class="container animate-fade">
            <div class="section-title"><h2>Tài Khoản Của Tôi</h2></div>
            <div class="profile-wrap">
                <div class="profile-info">
                    <h3 style="color:var(--gold); margin-bottom:20px"><i class="fas fa-user"></i> Thông Tin</h3>
                    <p style="margin-bottom:10px">Họ và tên: <b style="color:white"><?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Chưa cập nhật'; ?></b></p>
                    <p>Tên đăng nhập: <b style="color:white"><?php echo $_SESSION['user']; ?></b></p>
                    <p style="margin-top:20px; font-size:0.9rem; color:#888">Thành viên từ: 2024</p>
                </div>
                <div class="change-pass-form">
                    <h3 style="color:var(--gold); margin-bottom:20px"><i class="fas fa-key"></i> Đổi Mật Khẩu</h3>
                    <form method="POST">
                        <div class="form-group" style="margin-bottom:15px">
                            <label style="display:block; margin-bottom:5px; color:#ccc">Mật khẩu cũ:</label>
                            <input type="password" name="old_pass" required style="width:100%; padding:10px; border-radius:5px; background:#333; border:1px solid #555; color:white">
                        </div>
                        <div class="form-group" style="margin-bottom:15px">
                            <label style="display:block; margin-bottom:5px; color:#ccc">Mật khẩu mới:</label>
                            <input type="password" name="new_pass" required style="width:100%; padding:10px; border-radius:5px; background:#333; border:1px solid #555; color:white">
                        </div>
                        <div class="form-group" style="margin-bottom:20px">
                            <label style="display:block; margin-bottom:5px; color:#ccc">Nhập lại mật khẩu mới:</label>
                            <input type="password" name="confirm_pass" required style="width:100%; padding:10px; border-radius:5px; background:#333; border:1px solid #555; color:white">
                        </div>
                        <button type="submit" name="change_password" style="width:100%; padding:12px; background:var(--gold); color:black; font-weight:bold; border:none; border-radius:5px; cursor:pointer">XÁC NHẬN ĐỔI</button>
                    </form>
                </div>
            </div>
        </div>

    <!-- LỊCH SỬ -->
    <?php elseif ($page == 'history' && isset($_SESSION['user'])): ?>
        <div class="container animate-fade">
            <div class="section-title"><h2>Lịch Sử</h2></div>
            <div style="background:var(--gray); padding:20px; border-radius:10px; margin-bottom:30px;">
                <h3 style="color:var(--gold); margin-bottom:15px">Đặt Lịch</h3>
                <?php 
                $customer_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['user'];
                $res=$conn->query("SELECT * FROM bookings WHERE customer_name='$customer_name' ORDER BY created_at DESC");
                while($row=$res->fetch_assoc()): ?>
                    <div style="border-bottom:1px solid #444; padding:10px 0; display:flex; justify-content:space-between;">
                        <span><?php echo date("d/m", strtotime($row['book_date']))." - ".$row['book_time']; ?> | Stylist: <?php echo $row['stylist']; ?></span>
                        <?php 
                        if ($row['status']=='confirmed') { echo '<span style="color:#2ecc71; font-weight:bold">Đã xác nhận</span>'; } 
                        elseif ($row['status']=='rejected') { echo '<div style="text-align:right"><span style="color:#e74c3c; font-weight:bold">Bị từ chối</span>'.(!empty($row['reject_reason'])?'<br><small style="color:#aaa">Lý do: '.$row['reject_reason'].'</small>':'').'</div>'; } 
                        else { echo '<span style="color:#f1c40f">Đang chờ</span>'; }
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <div style="background:var(--gray); padding:20px; border-radius:10px;">
                <h3 style="color:var(--gold); margin-bottom:15px">Mua Hàng</h3>
                <?php $res=$conn->query("SELECT * FROM orders WHERE username='{$_SESSION['user']}' ORDER BY created_at DESC");
                while($row=$res->fetch_assoc()): $items=json_decode($row['items'],true); ?>
                    <div style="border-bottom:1px solid #444; padding:15px 0;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px"><span style="color:#888; font-size:0.9rem"><?php echo date("d/m/Y H:i", strtotime($row['created_at'])); ?></span><b style="color:var(--gold)"><?php echo number_format($row['total_price']); ?>đ</b></div>
                        <div><?php if(is_array($items)): foreach($items as $i): $qty_label = isset($i['quantity'])&&$i['quantity']>1 ? " (x".$i['quantity'].")" : ""; ?><span style="background:var(--black); padding:5px 10px; border-radius:15px; font-size:0.85rem; margin-right:5px; display:inline-block; margin-top:5px;"><?php echo $i['name'].$qty_label; ?> <?php if(isset($i['id'])) echo "<a href='index.php?page=products&highlight=".$i['id']."' style='color:var(--gold); margin-left:5px'><i class='fas fa-redo'></i></a>"; ?></span><?php endforeach; endif; ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    <!-- ADMIN PANEL -->
    <?php elseif ($page == 'admin' && isset($_SESSION['role']) && $_SESSION['role']=='admin'): ?>
        <div class="container animate-fade">
            <div class="section-title"><h2>Quản Trị Viên</h2></div>
            <!-- (Sản phẩm và Thợ giữ nguyên) -->
            <div class="admin-wrap">
                <div class="admin-header"><h3>Quản lý Sản phẩm</h3></div>
                <?php $p_edit = ['id'=>'', 'name'=>'', 'price'=>'', 'image'=>''];
                if(isset($_GET['edit_p'])) { $id=$_GET['edit_p']; $p_edit=$conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc(); } ?>
                <form method="POST">
                    <input type="hidden" name="p_id" value="<?php echo $p_edit['id']; ?>">
                    <div class="form-row"><input type="text" name="p_name" placeholder="Tên SP" value="<?php echo $p_edit['name']; ?>" required><input type="number" name="p_price" placeholder="Giá" value="<?php echo $p_edit['price']; ?>" required></div>
                    <div class="form-row"><input type="text" name="p_image" placeholder="Link ảnh" value="<?php echo $p_edit['image']; ?>"><button type="submit" name="save_product" class="btn-action" style="background:var(--black); width:150px">LƯU</button></div>
                </form>
                <table>
                    <tr><th>Tên</th><th>Giá</th><th>Action</th></tr>
                    <?php $res=$conn->query("SELECT * FROM products"); while($r=$res->fetch_assoc()): ?>
                    <tr><td><?php echo $r['name']; ?></td><td><?php echo number_format($r['price']); ?></td><td><a href="index.php?page=admin&edit_p=<?php echo $r['id']; ?>" class="btn-action btn-edit">Sửa</a> <a href="index.php?page=admin&delete_product=<?php echo $r['id']; ?>" onclick="return confirm('Xóa?')" class="btn-action btn-delete">Xóa</a></td></tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div class="admin-wrap">
                <div class="admin-header"><h3>Quản lý Thợ</h3></div>
                <?php $s_edit = ['id'=>'', 'name'=>'', 'experience'=>'', 'avatar'=>''];
                if(isset($_GET['edit_s'])) { $id=$_GET['edit_s']; $s_edit=$conn->query("SELECT * FROM stylists WHERE id=$id")->fetch_assoc(); } ?>
                <form method="POST">
                    <input type="hidden" name="s_id" value="<?php echo $s_edit['id']; ?>">
                    <div class="form-row"><input type="text" name="s_name" placeholder="Tên Thợ" value="<?php echo $s_edit['name']; ?>" required><input type="text" name="s_exp" placeholder="Kinh nghiệm" value="<?php echo $s_edit['experience']; ?>" required></div>
                    <div class="form-row"><input type="text" name="s_avatar" placeholder="Avatar URL" value="<?php echo $s_edit['avatar']; ?>"><button type="submit" name="save_stylist" class="btn-action" style="background:var(--black); width:150px">LƯU</button></div>
                </form>
                <table>
                    <tr><th>Tên</th><th>Kinh nghiệm</th><th>Action</th></tr>
                    <?php $res=$conn->query("SELECT * FROM stylists"); while($r=$res->fetch_assoc()): ?>
                    <tr><td><?php echo $r['name']; ?></td><td><?php echo $r['experience']; ?></td><td><a href="index.php?page=admin&edit_s=<?php echo $r['id']; ?>" class="btn-action btn-edit">Sửa</a> <a href="index.php?page=admin&delete_stylist=<?php echo $r['id']; ?>" onclick="return confirm('Xóa?')" class="btn-action btn-delete">Xóa</a></td></tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <!-- DUYỆT BOOKING (CÓ THÊM CỘT SĐT) -->
            <div class="admin-wrap">
                <div class="admin-header"><h3>Duyệt Đặt Lịch</h3></div>
                <table>
                    <tr><th>Khách</th><th>SĐT</th><th>Thời gian</th><th>Stylist</th><th>Hành động</th></tr>
                    <?php
                    $res = $conn->query("SELECT * FROM bookings WHERE status='pending' ORDER BY created_at DESC");
                    if($res->num_rows == 0) echo "<tr><td colspan='5' style='text-align:center; color:#999'>Không có đơn mới</td></tr>";
                    while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><a href="tel:<?php echo $row['phone']; ?>" style="color:var(--gold)"><?php echo $row['phone']; ?></a></td>
                        <td><?php echo date("d/m", strtotime($row['book_date']))." - ".$row['book_time']; ?></td>
                        <td><?php echo $row['stylist']; ?></td>
                        <td>
                            <a href="index.php?page=admin&confirm_booking=<?php echo $row['id']; ?>" class="btn-action btn-edit" style="background:#27ae60; margin-right:5px">Duyệt ✓</a>
                            <a href="#" onclick="rejectBooking(<?php echo $row['id']; ?>)" class="btn-action btn-delete">Từ chối ✕</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>

    <!-- LOGIN / REGISTER -->
    <?php elseif ($page == 'login'): ?>
        <div class="login-box animate-fade">
            <div id="loginForm" class="form-section">
                <h2 style="color:var(--gold); margin-bottom:20px">ĐĂNG NHẬP</h2>
                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">VÀO HỆ THỐNG</button>
                </form>
                <span class="toggle-link" onclick="toggleForms()">Chưa có tài khoản? Đăng ký ngay</span>
            </div>
            <div id="registerForm" class="form-section hidden">
                <h2 style="color:var(--gold); margin-bottom:20px">ĐĂNG KÝ MỚI</h2>
                <form method="POST">
                    <input type="text" name="reg_username" placeholder="Tên đăng nhập" required>
                    <input type="text" name="reg_fullname" placeholder="Họ và tên (VD: Nguyễn Văn A)" required>
                    <input type="email" name="reg_email" placeholder="Email" required>
                    <input type="password" name="reg_password" placeholder="Mật khẩu" required>
                    <button type="submit" name="register">TẠO TÀI KHOẢN</button>
                </form>
                <span class="toggle-link" onclick="toggleForms()">Đã có tài khoản? Đăng nhập</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- CART MODAL -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span onclick="document.getElementById('cartModal').style.display='none'" style="float:right; cursor:pointer; font-size:24px">&times;</span>
            <h2 style="color:var(--black); margin-bottom:20px">GIỎ HÀNG</h2>
            <div style="max-height:300px; overflow-y:auto; margin-bottom:20px; text-align:left; background:#f9f9f9; padding:10px; border-radius:5px;">
                <?php 
                $total=0;
                if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach($_SESSION['cart'] as $index => $c) {
                        $qty = isset($c['quantity']) ? $c['quantity'] : 1;
                        $line_total = $c['price'] * $qty;
                        $total += $line_total;
                        echo "<div style='display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding:10px 0'>";
                        echo "<div><div style='font-weight:bold'>{$c['name']} <span style='color:#c0392b; font-size:0.9em'>(x{$qty})</span></div><div style='font-size:0.85em; color:#666'>".number_format($c['price'])."đ/món</div></div>";
                        echo "<div style='display:flex; align-items:center; gap:10px'><b>".number_format($line_total)."đ</b><a href='index.php?remove_item={$index}&page={$page}' onclick=\"return confirm('Xóa?')\" style='color:red'><i class='fas fa-trash'></i></a></div>";
                        echo "</div>";
                    }
                    echo "<hr><div style='text-align:right; margin-top:10px; font-size:1.1rem; font-weight:bold'>Tổng: <span style='color:#c0392b'>".number_format($total)."đ</span></div>";
                    echo "<div id='qr' style='display:none; text-align:center; margin-top:10px'><img src='https://img.vietqr.io/image/MB-9090010199999-compact.png' width='150'><p style='font-size:12px'>Nội dung: Tên + SĐT</p></div>";
                    echo "<button onclick=\"document.getElementById('qr').style.display='block'; this.style.display='none'; document.getElementById('cf').style.display='block'\" style='width:100%; background:green; color:white; padding:10px; border:none; margin-top:10px; border-radius:5px; cursor:pointer'>THANH TOÁN QR</button>";
                    echo "<a href='index.php?checkout=1' id='cf' style='display:none; width:100%; background:var(--black); color:var(--gold); padding:10px; text-align:center; margin-top:10px; border-radius:5px; font-weight:bold'>ĐÃ CHUYỂN KHOẢN</a>";
                } else { echo "<p style='text-align:center; color:#888'>Giỏ hàng đang trống.</p>"; }
                ?>
            </div>
        </div>
    </div>

    <script>
        function rejectBooking(id) {
            let reason = prompt("Nhập lý do từ chối (VD: Stylist bận):");
            if (reason) {
                window.location.href = `index.php?page=admin&reject_booking=${id}&reason=${encodeURIComponent(reason)}`;
            }
        }
        function toggleForms() {
            const login = document.getElementById('loginForm');
            const register = document.getElementById('registerForm');
            if (login.classList.contains('hidden')) { login.classList.remove('hidden'); register.classList.add('hidden'); } 
            else { login.classList.add('hidden'); register.classList.remove('hidden'); }
        }
    </script>

</body>
</html>