<div class="container animate-fade">
    <div class="section-title"><h2>Dịch Vụ & Đặt Lịch</h2></div>
    
    <div class="services-layout">
        <!-- MENU DỊCH VỤ -->
        <div class="service-menu animate-fade delay-1">
            <div class="menu-head">
                <h3>MENU DỊCH VỤ</h3>
                <p>Bảng giá niêm yết</p>
            </div>
            
            <div class="menu-item"><span class="item-name">Cắt tóc Combo 7 bước</span><div class="item-dots"></div><span class="item-price">80.000đ</span></div>
            <div class="menu-item"><span class="item-name">Cạo mặt & Ráy tai</span><div class="item-dots"></div><span class="item-price">50.000đ</span></div>
            <div class="menu-item"><span class="item-name">Uốn Premlock / Fly</span><div class="item-dots"></div><span class="item-price">500.000đ</span></div>
            <div class="menu-item"><span class="item-name">Nhuộm thời trang</span><div class="item-dots"></div><span class="item-price">350.000đ</span></div>
            <div class="menu-item"><span class="item-name">Tẩy tóc (theo lần)</span><div class="item-dots"></div><span class="item-price">200.000đ</span></div>
            <div class="menu-item"><span class="item-name">Phục hồi tóc hư tổn</span><div class="item-dots"></div><span class="item-price">150.000đ</span></div>
        </div>

        <!-- FORM ĐẶT LỊCH -->
        <div class="booking-box animate-fade delay-2">
            <h3 class="booking-title">ĐẶT LỊCH GIỮ CHỖ</h3>
            <form method="POST">
                <!-- Chọn Ngày & Giờ (Chia cột đều) -->
                <div style="display:flex; gap:15px; margin-bottom: 20px;">
                    <div style="flex:1;">
                        <label style="color:#bbb; font-size:0.9rem; margin-bottom:5px; display:block;">Ngày cắt:</label>
                        <input type="date" name="date" required class="booking-input" min="<?php echo date('Y-m-d'); ?>" style="margin-bottom:0; width:100%;">
                    </div>
                    <div style="flex:1;">
                        <label style="color:#bbb; font-size:0.9rem; margin-bottom:5px; display:block;">Giờ đến:</label>
                        <input type="time" name="time" required class="booking-input" style="margin-bottom:0; width:100%;">
                    </div>
                </div>

                <!-- Nhập Số Điện Thoại -->
                <div style="margin-bottom: 20px;">
                    <label style="color:#bbb; font-size:0.9rem; margin-bottom:5px; display:block;">Số điện thoại:</label>
                    <input type="text" name="phone" placeholder="Nhập SĐT liên hệ (VD: 0912...)" required class="booking-input" pattern="[0-9]{10,11}" title="Vui lòng nhập đúng số điện thoại 10-11 số" style="margin-bottom:0;">
                </div>

                <!-- Chọn Stylist -->
                <div style="margin-bottom: 30px;">
                    <label style="color:#bbb; font-size:0.9rem; margin-bottom:5px; display:block;">Chọn Stylist:</label>
                    <select name="stylist" class="booking-input" style="margin-bottom:0; cursor:pointer;">
                        <option value="Ngẫu nhiên">-- Chọn Stylist (Ngẫu nhiên) --</option>
                        <?php 
                        // Lấy danh sách thợ từ DB bằng PDO
                        $stmt = $conn->prepare("SELECT * FROM stylists");
                        $stmt->execute();
                        while($r = $stmt->fetch()): 
                        ?>
                            <!-- Thêm color:black để chữ hiển thị rõ trên nền trắng mặc định của trình duyệt -->
                            <option value="<?php echo $r['name']; ?>" style="color:black;">
                                Stylist: <?php echo $r['name']; ?> (<?php echo $r['experience']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" name="book_now" class="btn-book">
                    <i class="far fa-calendar-check"></i> XÁC NHẬN ĐẶT LỊCH
                </button>
            </form>
        </div>
    </div>
</div>