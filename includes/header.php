<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AL BarberShop | Luxury & Style</title>
    <!-- FontAwesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* --- 1. GLOBAL VARIABLES & RESET --- */
        :root { 
            --gold: #c5a059; 
            --gold-light: #e6c888; 
            --black: #111111; 
            --dark: #1a1a1a; 
            --gray: #2d2d2d; 
            --text: #e0e0e0; 
            --white: #ffffff; 
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--black); color: var(--text); overflow-x: hidden; }
        a { text-decoration: none; color: inherit; transition: 0.3s ease; }
        ul { list-style: none; }

        /* --- 2. HEADER STYLES --- */
        header { 
            background: rgba(17, 17, 17, 0.98); 
            position: sticky; top: 0; z-index: 1000; 
            border-bottom: 1px solid #333; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        .inner { 
            max-width: 1200px; margin: 0 auto; 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 15px 20px; 
            position: relative;
        }
        
        .logo { 
            font-family: 'Playfair Display', serif; 
            font-size: 24px; color: var(--gold); 
            letter-spacing: 2px; font-weight: bold;
            display: flex; align-items: center; gap: 10px;
        }
        .logo i { color: var(--white); }

        /* --- 3. NAVIGATION --- */
        .nav-menu { display: flex; align-items: center; gap: 25px; }
        
        .nav-link { 
            color: #ccc; font-weight: 500; font-size: 0.95rem; position: relative; padding: 5px 0;
        }
        .nav-link::after { 
            content: ''; position: absolute; width: 0; height: 2px; 
            bottom: 0; left: 0; background-color: var(--gold); transition: 0.3s; 
        }
        .nav-link:hover, .nav-link.active { color: var(--gold); }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        .btn-cart { position: relative; color: var(--white); font-size: 1.1rem; }
        .cart-badge { 
            position: absolute; top: -8px; right: -8px; 
            background: var(--gold); color: var(--black); 
            font-size: 10px; font-weight: bold; padding: 2px 5px; border-radius: 50%; 
        }

        .hamburger { display: none; font-size: 24px; color: var(--gold); cursor: pointer; }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hamburger { display: block; }
            .nav-menu {
                display: none; position: absolute;
                top: 100%; left: 0; width: 100%;
                background-color: var(--dark);
                flex-direction: column; align-items: center;
                padding: 20px 0; border-bottom: 2px solid var(--gold);
                box-shadow: 0 10px 10px rgba(0,0,0,0.5);
            }
            .nav-menu.active { display: flex; animation: slideDown 0.3s ease forwards; }
            .nav-link { width: 100%; text-align: center; padding: 15px 0; border-bottom: 1px solid #333; }
            .btn-cart { margin-top: 15px; }
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- 4. SERVICES & BOOKING CSS (PHẦN BẠN ĐANG THIẾU) --- */
        .services-layout { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); 
            gap: 50px; 
            align-items: start; 
        }
        
        /* Menu Bảng Giá */
        .service-menu { 
            background: #222; padding: 40px; border-radius: 20px; 
            border: 1px solid #333; box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
        }
        .menu-head { text-align: center; margin-bottom: 30px; }
        .menu-head h3 { font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--gold); margin-bottom: 5px; }
        .menu-head p { color: #888; font-style: italic; }
        
        .menu-item { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 20px; }
        .item-name { flex: 1; font-weight: 500; color: #eee; }
        .item-dots { flex: 1; border-bottom: 2px dotted #444; margin: 0 10px; }
        .item-price { font-weight: bold; color: var(--gold); }

        /* Form Đặt Lịch */
        .booking-box { 
            background: linear-gradient(145deg, #1a1a1a, #252525); 
            padding: 30px; border-radius: 20px; 
            border: 2px solid var(--gold); 
            box-shadow: 0 0 30px rgba(197, 160, 89, 0.15); 
        }
        .booking-title { 
            text-align: center; margin-bottom: 25px; 
            font-family: 'Playfair Display', serif; color: var(--white); font-size: 1.8rem; 
        }
        
        /* Input & Select */
        .booking-input { 
            background: rgba(0,0,0,0.3); 
            border: 1px solid #444; 
            color: white; 
            padding: 12px 15px; 
            width: 100%; 
            border-radius: 8px; 
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }
        .booking-input:focus { border-color: var(--gold); background: rgba(0,0,0,0.5); }
        
        /* Nút Đặt Lịch */
        .btn-book { 
            width: 100%; padding: 15px; 
            background: var(--gold); color: black; 
            font-weight: bold; border: none; 
            border-radius: 50px; font-size: 1.1rem; 
            cursor: pointer; transition: 0.3s; 
            text-transform: uppercase; letter-spacing: 1px; margin-top: 10px;
        }
        .btn-book:hover { background: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(255,255,255,0.2); }

        /* --- 5. ADMIN & TABLES --- */
        .admin-wrap { background: var(--white); color: var(--black); padding: 25px; border-radius: 10px; margin-bottom: 30px; overflow-x: auto; }
        .admin-header { border-bottom: 2px solid #eee; margin-bottom: 20px; padding-bottom: 10px; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap; }
        .form-row input, .form-row select { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-width: 200px; }
        
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--black); color: var(--gold); }
        .btn-action { padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; color: white; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-edit { background: #2980b9; } 
        .btn-delete { background: #c0392b; }

        /* --- 6. COMMON UI --- */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        .section-title { text-align: center; margin-bottom: 40px; }
        .section-title h2 { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--gold); border-bottom: 2px solid var(--gray); padding-bottom: 10px; display: inline-block; }
        
        .hero { height: 60vh; background: linear-gradient(to bottom, rgba(0,0,0,0.6), var(--black)), url('https://images.unsplash.com/photo-1621605815971-fbc98d6d4e59?q=80&w=2070'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: 4rem; color: var(--gold); text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .btn-cta { padding: 12px 35px; border-radius: 50px; background: var(--gold); color: black; font-weight: bold; display: inline-block; margin-top: 20px; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background: var(--gray); border-radius: 15px; overflow: hidden; border: 1px solid #333; transition: 0.4s; position: relative; }
        .card:hover { transform: translateY(-5px); border-color: var(--gold); }
        .card img { width: 100%; height: 260px; object-fit: cover; }
        .card-body { padding: 20px; text-align: center; }
        .card-title { font-size: 1.2rem; font-weight: 600; color: var(--white); margin-bottom: 10px; }
        .card-price { color: var(--gold); font-size: 1.3rem; font-weight: bold; margin-bottom: 15px; display: block; }
        .btn-block { width: 100%; padding: 10px; background: transparent; border: 1px solid var(--gold); color: var(--gold); font-weight: 600; cursor: pointer; border-radius: 5px; transition:0.3s; }
        .btn-block:hover { background: var(--gold); color: black; }

        /* Modal & Login */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 2000; }
        .modal-content { background: var(--white); color: var(--black); margin: 5% auto; padding: 20px; width: 95%; max-width: 500px; border-radius: 10px; text-align: center; position:relative; }
        .login-box { max-width: 400px; margin: 10vh auto; background: var(--gray); padding: 30px; border-radius: 15px; border: 1px solid #333; text-align: center; }
        .login-box input { background: var(--dark); border: 1px solid #444; color: white; margin-bottom: 15px; padding:12px; width:100%; border-radius:5px; }
        .login-box button { background: var(--gold); color: black; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; border-radius:5px; }
        .toggle-link { color: var(--gold); cursor: pointer; font-size: 0.9rem; margin-top: 15px; display: block; text-decoration: underline; }
        .hidden { display: none; }
        
        .profile-wrap { display: flex; gap: 30px; flex-wrap: wrap; }
        .profile-info { flex: 1; background: var(--gray); padding: 30px; border-radius: 10px; border: 1px solid #333; min-width: 300px; }
        .change-pass-form { flex: 1; background: var(--dark); padding: 30px; border-radius: 10px; border: 1px solid var(--gold); min-width: 300px; }

        .animate-fade { animation: fadeIn 1s ease forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <header>
        <div class="inner">
            <a href="index.php" class="logo"><i class="fas fa-cut"></i> AL Barber</a>
            
            <div class="hamburger" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>

            <nav class="nav-menu" id="nav-menu">
                <a href="index.php?page=home" class="nav-link <?php echo $page=='home'?'active':''; ?>">Trang chủ</a>
                <a href="index.php?page=services" class="nav-link <?php echo $page=='services'?'active':''; ?>">Dịch vụ</a>
                <a href="index.php?page=products" class="nav-link <?php echo $page=='products'?'active':''; ?>">Sản phẩm</a>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="index.php?page=history" class="nav-link <?php echo $page=='history'?'active':''; ?>">Lịch sử</a>
                    <a href="index.php?page=profile" class="nav-link <?php echo $page=='profile'?'active':''; ?>" style="color:var(--gold)">Tài khoản</a>
                    
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="index.php?page=admin" class="nav-link" style="color:#e74c3c">Admin</a>
                    <?php endif; ?>
                    
                    <a href="#" onclick="document.getElementById('cartModal').style.display='block'; toggleMenu()" class="btn-cart">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-badge">
                            <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?>
                        </span>
                    </a>
                    
                    <a href="index.php?action=logout" class="nav-link">Thoát</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="nav-link" style="border: 1px solid var(--gold); padding: 5px 20px; border-radius: 20px; color: var(--gold);">Đăng nhập</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("nav-menu");
            menu.classList.toggle("active");
        }
    </script>