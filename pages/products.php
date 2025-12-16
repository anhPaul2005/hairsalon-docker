<div class="container animate-fade">
    <div class="section-title"><h2>Sản Phẩm Grooming</h2></div>

    <!-- THANH TÌM KIẾM SẢN PHẨM -->
    <div style="margin-bottom: 40px; text-align: center;">
        <form method="GET" action="index.php" style="display: inline-block; position: relative; width: 100%; max-width: 500px;">
            <input type="hidden" name="page" value="products">
            
            <input type="text" name="search" placeholder="Tìm kiếm sáp, gôm, dầu gội..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                   style="width: 100%; padding: 15px 50px 15px 25px; border-radius: 50px; border: 2px solid var(--gold); background: rgba(255,255,255,0.05); color: white; font-size: 1rem; outline: none; transition: 0.3s;">
            
            <button type="submit" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gold); font-size: 1.2rem; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>
        </form>
        
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <div style="margin-top: 10px;">
                <span style="color: #bbb;">Kết quả cho: "<?php echo htmlspecialchars($_GET['search']); ?>"</span>
                <a href="index.php?page=products" style="color: var(--gold); margin-left: 10px; font-size: 0.9rem;">(Xóa lọc)</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- DANH SÁCH SẢN PHẨM -->
    <div class="grid">
        <?php
        // 1. Xử lý biến Highlight (Sản phẩm mua lại)
        $hl_id = null;
        if (isset($_GET['highlight'])) {
            $hl_id = $_GET['highlight'];
        } elseif (isset($highlight_id)) {
            $hl_id = $highlight_id;
        }

        // 2. Xây dựng câu truy vấn (PDO)
        $sql = "SELECT * FROM products";
        $params = [];

        // Nếu có từ khóa tìm kiếm
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $sql .= " WHERE name LIKE ?";
            $params[] = "%" . $_GET['search'] . "%";
        }

        // Thực thi truy vấn
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        // 3. Hiển thị dữ liệu
        if ($stmt->rowCount() > 0) {
            while($row = $stmt->fetch()): 
                // Kiểm tra xem sản phẩm này có cần làm nổi bật không
                $is_hl = ($hl_id == $row['id']);
            ?>
            
            <div id="p-<?php echo $row['id']; ?>" class="card animate-fade <?php echo $is_hl ? 'highlight-item' : ''; ?>">
                
                <!-- Nhãn Mua Lại -->
                <?php if($is_hl): ?>
                    <div style="position:absolute; top:10px; right:10px; background:var(--gold); color:black; padding:5px 10px; font-size:12px; font-weight:bold; border-radius:20px; z-index:10; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                        <i class="fas fa-star"></i> MUA LẠI
                    </div>
                <?php endif; ?>
                
                <img src="<?php echo $row['image']; ?>" onerror="this.src='https://placehold.co/400?text=No+Image'" alt="<?php echo $row['name']; ?>">
                
                <div class="card-body">
                    <div class="card-title"><?php echo $row['name']; ?></div>
                    <span class="card-price"><?php echo number_format($row['price']); ?>đ</span>
                    
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
                        
                        <!-- Ô chọn số lượng -->
                        <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <label style="font-size: 0.9rem; color: #ccc;">Số lượng:</label>
                            <input type="number" name="quantity" value="1" min="1" max="50"
                                   style="width: 60px; padding: 5px; border: 1px solid var(--gold); background: #222; color: var(--gold); border-radius: 5px; text-align: center; font-weight: bold; outline: none;">
                        </div>
                        
                        <button type="submit" name="add_to_cart" class="btn-block">
                            <i class="fas fa-cart-plus"></i> THÊM VÀO GIỎ
                        </button>
                    </form>
                </div>
            </div>
            
            <?php endwhile; 
        } else {
            echo "<div style='grid-column: 1/-1; text-align: center; padding: 50px;'>
                    <i class='fas fa-box-open' style='font-size: 3rem; color: #444; margin-bottom: 15px;'></i>
                    <p style='color:#888; font-size: 1.1rem;'>Không tìm thấy sản phẩm nào phù hợp.</p>
                  </div>";
        }
        ?>
    </div>
    
    <!-- Script cuộn đến sản phẩm mua lại (Không Zoom) -->
    <?php if($hl_id): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                setTimeout(() => {
                    let element = document.getElementById('p-<?php echo $hl_id; ?>');
                    if(element) {
                        element.scrollIntoView({behavior: 'smooth', block: 'center'});
                    }
                }, 500);
            });
        </script>
    <?php endif; ?>

</div> 
<!-- ĐÓNG CONTAINER TẠI ĐÂY ĐỂ TÁCH BIỆT NÚT GIỎ HÀNG RA KHỎI HIỆU ỨNG ANIMATE -->

<!-- [MỚI] NÚT GIỎ HÀNG TRÔI NỔI (ĐẶT NGOÀI CONTAINER ĐỂ FIX POSITION HOẠT ĐỘNG ĐÚNG) -->
<div class="floating-cart" onclick="openCartWithMap()" title="Xem giỏ hàng">
    <i class="fas fa-shopping-cart"></i>
    <span class="float-badge">
        <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?>
    </span>
</div>

<!-- CSS CHO NÚT GIỎ HÀNG -->
<style>
    .floating-cart {
        position: fixed; /* Cố định theo màn hình */
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: var(--gold);
        color: var(--black);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);
        cursor: pointer;
        z-index: 9999; /* Đảm bảo nằm trên cùng */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: floatUp 0.5s ease-out;
    }
    .floating-cart:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(197, 160, 89, 0.6);
    }
    .float-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #c0392b; /* Màu đỏ nổi bật */
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    @keyframes floatUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>