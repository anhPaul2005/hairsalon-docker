<div class="container animate-fade">
    <div class="section-title"><h2>Lịch Sử Của Bạn</h2></div>

    <!-- 1. LỊCH SỬ ĐẶT LỊCH CẮT TÓC -->
    <div style="background:var(--gray); padding:20px; border-radius:10px; margin-bottom:30px; border:1px solid #333;">
        <h3 style="color:var(--gold); margin-bottom:15px; border-bottom:1px dashed #555; padding-bottom:10px;">
            <i class="far fa-calendar-check"></i> Lịch Đặt Cắt Tóc
        </h3>
        
        <?php 
        // Lấy tên khách hàng từ session (ưu tiên Fullname)
        $customer_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['user'];
        
        // Truy vấn bảng bookings (PDO)
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE customer_name = ? ORDER BY created_at DESC");
        $stmt->execute([$customer_name]);
        
        if ($stmt->rowCount() == 0) {
            echo "<p style='color:#888; font-style:italic;'>Bạn chưa có lịch đặt nào.</p>";
        } else {
            while($row = $stmt->fetch()): 
        ?>
            <div style="border-bottom:1px solid #444; padding:15px 0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <!-- Thông tin lịch -->
                <div style="flex:1;">
                    <div style="font-size:1.1rem; color:white; font-weight:bold;">
                        <?php echo date("d/m/Y", strtotime($row['book_date'])); ?> 
                        <span style="color:var(--gold)"> - <?php echo date("H:i", strtotime($row['book_time'])); ?></span>
                    </div>
                    <div style="color:#aaa; font-size:0.9rem; margin-top:5px;">
                        Stylist: <span style="color:white"><?php echo $row['stylist']; ?></span>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div style="text-align:right;">
                    <?php 
                    if ($row['status'] == 'confirmed') {
                        echo '<span style="background:rgba(39, 174, 96, 0.2); color:#2ecc71; padding:5px 10px; border-radius:15px; font-weight:bold; font-size:0.9rem;">Đã xác nhận <i class="fas fa-check"></i></span>';
                    } elseif ($row['status'] == 'rejected') {
                        echo '<span style="background:rgba(231, 76, 60, 0.2); color:#e74c3c; padding:5px 10px; border-radius:15px; font-weight:bold; font-size:0.9rem;">Bị từ chối <i class="fas fa-times"></i></span>';
                        // Hiện lý do từ chối nếu có
                        if (!empty($row['reject_reason'])) {
                            echo '<div style="color:#e74c3c; font-size:0.85rem; margin-top:5px; max-width:200px;">Lý do: '.$row['reject_reason'].'</div>';
                        }
                    } else {
                        echo '<span style="background:rgba(241, 196, 15, 0.2); color:#f1c40f; padding:5px 10px; border-radius:15px; font-weight:bold; font-size:0.9rem;">Đang chờ duyệt <i class="fas fa-clock"></i></span>';
                    }
                    ?>
                </div>
            </div>
        <?php 
            endwhile; 
        }
        ?>
    </div>

    <!-- 2. LỊCH SỬ MUA HÀNG -->
    <div style="background:var(--gray); padding:20px; border-radius:10px; border:1px solid #333;">
        <h3 style="color:var(--gold); margin-bottom:15px; border-bottom:1px dashed #555; padding-bottom:10px;">
            <i class="fas fa-shopping-bag"></i> Đơn Hàng Sản Phẩm
        </h3>
        
        <?php 
        // Truy vấn bảng orders theo username (PDO)
        $username = $_SESSION['user'];
        $stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY created_at DESC");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() == 0) {
            echo "<p style='color:#888; font-style:italic;'>Bạn chưa mua sản phẩm nào.</p>";
        } else {
            while($row = $stmt->fetch()): 
                $items = json_decode($row['items'], true);
        ?>
            <div style="border-bottom:1px solid #444; padding:20px 0;">
                <!-- Header Đơn hàng -->
                <div style="display:flex; justify-content:space-between; margin-bottom:10px; align-items:flex-start;">
                    <div>
                        <span style="color:#888; font-size:0.9rem; display:block;">Ngày đặt: <?php echo date("d/m/Y H:i", strtotime($row['created_at'])); ?></span>
                        <?php if (!empty($row['address'])): ?>
                            <div style="color:#ccc; font-size:0.9rem; margin-top:5px;">
                                <i class="fas fa-map-marker-alt" style="color:var(--gold)"></i> 
                                <?php echo $row['address']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="text-align:right;">
                        <div style="font-size:1.2rem; font-weight:bold; color:var(--gold); margin-bottom:5px;">
                            <?php echo number_format($row['total_price']); ?>đ
                        </div>
                        
                        <!-- Trạng thái Đơn hàng -->
                        <?php 
                        if ($row['status'] == 'confirmed') {
                            echo '<span style="color:#2ecc71; font-weight:bold; font-size:0.9rem;">Đã giao hàng <i class="fas fa-truck"></i></span>';
                        } elseif ($row['status'] == 'rejected') {
                            echo '<span style="color:#e74c3c; font-weight:bold; font-size:0.9rem;">Đã hủy đơn</span>';
                        } else {
                            echo '<span style="color:#f1c40f; font-weight:bold; font-size:0.9rem;">Đang xử lý...</span>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Lý do hủy đơn (nếu có) -->
                <?php if ($row['status'] == 'rejected' && !empty($row['reject_reason'])): ?>
                    <div style="background:rgba(231, 76, 60, 0.1); border-left:3px solid #e74c3c; padding:10px; color:#e74c3c; margin-bottom:10px; font-size:0.9rem;">
                        <b>Lý do hủy:</b> <?php echo $row['reject_reason']; ?>
                    </div>
                <?php endif; ?>

                <!-- Danh sách sản phẩm -->
                <div style="background:rgba(0,0,0,0.2); padding:15px; border-radius:8px;">
                    <?php 
                    if (is_array($items)): 
                        foreach($items as $i): 
                            // Xử lý số lượng hiển thị
                            $qty_label = (isset($i['quantity']) && $i['quantity'] > 1) ? " <b style='color:white'>(x".$i['quantity'].")</b>" : "";
                    ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:8px;">
                            <span style="color:#ddd; font-size:0.95rem;">
                                <i class="fas fa-box-open" style="color:#666; margin-right:5px;"></i> 
                                <?php echo $i['name'] . $qty_label; ?>
                            </span>
                            
                            <!-- Nút Mua Lại -->
                            <?php if (isset($i['id'])): ?>
                                <a href="index.php?page=products&highlight=<?php echo $i['id']; ?>" 
                                   style="color:var(--gold); font-size:0.85rem; border:1px solid var(--gold); padding:2px 8px; border-radius:10px; transition:0.3s;"
                                   onmouseover="this.style.background='var(--gold)'; this.style.color='black'"
                                   onmouseout="this.style.background='transparent'; this.style.color='var(--gold)'">
                                   <i class="fas fa-redo"></i> Mua lại
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                </div>
            </div>
        <?php 
            endwhile; 
        }
        ?>
    </div>
</div>