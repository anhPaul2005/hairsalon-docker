<?php if (isset($_SESSION['user'])): ?>
    <div class="container animate-fade">
        <div class="section-title"><h2>Tài Khoản Của Tôi</h2></div>
        
        <div class="profile-wrap">
            <!-- Cột 1: Thông tin -->
            <div class="profile-info">
                <h3 style="color:var(--gold); margin-bottom:20px; border-bottom:1px solid #444; padding-bottom:10px;">
                    <i class="fas fa-id-card"></i> Thông Tin Cá Nhân
                </h3>
                
                <div style="margin-bottom:15px; font-size:1.1rem;">
                    <span style="color:#888;">Họ và tên:</span><br>
                    <b style="color:white; font-size:1.3rem;">
                        <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Chưa cập nhật'; ?>
                    </b>
                </div>
                
                <div style="margin-bottom:15px;">
                    <span style="color:#888;">Tên đăng nhập:</span><br>
                    <b style="color:var(--gold);"><?php echo $_SESSION['user']; ?></b>
                </div>

                <div style="margin-top:30px; font-size:0.9rem; color:#666; font-style:italic;">
                    Thành viên thân thiết của AL BarberShop
                </div>
            </div>

            <!-- Cột 2: Đổi mật khẩu -->
            <div class="change-pass-form">
                <h3 style="color:var(--gold); margin-bottom:20px; border-bottom:1px solid #444; padding-bottom:10px;">
                    <i class="fas fa-key"></i> Đổi Mật Khẩu
                </h3>
                
                <form method="POST">
                    <div class="form-group" style="margin-bottom:15px">
                        <label style="display:block; margin-bottom:5px; color:#ccc">Mật khẩu hiện tại:</label>
                        <input type="password" name="old_pass" required style="width:100%; padding:12px; border-radius:5px; background:#222; border:1px solid #444; color:white; outline:none;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:15px">
                        <label style="display:block; margin-bottom:5px; color:#ccc">Mật khẩu mới:</label>
                        <input type="password" name="new_pass" required style="width:100%; padding:12px; border-radius:5px; background:#222; border:1px solid #444; color:white; outline:none;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:25px">
                        <label style="display:block; margin-bottom:5px; color:#ccc">Nhập lại mật khẩu mới:</label>
                        <input type="password" name="confirm_pass" required style="width:100%; padding:12px; border-radius:5px; background:#222; border:1px solid #444; color:white; outline:none;">
                    </div>
                    
                    <button type="submit" name="change_password" class="btn-book" style="font-size:1rem; padding:10px;">
                        CẬP NHẬT MẬT KHẨU
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Nếu chưa đăng nhập thì chuyển hướng -->
    <script>window.location.href = 'index.php?page=login';</script>
<?php endif; ?>