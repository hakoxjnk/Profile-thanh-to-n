<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HakoXjnk | Premium Payment Portal</title>
    <link rel="icon" type="image/x-icon" href="https://i.pinimg.com/736x/2b/7d/10/2b7d103bfadc92765cce6c79a5ab6924.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-main: #0a0b10;
            --card-bg: rgba(20, 21, 26, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
            --text-main: #ffffff;
            --text-muted: #9ca3af;
            --accent-blue: #3b82f6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body, html { overflow-x: hidden; scroll-behavior: smooth; }

        body {
            font-family: 'Quicksand', sans-serif;
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: url('https://i.pinimg.com/736x/42/b8/26/42b826d33fc960698412539c2b1611ad.jpg') center/cover fixed no-repeat;
            position: relative;
        }

        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(10, 11, 16, 0.92); 
            z-index: -1; 
        }

        .container { width: 100%; max-width: 900px; margin: 0 auto; padding: 0 20px; flex: 1; position: relative; z-index: 10; }

        /* NAVBAR */
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 40px 0; }
        .nav-links a { color: var(--text-muted); text-decoration: none; font-weight: 700; margin-right: 30px; font-size: 18px; transition: 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--text-main); }
        .theme-toggle { background: none; border: none; color: var(--text-main); font-size: 22px; cursor: pointer; transition: 0.3s; }
        .theme-toggle:hover { transform: rotate(45deg); }

        /* HIỆU ỨNG LƯỚT */
        .reveal {
            opacity: 0; transform: translate3d(0, 40px, 0); 
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
            will-change: opacity, transform; 
        }
        .reveal.active { opacity: 1; transform: translate3d(0, 0, 0); }

        /* PROFILE CARD */
        .profile-card {
            background: rgba(20, 21, 26, 0.65); 
            backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px; overflow: hidden; margin-bottom: 60px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
        }

        .profile-banner { width: 100%; height: 240px; object-fit: cover; display: block; }
        .profile-body { padding: 0 50px 50px; display: flex; gap: 50px; align-items: flex-start; }
        
        .avatar-wrap img {
            width: 170px; height: 170px; border-radius: 50%; object-fit: cover;
            border: 6px solid #13151a; margin-top: -85px; position: relative;
            background: #13151a; box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        
        .profile-info { flex: 1; margin-top: 25px;}
        .profile-info h1 { font-size: 34px; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px;}
        .profile-info h1 i { color: #38bdf8; font-size: 20px; }
        
        .role { color: #a1c4fd; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; }
        .profile-divider { width: 100%; height: 1px; background: rgba(255, 255, 255, 0.08); margin-bottom: 30px; }

        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px 40px; }
        .contact-item { display: flex; flex-direction: column; gap: 5px; }
        .contact-label { font-size: 12px; color: #8b949e; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; display: flex; align-items: center; gap: 8px; }
        .contact-label i { font-size: 14px; color: #4b5563; }
        .contact-value { font-size: 16px; font-weight: 500; color: #f3f4f6; }

        /* KHU VỰC THẺ THANH TOÁN */
        .section-title {
            font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 30px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; gap: 12px;
        }
        .section-title::before { content: ""; display: block; width: 30px; height: 4px; background: var(--accent-blue); border-radius: 4px; }

        .payment-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 60px; }

        .pay-card {
            background: rgba(20, 21, 26, 0.5); border: 1px solid var(--border-color); border-radius: 20px;
            padding: 30px 20px; text-align: center; transition: all 0.3s ease;
            position: relative; overflow: hidden; backdrop-filter: blur(10px); cursor: pointer;
        }
        .pay-card:hover { transform: translateY(-5px); background: rgba(30, 32, 40, 0.8); border-color: rgba(255,255,255,0.3); box-shadow: 0 10px 20px rgba(0,0,0,0.4); }
        .pay-card:active { transform: scale(0.97) translateY(0); }
        
        .pay-card::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 4px; }
        .card-1::before { background: linear-gradient(90deg, #ec4899, #f43f5e); } 
        .card-2::before { background: linear-gradient(90deg, #10b981, #047857); } 
        .card-3::before { background: linear-gradient(90deg, #ef4444, #b91c1c); } 
        .card-4::before { background: linear-gradient(90deg, #0c3696, #3868d9); } 

        .bank-logo { height: 45px; max-width: 100%; object-fit: contain; margin-bottom: 15px; }
        .pay-name { font-size: 16px; color: var(--text-muted); margin-bottom: 5px; font-weight: 600;}
        .pay-number { font-size: 24px; font-weight: 800; letter-spacing: 1px; margin-bottom: 25px; color: #fff;}

        .copy-btn {
            background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid rgba(255,255,255,0.15); 
            padding: 12px 30px; border-radius: 30px; font-family: 'Quicksand', sans-serif;
            font-size: 14px; font-weight: 700; cursor: pointer; transition: 0.2s;
            display: inline-flex; align-items: center; gap: 8px; width: 100%; justify-content: center;
        }
        .copy-btn:hover { background: #fff; color: #000; }

        /* =========================================
           🔥 CSS CHO MODAL THANH TOÁN (BẢNG NỔI) 🔥
           ========================================= */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            z-index: 99999; display: flex; justify-content: center; align-items: center;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }

        .modal-content {
            background: rgba(20, 21, 26, 0.9); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 24px; width: 90%; max-width: 400px; padding: 30px;
            text-align: center; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            transform: translateY(30px) scale(0.95); transition: all 0.3s ease;
        }
        .modal-overlay.active .modal-content { transform: translateY(0) scale(1); }

        .close-btn {
            position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.1);
            border: none; color: #fff; width: 35px; height: 35px; border-radius: 50%;
            cursor: pointer; transition: 0.2s; font-size: 16px; display: flex; justify-content: center; align-items: center;
        }
        .close-btn:hover { background: #ff4757; transform: rotate(90deg); }

        .modal-header { margin-bottom: 20px; }
        .modal-logo { height: 40px; margin-bottom: 10px; object-fit: contain; }
        .modal-bank-name { font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff;}

        .qr-box {
            background: #fff; padding: 15px; border-radius: 16px; margin-bottom: 20px;
            display: inline-flex; justify-content: center; align-items: center; box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            min-width: 200px; min-height: 200px;
        }
        .qr-box img { width: 180px; height: 180px; display: block; object-fit: contain; border-radius: 8px;}

        /* 🔥 ĐÃ FIX CANH GIỮA Ở ĐÂY 🔥 */
        .modal-details { 
            background: rgba(255,255,255,0.05); border-radius: 12px; padding: 15px; 
            margin-bottom: 25px; border: 1px solid rgba(255,255,255,0.05); 
            text-align: center; /* Căn giữa toàn bộ chữ */
        }
        .detail-row { 
            display: flex; flex-direction: column; align-items: center; /* Ép vào giữa trục */
            margin-bottom: 12px; 
        }
        .detail-row:last-child { margin-bottom: 0; }
        .detail-label { font-size: 12px; color: #9ca3af; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 3px; }
        .detail-value { font-size: 18px; color: #fff; font-weight: 700; }
        .detail-value.number { font-size: 22px; color: #38bdf8; letter-spacing: 1px;}

        .modal-copy-btn {
            width: 100%; background: #3b82f6; color: #fff; border: none; padding: 15px;
            border-radius: 12px; font-family: 'Quicksand', sans-serif; font-size: 16px; font-weight: 700;
            cursor: pointer; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .modal-copy-btn:hover { background: #2563eb; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4); }
        .modal-copy-btn.copied { background: #10b981; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.4); }

        /* HIỆU ỨNG TIM BAY VÀ TUYẾT RƠI */
        .click-heart {
            position: fixed; color: #ff4757; font-size: 24px; pointer-events: none; z-index: 9999;
            transform: translate3d(-50%, -50%, 0); animation: floatHeart 1s ease-out forwards;
            will-change: transform, opacity;
        }
        @keyframes floatHeart {
            0% { transform: translate3d(-50%, -50%, 0) scale(0.5); opacity: 1; }
            50% { transform: translate3d(-50%, -80px, 0) scale(1.2); opacity: 1; }
            100% { transform: translate3d(-50%, -120px, 0) scale(1.5); opacity: 0; }
        }

        .snowflake {
            position: fixed; top: -10px; background: rgba(255, 255, 255, 0.8);
            border-radius: 50%; pointer-events: none; z-index: 1; 
            animation: fall linear infinite; will-change: transform; 
        }
        @keyframes fall {
            0% { transform: translate3d(0, -10px, 0); opacity: 1; }
            100% { transform: translate3d(30px, 105vh, 0); opacity: 0.1; }
        }

        .blog-footer { padding: 40px 0; font-size: 15px; color: var(--text-muted); border-top: 1px solid var(--border-color); text-align: center; }

        /* ĐIỆN THOẠI */
        @media (max-width: 768px) {
            .navbar { padding: 25px 0; }
            .nav-links a { margin-right: 15px; font-size: 15px; }
            .theme-toggle { font-size: 18px; }
            .profile-card { border-radius: 20px; margin-bottom: 40px; }
            .profile-banner { height: 150px; }
            .profile-body { flex-direction: column; text-align: center; padding: 0 20px 30px; gap: 15px; align-items: center; }
            .avatar-wrap img { width: 120px; height: 120px; margin-top: -60px; border-width: 4px; }
            .profile-info { margin-top: 0; width: 100%; }
            .profile-info h1 { font-size: 24px; justify-content: center; gap: 8px;}
            .profile-info h1 i { font-size: 16px; }
            .role { font-size: 12px; margin-bottom: 20px; }
            .profile-divider { margin-bottom: 20px; }
            .contact-grid { grid-template-columns: 1fr; gap: 15px; text-align: left; background: rgba(255,255,255,0.02); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); }
            .contact-value { font-size: 14px; }
            .section-title { font-size: 20px; margin-bottom: 20px; }
            .payment-grid { gap: 15px; margin-bottom: 40px; }
            .pay-card { padding: 25px 15px; border-radius: 16px; }
            .bank-logo { height: 35px; }
            .pay-number { font-size: 20px; margin-bottom: 15px; }
            .copy-btn { padding: 10px 20px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <nav class="navbar reveal active">
            <div class="nav-links">
                <a href="/">Home</a>
                <a href="/blog">My blog</a>
                <a href="/blogs">Blog</a>
                <a href="#" class="active">Donate</a>
                <a href="/taixiu">Casio</a>
                
            </div>
            <button class="theme-toggle"><i class="fa-regular fa-sun"></i></button>
        </nav>

        <div class="profile-card reveal">
            <img class="profile-banner" src="https://i.pinimg.com/webp/1200x/21/25/2d/21252da8e8810e8fc49eb266d5a84af4.webp" alt="Banner Cover">
            <div class="profile-body">
                <div class="avatar-wrap">
                    <img src="https://i.pinimg.com/1200x/8f/51/b6/8f51b60aecff8b7693ebfc128bab78ff.jpg" alt="Avatar">
                </div>
                <div class="profile-info">
                    <h1>Trần Thiên An <i class="fa-solid fa-circle-check"></i></h1>
                    <p class="role">Web Developer • UI/UX Designer</p>
                    <div class="profile-divider"></div>
                    <div class="contact-grid">
                        <div class="contact-item">
                            <span class="contact-label"><i class="fa-regular fa-envelope"></i> Email</span>
                            <span class="contact-value">contact@hakoxjnk.io.vn</span>
                        </div>
                        <div class="contact-item">
                            <span class="contact-label"><i class="fa-solid fa-link"></i> Website</span>
                            <span class="contact-value">hakoxjnk.io.vn</span>
                        </div>
                        <div class="contact-item">
                            <span class="contact-label"><i class="fa-solid fa-phone"></i> Phone</span>
                            <span class="contact-value">038.8178.814</span>
                        </div>
                        <div class="contact-item">
                            <span class="contact-label"><i class="fa-solid fa-location-dot"></i> Location</span>
                            <span class="contact-value">Sai Gon City</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="section-title reveal">Thông Tin Thanh Toán</h2>
        <div class="payment-grid">
            
            <div class="pay-card card-1 reveal">
                <img src="https://developers.momo.vn/v3/vi/assets/images/MOMO-Logo-App-6262c3743a290ef02396a24ea2b66c35.png" alt="Momo Logo" class="bank-logo">
                <div class="pay-name">TRAN THIEN AN</div>
                <div class="pay-number" id="stk1">Bảo Trì !</div>
                <button class="copy-btn" onclick="openPaymentModal(event, 'MOMO', 'https://developers.momo.vn/v3/vi/assets/images/MOMO-Logo-App-6262c3743a290ef02396a24ea2b66c35.png', 'TRAN THIEN AN', 'Bảo Trì !', 'https://cdn-images.dzcdn.net/images/cover/1714d8c6d6f96f951b5f3fac9423f801/0x1900-000000-80-0-0.jpg')">
                    <i class="fa-solid fa-qrcode"></i> THÔNG TIN CHUYỂN KHOẢN
                </button>
            </div>
            
            <div class="pay-card card-2 reveal">
                <img src="https://i.ibb.co/qM7Q85Dj/rounded-in-photoretrica-1.png" alt="Vietcombank Logo" class="bank-logo">
                <div class="pay-name">NGUYEN XUAN PHONG</div>
                <div class="pay-number" id="stk2">9772688841</div>
                <button class="copy-btn" onclick="openPaymentModal(event, 'VIETCOMBANK', 'https://i.ibb.co/qM7Q85Dj/rounded-in-photoretrica-1.png', 'NGUYEN XUAN PHONG', '9772688841', 'vcb')">
                    <i class="fa-solid fa-qrcode"></i> THÔNG TIN CHUYỂN KHOẢN
                </button>
            </div>
            
            <div class="pay-card card-3 reveal">
                <img src="https://i.ibb.co/FbW0KffP/rounded-in-photoretrica-2.png" alt="Techcombank Logo" class="bank-logo">
                <div class="pay-name">TRAN THIEN AN</div>
                <div class="pay-number" id="stk3">999997777770</div>
                <button class="copy-btn" onclick="openPaymentModal(event, 'TECHCOMBANK', 'https://i.ibb.co/FbW0KffP/rounded-in-photoretrica-2.png', 'TRAN THIEN AN', '999997777770', 'tcb')">
                    <i class="fa-solid fa-qrcode"></i> THÔNG TIN CHUYỂN KHOẢN
                </button>
            </div>
            
            <div class="pay-card card-4 reveal">
                <img src="https://i.ibb.co/3myy53T3/rounded-in-photoretrica-3.png" alt="Paypal Logo" class="bank-logo">
                <div class="pay-name">TRAN THIEN AN</div>
                <div class="pay-number" id="stk4">Bảo Trì !</div>
                <button class="copy-btn" onclick="openPaymentModal(event, 'PAYPAL', 'https://i.ibb.co/3myy53T3/rounded-in-photoretrica-3.png', 'TRAN THIEN AN', 'Bảo Trì !', 'https://cdn-images.dzcdn.net/images/cover/1714d8c6d6f96f951b5f3fac9423f801/0x1900-000000-80-0-0.jpg')">
                    <i class="fa-solid fa-qrcode"></i> THÔNG TIN CHUYỂN KHOẢN
                </button>
            </div>
        </div>

        <footer class="blog-footer reveal">
            <p>© 2026 hakodev. All rights reserved.</p>
        </footer>
    </div>

    <div id="paymentModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            
            <div class="modal-header">
                <img id="mBankLogo" class="modal-logo" src="" alt="Bank Logo">
                <h3 id="mBankName" class="modal-bank-name">Ngân Hàng</h3>
            </div>
            
            <div class="qr-box">
                <img id="mQRCode" src="" alt="Mã QR Thanh Toán">
            </div>
            
            <div class="modal-details">
                <div class="detail-row">
                    <span class="detail-label">Tên tài khoản</span>
                    <span id="mAccName" class="detail-value">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Số tài khoản</span>
                    <span id="mAccNumber" class="detail-value number">...</span>
                </div>
            </div>
            
            <button id="mCopyBtn" class="modal-copy-btn" onclick="copyFromModal()">
                <i class="fa-regular fa-copy"></i> COPY SỐ TÀI KHOẢN
            </button>
        </div>
    </div>

    <script>
        // =========================================
        // 🔥 JAVASCRIPT XỬ LÝ API QUÉT MÃ VIETQR 🔥
        // =========================================
        let currentCopyNumber = ""; 

        function openPaymentModal(e, bankName, logoUrl, accName, accNumber, qrData) {
            e.stopPropagation(); // Ngăn sự kiện thả tim trùng lặp
            
            // 1. Cập nhật Text
            document.getElementById('mBankName').innerText = bankName;
            document.getElementById('mBankLogo').src = logoUrl;
            document.getElementById('mAccName').innerText = accName;
            document.getElementById('mAccNumber').innerText = accNumber;
            
            // 2. Xử lý Ảnh Mã QR (Dùng API nếu là Ngân Hàng, Dùng Link nếu Bảo Trì)
            let qrImage = document.getElementById('mQRCode');
            if (accNumber === "Bảo Trì !" || qrData.startsWith("http")) {
                // Sếp đang bảo trì hoặc truyền link ảnh fix cứng thì dùng ảnh đó
                qrImage.src = qrData.startsWith("http") ? qrData : "https://st4.depositphotos.com/14953852/22772/v/450/depositphotos_227725020-stock-illustration-image-available-icon-flat-vector.jpg";
            } else {
                // Tự động gen QR chuẩn xịn qua VietQR (vd qrData = 'vcb' hoặc 'tcb')
                let safeName = encodeURIComponent(accName);
                qrImage.src = `https://img.vietqr.io/image/${qrData}-${accNumber}-compact2.png?accountName=${safeName}`;
            }

            // 3. Xử lý nút Copy (Ẩn đi nếu thẻ đang bảo trì)
            currentCopyNumber = accNumber; 
            let copyBtn = document.getElementById('mCopyBtn');
            if (accNumber === "Bảo Trì !") {
                copyBtn.style.display = 'none';
            } else {
                copyBtn.style.display = 'flex';
                copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> COPY SỐ TÀI KHOẢN';
                copyBtn.classList.remove('copied');
            }

            // Bật Popup lên giữa mặt
            document.getElementById('paymentModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        function copyFromModal() {
            navigator.clipboard.writeText(currentCopyNumber).then(function() {
                let btnElement = document.getElementById('mCopyBtn');
                btnElement.innerHTML = '<i class="fa-solid fa-check"></i> ĐÃ COPY THÀNH CÔNG';
                btnElement.classList.add('copied');
            }).catch(function(err) {
                alert("Lỗi không thể copy!");
            });
        }

        // Bấm nền đen tắt Popup
        window.onclick = function(event) {
            let modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // =========================================
        // CÁC HIỆU ỨNG CŨ CỦA SẾP GIỮ NGUYÊN
        // =========================================
        document.addEventListener("DOMContentLoaded", function() {
            const reveals = document.querySelectorAll(".reveal");
            const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -20px 0px" };
            const revealObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                        observer.unobserve(entry.target); 
                    }
                });
            }, revealOptions);
            reveals.forEach(reveal => revealObserver.observe(reveal));
            initSnow();
        });

        document.addEventListener('click', function(e) {
            // Không thả tim nếu bấm trúng nút của Modal
            if (e.target.closest('.close-btn') || e.target.closest('.copy-btn') || e.target.closest('.modal-copy-btn') || e.target.closest('.modal-content')) return;
            
            let heart = document.createElement('i');
            heart.classList.add('fa-solid', 'fa-heart', 'click-heart');
            heart.style.left = e.clientX + 'px';
            heart.style.top = e.clientY + 'px';
            document.body.appendChild(heart);
            setTimeout(() => { heart.remove(); }, 1000);
        });

        function initSnow() {
            const numFlakes = 25; 
            for (let i = 0; i < numFlakes; i++) {
                const snow = document.createElement('div');
                snow.classList.add('snowflake');
                snow.style.left = Math.random() * 100 + 'vw';
                snow.style.animationDuration = Math.random() * 3 + 3 + 's'; 
                snow.style.animationDelay = Math.random() * 5 + 's'; 
                const size = Math.random() * 4 + 3 + 'px';
                snow.style.width = size;
                snow.style.height = size;
                snow.style.opacity = Math.random() * 0.8 + 0.2;
                document.body.appendChild(snow);
            }
        }
    </script>
</body>
</html>