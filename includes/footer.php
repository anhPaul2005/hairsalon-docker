<!-- CART MODAL -->
    <div id="cartModal" class="modal">
        <div class="modal-content" style="width: 500px; max-width: 95%;"> 
            <span onclick="closeCartModal()" style="float:right; cursor:pointer; font-size:24px">&times;</span>
            <h2 style="color:var(--black); margin-bottom:20px">GIỎ HÀNG</h2>
            
            <div style="max-height:80vh; overflow-y:auto; margin-bottom:20px; text-align:left; background:#f9f9f9; padding:15px; border-radius:5px;">
                <?php 
                $total=0;
                // Lấy trang hiện tại để khi tăng giảm số lượng sẽ load lại đúng trang đó
                $current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

                if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach($_SESSION['cart'] as $index => $c) {
                        $qty = isset($c['quantity']) ? $c['quantity'] : 1;
                        $line_total = $c['price'] * $qty;
                        $total += $line_total;
                        
                        echo "<div style='display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding:15px 0'>";
                        
                        // CỘT 1: Tên sp + Giá + Bộ chỉnh số lượng
                        echo "<div style='flex:1'>";
                        echo "<div style='font-weight:bold; font-size:1.05rem; color:#333'>{$c['name']}</div>";
                        echo "<div style='font-size:0.9em; color:#666; margin-top:2px'>".number_format($c['price'])."đ</div>";
                        
                        // [NÚT TĂNG GIẢM SỐ LƯỢNG]
                        echo "<div style='display:flex; align-items:center; gap:8px; margin-top:8px;'>";
                        // Nút giảm (-)
                        echo "<a href='index.php?update_qty={$index}&type=dec&page={$current_page}' style='display:inline-block; width:25px; height:25px; line-height:23px; text-align:center; border:1px solid #ddd; background:white; color:#333; text-decoration:none; border-radius:3px; font-weight:bold'>-</a>";
                        
                        // Số lượng hiện tại
                        echo "<span style='font-weight:bold; color:var(--gold); min-width:20px; text-align:center;'>{$qty}</span>";
                        
                        // Nút tăng (+)
                        echo "<a href='index.php?update_qty={$index}&type=inc&page={$current_page}' style='display:inline-block; width:25px; height:25px; line-height:23px; text-align:center; border:1px solid #ddd; background:white; color:#333; text-decoration:none; border-radius:3px; font-weight:bold'>+</a>";
                        echo "</div>";
                        
                        echo "</div>"; // End Cột 1

                        // CỘT 2: Thành tiền + Nút Xóa
                        echo "<div style='text-align:right'>";
                        echo "<div style='font-weight:bold; color:#c0392b; margin-bottom:5px'>".number_format($line_total)."đ</div>";
                        echo "<a href='index.php?remove_item={$index}&page={$current_page}' onclick=\"return confirm('Xóa món này?')\" style='color:#999; font-size:0.9rem; text-decoration:underline; cursor:pointer'><i class='fas fa-trash'></i> Xóa</a>";
                        echo "</div>";
                        
                        echo "</div>";
                    }
                    // TỔNG TIỀN
                    echo "<div style='text-align:right; margin-top:20px; padding-top:10px; border-top:2px solid #ddd'>";
                    echo "Tổng thanh toán: <span style='font-size:1.3rem; font-weight:bold; color:var(--gold)'>".number_format($total)."đ</span>";
                    echo "</div>";
                    
                    // --- BƯỚC 1: BẢN ĐỒ & ĐỊA CHỈ ---
                    echo "<div id='step1_address' style='margin-top:20px;'>";
                    echo "<label style='display:block; text-align:left; font-weight:bold; margin-bottom:8px; color:#333'><i class='fas fa-map-marker-alt'></i> Vị trí giao hàng:</label>";
                    
                    // Khung bản đồ Google Maps
                    echo "<div id='map' style='width: 100%; height: 250px; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 5px;'></div>";
                    echo "<p style='font-size:0.8rem; color:#666; margin-bottom:10px; font-style:italic'>(Kéo thả ghim đỏ để chọn vị trí chính xác)</p>";

                    // Ô nhập địa chỉ (Tự động điền)
                    echo "<textarea id='customerAddress' placeholder='Địa chỉ chi tiết sẽ hiện ở đây...' style='width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; height:60px; font-family:inherit; font-size:0.95rem'></textarea>";
                    
                    echo "<button onclick='goToPayment()' style='width:100%; background:var(--black); color:var(--gold); padding:15px; border:none; margin-top:15px; border-radius:5px; cursor:pointer; font-weight:bold; transition:0.3s; font-size:1rem'>TIẾP TỤC THANH TOÁN <i class='fas fa-arrow-right'></i></button>";
                    echo "</div>";

                    // --- BƯỚC 2: QUÉT MÃ QR ---
                    echo "<div id='step2_qr' style='display:none; text-align:center; margin-top:20px; animation: fadeIn 0.5s;'>";
                    echo "<p style='color:green; font-weight:bold; margin-bottom:15px; font-size:1.1rem'><i class='fas fa-check-circle'></i> Đã lưu địa chỉ giao hàng</p>";
                    
                    echo "<div style='background:white; padding:15px; display:inline-block; border:1px solid #ddd; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1)'>";
                    // Mã QR MB Bank
                    echo "<img src='https://img.vietqr.io/image/MB-9090010199999-compact.png' width='200'>";
                    echo "</div>";
                    echo "<p style='font-size:13px; margin-top:10px; color:#666'>Nội dung chuyển khoản: <b>Tên + SĐT</b></p>";
                    
                    echo "<button onclick='confirmPayment()' style='width:100%; background:#27ae60; color:white; padding:15px; border:none; margin-top:20px; border-radius:5px; cursor:pointer; font-weight:bold; box-shadow:0 4px 6px rgba(39, 174, 96, 0.2); font-size:1rem'>ĐÃ CHUYỂN KHOẢN XONG</button>";
                    echo "<button onclick='backToAddress()' style='background:none; border:none; color:#888; text-decoration:underline; cursor:pointer; margin-top:15px; font-size:0.9rem'><i class='fas fa-arrow-left'></i> Quay lại sửa địa chỉ</button>";
                    echo "</div>";

                } else { 
                    echo "<div style='text-align:center; padding:40px; color:#888'><i class='fas fa-shopping-basket' style='font-size:3rem; margin-bottom:10px; opacity:0.5'></i><p>Giỏ hàng đang trống.</p><a href='index.php?page=products' style='color:var(--gold); text-decoration:underline'>Mua sắm ngay</a></div>"; 
                }
                ?>
            </div>
        </div>
    </div>

    <!-- SCRIPT XỬ LÝ TOÀN BỘ WEBSITE -->
    <script>
        // 1. Hàm Admin từ chối lịch đặt
        function rejectBooking(id) {
            let reason = prompt("Nhập lý do từ chối (VD: Stylist bận, Hết chỗ...):");
            if (reason) {
                window.location.href = `index.php?page=admin&reject_booking=${id}&reason=${encodeURIComponent(reason)}`;
            }
        }

        // [MỚI] Hàm Admin từ chối đơn hàng
        function rejectOrder(id) {
            let reason = prompt("Nhập lý do hủy đơn hàng (VD: Hết hàng, Sai địa chỉ...):");
            if (reason) {
                window.location.href = `index.php?page=admin&reject_order=${id}&reason=${encodeURIComponent(reason)}`;
            }
        }

        // 2. Hàm chuyển đổi Login <-> Register
        function toggleForms() {
            const login = document.getElementById('loginForm');
            const register = document.getElementById('registerForm');
            if (login.classList.contains('hidden')) { 
                login.classList.remove('hidden'); 
                register.classList.add('hidden'); 
            } else { 
                login.classList.add('hidden'); 
                register.classList.remove('hidden'); 
            }
        }

        // 3. Xử lý Giỏ hàng & Google Maps
        let map, marker, geocoder;

        function initMap() {
            // Tọa độ mặc định (Ví dụ: TP.HCM)
            const defaultLoc = { lat: 10.7769, lng: 106.7009 }; 

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: defaultLoc,
                mapTypeControl: false, // Tắt các nút điều khiển thừa cho gọn
            });

            geocoder = new google.maps.Geocoder();

            // Tạo ghim (marker) có thể kéo thả
            marker = new google.maps.Marker({
                position: defaultLoc,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            // Sự kiện khi kéo thả marker xong -> Lấy địa chỉ
            marker.addListener("dragend", () => {
                geocodePosition(marker.getPosition());
            });

            // Sự kiện khi click vào bản đồ -> Di chuyển ghim tới đó
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                geocodePosition(e.latLng);
                map.panTo(e.latLng);
            });
        }

        // Chuyển tọa độ thành địa chỉ text
        function geocodePosition(pos) {
            geocoder.geocode({ location: pos }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        // Điền địa chỉ vào ô textarea
                        document.getElementById("customerAddress").value = results[0].formatted_address;
                    } else {
                        document.getElementById("customerAddress").value = "Không tìm thấy địa chỉ chi tiết.";
                    }
                } else {
                    document.getElementById("customerAddress").value = "Lỗi định vị: " + status;
                }
            });
        }

        // Mở giỏ hàng và load lại map (fix lỗi map bị xám)
        function openCartWithMap() {
            document.getElementById('cartModal').style.display = 'block';
            if(map) {
                setTimeout(() => {
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(marker.getPosition());
                }, 100);
            }
        }

        function closeCartModal() {
            document.getElementById('cartModal').style.display = 'none';
        }

        // Gán sự kiện click cho nút giỏ hàng trên Header
        document.addEventListener("DOMContentLoaded", function() {
            const btnCart = document.querySelector('.btn-cart');
            if(btnCart) {
                btnCart.setAttribute('onclick', 'openCartWithMap()');
            }

            // [MỚI] Tự động mở lại giỏ hàng nếu URL có tham số open_cart=1
            // (Được gửi từ logic.php sau khi tăng/giảm số lượng)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('open_cart')) {
                openCartWithMap();
                
                // Xóa tham số khỏi URL để không bị mở lại khi refresh trang bình thường
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?page=" + urlParams.get('page');
                window.history.replaceState({path: newUrl}, '', newUrl);
            }
        });

        // Chuyển bước thanh toán
        function goToPayment() {
            var addr = document.getElementById('customerAddress').value;
            if (addr.trim() === "") {
                alert("Vui lòng chọn vị trí trên bản đồ hoặc nhập địa chỉ!");
                document.getElementById('customerAddress').focus();
                return;
            }
            document.getElementById('step1_address').style.display = 'none';
            document.getElementById('step2_qr').style.display = 'block';
        }

        function backToAddress() {
            document.getElementById('step1_address').style.display = 'block';
            document.getElementById('step2_qr').style.display = 'none';
        }

        function confirmPayment() {
            var addr = document.getElementById('customerAddress').value;
            // Gửi yêu cầu thanh toán kèm địa chỉ
            window.location.href = 'index.php?checkout=1&address=' + encodeURIComponent(addr);
        }
    </script>

    <!-- LOAD GOOGLE MAPS API (Key của bạn) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDPsQMckuYUE-CjGyNDVHCqRfW887Hcp7c&callback=initMap&libraries=places&v=weekly" async defer></script>
</body>
</html>