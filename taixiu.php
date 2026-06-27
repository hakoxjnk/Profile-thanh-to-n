<?php
session_start(); // Bật Session để nhớ tên Khách

// ==========================================
// CẤU HÌNH DATABASE CHUẨN CỦA SẾP
// ==========================================
$db_host = 'localhost';
$db_name = 'hakoxjnk449_hakoxjnk'; 
$db_user = 'hakoxjnk449_hakoxjnk';     
$db_pass = 'hakoxjnk449_hakoxjnk';    

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // TỰ ĐỘNG TẠO BẢNG LƯU THIẾT BỊ ONLINE
    $pdo->exec("CREATE TABLE IF NOT EXISTS online_players (
        id VARCHAR(255) PRIMARY KEY,
        last_seen INT NOT NULL
    )");

    // TỰ ĐỘNG TẠO BẢNG LƯU TIN NHẮN CHAT
    $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_name VARCHAR(50),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Hệ thống đang bảo trì!");
}

// ==========================================
// API XỬ LÝ HEARTBEAT (MẮT XEM)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'heartbeat') {
    $sessionId = session_id();
    $now = time();

    $stmt = $pdo->prepare("REPLACE INTO online_players (id, last_seen) VALUES (?, ?)");
    $stmt->execute([$sessionId, $now]);

    $expired = $now - 7;
    $stmt = $pdo->prepare("DELETE FROM online_players WHERE last_seen < ?");
    $stmt->execute([$expired]);

    $stmt = $pdo->query("SELECT COUNT(*) FROM online_players");
    $realOnlineCount = $stmt->fetchColumn();

    header('Content-Type: application/json');
    echo json_encode(['online' => $realOnlineCount]);
    exit();
}

// ==========================================
// API XỬ LÝ CHAT (GỬI TIN)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'send_chat') {
    $msg = $_POST['message'] ?? '';
    // Nếu chưa có tên, random cho 1 cái tên Khách
    if (!isset($_SESSION['guest_name'])) {
        $_SESSION['guest_name'] = 'Khách_' . rand(1000, 9999);
    }
    $sender = $_SESSION['guest_name'];

    if (!empty(trim($msg))) {
        $safeMsg = htmlspecialchars(trim($msg)); // Chống hack XSS
        $pdo->prepare("INSERT INTO chat_messages (sender_name, message) VALUES (?, ?)")->execute([$sender, $safeMsg]);
    }
    exit();
}

// ==========================================
// API XỬ LÝ CHAT (LẤY TIN NHẮN)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'get_chat') {
    // Lấy 50 tin nhắn mới nhất
    $stmt = $pdo->query("SELECT * FROM (SELECT * FROM chat_messages ORDER BY id DESC LIMIT 50) sub ORDER BY id ASC");
    $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $me = $_SESSION['guest_name'] ?? '';
    $html = '';
    
    foreach($msgs as $m) {
        $isMine = ($m['sender_name'] === $me) ? 'mine' : 'other';
        if (strpos($m['sender_name'], 'Admin') !== false) {
            $isMine = 'admin';
        }
        $html .= '
        <div class="msg-box '.$isMine.'">
            <div class="msg-sender">'.$m['sender_name'].'</div>
            <div class="msg-content">'.$m['message'].'</div>
        </div>';
    }
    echo $html;
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hako Casino | Premium Tài Xỉu</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* TONE MÀU WIBU ANIME PASTEL */
            --bg-dark: #120b18; 
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.15);
            
            --tai-color: #a1c4fd; 
            --xiu-color: #ffb7c5; 
            
            --text-main: #ffffff;
            --text-muted: #b0bac5;

            --glow-tai: 0 0 25px rgba(161, 196, 253, 0.6);
            --glow-xiu: 0 0 25px rgba(255, 183, 197, 0.6);
            --glow-white: 0 0 15px rgba(255, 255, 255, 0.5);
            --glow-jackpot: 0 0 20px rgba(250, 204, 21, 0.8);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Quicksand', sans-serif; }
        
        body, html { min-height: 100vh; overflow-x: hidden; scroll-behavior: smooth; }
        
        body {
            /* THAY LINK ẢNH ANIME NỀN WEB VÀO ĐÂY */
            background: url('https://i.pinimg.com/736x/7c/07/e7/7c07e7cf6fb11300400c98f34474dd35.jpg') center/cover fixed no-repeat;
            color: var(--text-main); display: flex; flex-direction: column;
        }

        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(18, 11, 24, 0.6); backdrop-filter: blur(8px); z-index: -1; 
        }

        .container { width: 100%; max-width: 1000px; margin: 0 auto; padding: 0 20px; flex: 1; display: flex; flex-direction: column; }

        /* NAVBAR ĐỒNG BỘ MÀU PASTEL */
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 40px 0; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { color: var(--text-muted); text-decoration: none; font-weight: 700; font-size: 18px; transition: 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--text-main); text-shadow: var(--glow-white); }
        .theme-toggle { background: none; border: none; color: var(--text-main); font-size: 22px; cursor: pointer; transition: 0.3s; }

        /* GAME WRAPPER KÍNH MỜ BỒNG BỀNH */
        .game-wrapper {
            background: var(--glass-bg); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border); border-radius: 36px;
            padding: 40px; margin-bottom: 60px; box-shadow: 0 30px 60px rgba(0,0,0,0.4), inset 0 0 20px rgba(255,255,255,0.05);
            display: flex; flex-direction: column; align-items: center; position: relative; overflow: hidden;
        }

        .game-header { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .game-title-box { display: flex; flex-direction: column; gap: 5px; }
        .game-title { font-size: 28px; font-weight: 900; letter-spacing: 2px; text-shadow: var(--glow-white); }
        .online-count { font-size: 14px; font-weight: 700; color: #a1c4fd; display: flex; align-items: center; gap: 8px; text-shadow: var(--glow-tai); }
        .online-count i { animation: blink 2s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        .balance-box {
            background: rgba(255,255,255,0.05); padding: 10px 25px; border-radius: 30px;
            display: inline-flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 800;
            border: 1px solid var(--glass-border); color: #facc15; text-shadow: 0 0 10px rgba(250, 204, 21, 0.4);
        }

        /* KHU VỰC BÀN CƯỢC */
        .game-board { width: 100%; display: grid; grid-template-columns: 1fr 300px 1fr; gap: 25px; align-items: center; }

        .bet-zone {
            background: rgba(255,255,255,0.02); border: 2px solid var(--glass-border); border-radius: 28px; 
            padding: 30px 20px; text-align: center; cursor: pointer; transition: 0.4s;
            display: flex; flex-direction: column; justify-content: center; min-height: 250px; position: relative;
            box-shadow: inset 0 10px 20px rgba(0,0,0,0.2);
        }
        .bet-zone.disabled { pointer-events: none; opacity: 0.7; filter: grayscale(50%); }
        
        #zone-tai:hover { border-color: var(--tai-color); background: rgba(161, 196, 253, 0.05); box-shadow: var(--glow-tai); transform: translateY(-5px); }
        #zone-xiu:hover { border-color: var(--xiu-color); background: rgba(255, 183, 197, 0.05); box-shadow: var(--glow-xiu); transform: translateY(-5px); }
        
        .bet-title { font-size: 45px; font-weight: 900; margin-bottom: 2px; letter-spacing: 2px; }
        #zone-tai .bet-title { color: var(--tai-color); text-shadow: var(--glow-tai); }
        #zone-xiu .bet-title { color: var(--xiu-color); text-shadow: var(--glow-xiu); }
        
        .jackpot-text { font-size: 12px; font-weight: 800; color: #facc15; margin-bottom: 10px; text-shadow: var(--glow-jackpot); animation: blink 1.5s infinite; letter-spacing: 1px;}
        .fake-pool { font-size: 13px; color: var(--text-muted); margin-bottom: 15px; font-weight: 600; display: flex; flex-direction: column; gap: 5px;}
        .fake-pool span { color: #fff; text-shadow: 0 0 5px rgba(255,255,255,0.3);}
        .my-bet { background: rgba(255,255,255,0.1); padding: 10px; border-radius: 12px; font-size: 18px; font-weight: 700; color: #facc15; }

        /* TRUNG TÂM BÁT ĐĨA */
        .center-stage { display: flex; flex-direction: column; align-items: center; }
        
        .timer-ring {
            width: 70px; height: 70px; border-radius: 50%; border: 4px solid var(--glass-border);
            display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: 900;
            margin-bottom: 20px; background: rgba(255,255,255,0.05); box-shadow: var(--glow-white); transition: 0.3s;
        }
        .timer-ring.warning { border-color: var(--xiu-color); color: var(--xiu-color); text-shadow: var(--glow-xiu); animation: pulse 1s infinite; box-shadow: var(--glow-xiu);}
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        .timer-ring.locked { border-color: #f59e0b; color: #f59e0b; font-size: 16px; box-shadow: 0 0 15px rgba(245, 158, 11, 0.5); }

        .plate-container { position: relative; width: 260px; height: 260px; }
        
        .plate {
            width: 100%; height: 100%; 
            /* ẢNH ĐĨA ANIME NẰM Ở ĐÂY */
            background: url('https://i.pinimg.com/736x/64/76/89/647689583d30133f71b8c3e8862c8611.jpg') center/cover no-repeat;
            border-radius: 50%; border: 4px solid var(--glass-border); backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6), inset 0 10px 30px rgba(0,0,0,0.6);
            display: flex; justify-content: center; align-items: center; position: absolute; top: 0; left: 0; overflow: hidden;
        }
        .plate::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.3); z-index: 1;
        }

        /* TĂNG KHOẢNG CÁCH GIỮA 3 VIÊN XÚC XẮC */
        .dice-triangle { position: absolute; width: 140px; height: 130px; z-index: 5; top: 50%; left: 50%; transform: translate(-50%, -50%); }
        
        .dice { 
            position: absolute; font-size: 52px; 
            color: #ffffff; 
            text-shadow: 
                0px 1px 0px #cccccc,
                0px 2px 0px #bbbbbb,
                0px 3px 0px #999999,
                0px 4px 0px #7a7a7a,
                0px 6px 15px rgba(0,0,0,0.9);
            z-index: 10;
        }
        #d1 { top: 0; left: 50%; transform: translateX(-50%); } 
        #d2 { bottom: 0; left: 0; } 
        #d3 { bottom: 0; right: 0; } 

        /* NẮP BÁT MÀU ĐẶC KHÔNG XUYÊN THẤU */
        .lid {
            width: 270px; height: 270px; 
            background: radial-gradient(circle at 30% 30%, rgb(255, 183, 197) 0%, rgb(200, 100, 150) 100%);
            border-radius: 50%; position: absolute; top: -5px; left: -5px;
            box-shadow: inset 0 -15px 30px rgba(0,0,0,0.3), 0 20px 30px rgba(255, 183, 197, 0.4);
            z-index: 10; display: flex; justify-content: center; align-items: center;
            cursor: grab; touch-action: none; border: 2px solid rgba(255,255,255,0.4);
        }
        .lid:active { cursor: grabbing; }
        .lid::after {
            content: ''; width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.4); box-shadow: inset 0 5px 15px rgba(0,0,0,0.1);
        }

        .plate-container.shaking { animation: shakePlate 0.2s infinite alternate; }
        @keyframes shakePlate {
            0% { transform: translate(5px, 5px) rotate(0deg); }
            25% { transform: translate(-5px, -5px) rotate(-3deg); }
            50% { transform: translate(0px, 6px) rotate(3deg); }
            75% { transform: translate(6px, -5px) rotate(-3deg); }
            100% { transform: translate(-5px, 5px) rotate(0deg); }
        }

        .game-status { margin-top: 20px; font-size: 15px; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px; min-height: 24px; text-shadow: var(--glow-white); text-align: center;}

        /* CHỌN CHIP PASTEL */
        .chip-rack { margin-top: 30px; display: flex; gap: 15px; background: rgba(255,255,255,0.05); padding: 15px 25px; border-radius: 40px; border: 1px solid var(--glass-border); flex-wrap: wrap; justify-content: center; width: 100%; box-shadow: inset 0 5px 15px rgba(0,0,0,0.2); }
        .chip { width: 55px; height: 55px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 13px; color: #fff; cursor: pointer; border: 2px solid rgba(255,255,255,0.2); transition: 0.3s; box-shadow: 0 5px 10px rgba(0,0,0,0.2); backdrop-filter: blur(5px); }
        .chip.active { transform: translateY(-8px) scale(1.1); box-shadow: var(--glow-white); border-style: solid; border-color: #fff; background: rgba(255,255,255,0.3);}
        .chip.disabled { opacity: 0.5; pointer-events: none; filter: grayscale(100%);}

        /* THANH MINI HISTORY */
        .history-mini { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 30px; background: rgba(255,255,255,0.05); padding: 12px 25px; border-radius: 30px; border: 1px solid var(--glass-border); width: 100%; max-width: 500px; }
        .history-dots { display: flex; gap: 10px; align-items: center; }
        .btn-more-history { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: #fff; border-radius: 50%; width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.3s; }
        .btn-more-history:hover { background: rgba(255,255,255,0.3); transform: scale(1.1); box-shadow: var(--glow-white);}
        .c-dot { width: 24px; height: 24px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 11px; font-weight: 900; box-shadow: 0 2px 5px rgba(0,0,0,0.3); flex-shrink: 0; }
        .c-dot.tai { background: var(--tai-color); color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.5);}
        .c-dot.xiu { background: var(--xiu-color); color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.5);}

        /* MODAL SOI CẦU */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(18, 11, 24, 0.8); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999; display: flex; justify-content: center; align-items: center; padding: 20px; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-content { background: rgba(255, 255, 255, 0.1); border: 1px solid var(--glass-border); border-radius: 24px; width: 100%; max-width: 850px; padding: 25px; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.5), inset 0 0 20px rgba(255, 255, 255, 0.1); transform: translateY(30px) scale(0.95); transition: all 0.3s ease; backdrop-filter: blur(25px); display: flex; flex-direction: column; gap: 20px; }
        .modal-overlay.active .modal-content { transform: translateY(0) scale(1); }
        .close-btn { position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.1); border: none; color: #fff; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; transition: 0.3s; font-size: 16px; display: flex; justify-content: center; align-items: center; z-index: 10; }
        .close-btn:hover { background: var(--xiu-color); transform: rotate(90deg); box-shadow: var(--glow-xiu);}
        .modal-title-box { text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 15px;}
        .modal-title { font-size: 24px; font-weight: 900; letter-spacing: 2px; color: #fff; text-transform: uppercase; text-shadow: var(--glow-white);}
        .stats-header { display: flex; justify-content: center; gap: 20px; }
        .stat-pill { background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); padding: 8px 25px; border-radius: 30px; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .stat-pill.tai span { color: var(--tai-color); text-shadow: var(--glow-tai);}
        .stat-pill.xiu span { color: var(--xiu-color); text-shadow: var(--glow-xiu);}

        /* FIX LỖI BẢNG LỊCH SỬ BẰNG NỀN GRID CHẤM BI */
        .board-container { width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); border-radius: 12px; padding: 10px; overflow-x: auto; }
        .board-grid {
            display: grid; grid-template-rows: repeat(6, 28px); grid-auto-columns: 28px; grid-auto-flow: column; gap: 4px; min-width: 100%; 
            background-image: radial-gradient(circle, rgba(255,255,255,0.05) 12px, transparent 13px);
            background-size: 32px 32px; background-position: 0 0; padding-right: 15px;
        }
        .board-cell { width: 28px; height: 28px; display: flex; justify-content: center; align-items: center;}
        .board-cell .c-dot { width: 24px; height: 24px; font-size: 11px; }

        .toast {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95); padding: 15px 30px; border-radius: 20px; color: #120b18;
            font-weight: 800; font-size: 18px; box-shadow: 0 15px 30px rgba(0,0,0,0.3); border: 2px solid var(--tai-color);
            opacity: 0; visibility: hidden; transition: 0.3s; z-index: 999999; text-align: center;
        }
        .toast.show { opacity: 1; visibility: visible; top: 40px; }
        .toast.lose { border-color: var(--xiu-color); }
        .toast.jackpot { background: #facc15; border-color: #fff; box-shadow: var(--glow-jackpot); animation: shakePlate 0.3s infinite; }

        .blog-footer { padding: 30px 0; font-size: 15px; color: var(--text-muted); border-top: 1px solid rgba(255,255,255,0.05); text-align: center; }

        /* KHUNG CHAT LƠ LỬNG GÓC DƯỚI & THÊM BACKGROUND ẢNH ANIME */
        .chat-toggle-btn { 
            position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; 
            background: linear-gradient(135deg, var(--tai-color), var(--xiu-color)); border-radius: 50%; color: #fff; 
            font-size: 26px; border: 2px solid rgba(255,255,255,0.5); cursor: pointer; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.3), var(--glow-xiu); z-index: 1000; display: flex; justify-content: center; align-items: center; transition: 0.3s; 
        }
        .chat-toggle-btn:hover { transform: scale(1.1) rotate(10deg); }
        
        .chat-panel { 
            position: fixed; bottom: 110px; right: -500px; width: 360px; height: 500px; max-height: 75vh; 
            /* THÊM ẢNH NỀN CHO KHUNG CHAT TẠI ĐÂY NHA SẾP */
            background: linear-gradient(rgba(18, 11, 24, 0.75), rgba(18, 11, 24, 0.9)), url('https://i.pinimg.com/736x/1c/18/5f/1c185f3eca220b5566c9affa1befaf6d.jpg') center/cover;
            border: 1px solid var(--glass-border); border-radius: 24px; z-index: 1000; display: flex; flex-direction: column; transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 15px 40px rgba(0,0,0,0.4), inset 0 0 15px rgba(255,255,255,0.1); overflow: hidden;
        }
        .chat-panel.open { right: 30px; }
        
        .chat-header { background: rgba(0,0,0,0.4); padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; backdrop-filter: blur(5px); }
        .chat-title { font-size: 16px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px;}
        .chat-title i { color: var(--xiu-color); }
        .close-chat-btn { background: rgba(255,255,255,0.1); border-radius: 50%; width: 30px; height: 30px; border: none; color: #fff; font-size: 14px; cursor: pointer; transition: 0.3s;}
        .close-chat-btn:hover { background: var(--xiu-color); }
        
        .chat-body { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
        .chat-body::-webkit-scrollbar { width: 4px; }
        .chat-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 10px; }
        
        .msg-box { display: flex; flex-direction: column; max-width: 85%; }
        .msg-box.other { align-self: flex-start; }
        .msg-box.mine { align-self: flex-end; align-items: flex-end; }
        .msg-sender { font-size: 11px; color: var(--text-muted); font-weight: 700; margin-bottom: 4px; }
        .msg-box.mine .msg-sender { color: var(--tai-color); }
        .msg-box.admin .msg-sender { color: #facc15; text-shadow: 0 0 5px rgba(250, 204, 21, 0.5);}
        
        .msg-content { padding: 10px 14px; border-radius: 18px; font-size: 14px; line-height: 1.5; color: #fff; text-align: left; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        .msg-box.other .msg-content { background: rgba(255,255,255,0.15); border-top-left-radius: 4px; border: 1px solid rgba(255,255,255,0.05); backdrop-filter: blur(5px);}
        .msg-box.mine .msg-content { background: rgba(161, 196, 253, 0.3); border-top-right-radius: 4px; border: 1px solid rgba(161, 196, 253, 0.5); backdrop-filter: blur(5px);}
        .msg-box.admin .msg-content { background: rgba(250, 204, 21, 0.3); border: 1px solid rgba(250, 204, 21, 0.5); border-top-left-radius: 4px; backdrop-filter: blur(5px);}
        
        .chat-footer { background: rgba(0,0,0,0.4); padding: 15px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; gap: 10px; backdrop-filter: blur(5px); }
        .chat-input { flex: 1; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 10px 15px; border-radius: 20px; color: #fff; outline: none; transition: 0.3s; }
        .chat-input:focus { border-color: var(--tai-color); box-shadow: var(--glow-tai); }
        .chat-input::placeholder { color: rgba(255,255,255,0.5); }
        .chat-send-btn { width: 42px; height: 42px; background: var(--tai-color); border: none; border-radius: 50%; color: #120b18; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.3s; font-size: 16px;}
        .chat-send-btn:hover { transform: scale(1.1); box-shadow: var(--glow-tai); }

        @media (max-width: 800px) {
            .container { padding: 0 12px; } 
            .navbar { padding: 25px 0 15px 0; flex-direction: column; gap: 12px; }
            .nav-links { gap: 10px; flex-wrap: wrap; justify-content: center; }
            .nav-links a { font-size: 14px; background: rgba(255,255,255,0.1); padding: 6px 14px; border-radius: 20px; }
            .theme-toggle { display: none; } 
            .game-wrapper { padding: 20px 12px; border-radius: 30px; margin-bottom: 30px; }
            .game-header { flex-direction: column; gap: 12px; margin-bottom: 20px; text-align: center; }
            .game-title { font-size: 22px; }
            .online-count { justify-content: center; font-size: 13px; }
            .balance-box { font-size: 17px; padding: 6px 20px; }
            .game-board { grid-template-columns: 1fr 1fr; gap: 12px; }
            .center-stage { grid-column: span 2; order: -1; margin-bottom: 5px; } 
            .plate-container, .plate { width: 210px; height: 210px; }
            .lid { width: 220px; height: 220px; }
            .dice-triangle { width: 110px; height: 100px; } /* Xúc xắc to và giãn ra trên Mobile */
            .dice { font-size: 38px; }
            .timer-ring { width: 60px; height: 60px; font-size: 24px; margin-bottom: 12px; }
            .bet-zone { min-height: 140px; padding: 15px 10px; border-radius: 24px;}
            .bet-title { font-size: 32px; margin-bottom: 2px; }
            .jackpot-text { font-size: 10px; }
            .my-bet { font-size: 14px; padding: 6px; border-radius: 10px; }
            .chip-rack { gap: 8px; padding: 10px; margin-top: 20px; border-radius: 30px; }
            .chip { width: 44px; height: 44px; font-size: 11px; }
            .history-mini { padding: 10px 15px; margin-top: 20px; }
            .c-dot { width: 20px; height: 20px; font-size: 9px; }
            .modal-content { padding: 15px; max-height: 90vh; gap: 15px; border-radius: 20px;}
            .stats-header { flex-direction: row; gap: 10px; justify-content: center; }
            .stat-pill { font-size: 14px; padding: 6px 15px; }
            .stat-pill span { font-size: 18px; }
            .board-container { padding: 6px; }
            .board-grid { background-size: 30px 30px; }
            .board-cell { width: 26px; height: 26px; }
            .board-cell .c-dot { width: 22px; height: 22px; font-size: 10px; }
            .chat-toggle-btn { width: 55px; height: 55px; font-size: 22px; bottom: 20px; right: 20px; }
            .chat-panel { width: calc(100% - 40px); bottom: 90px; right: -120%; height: 60vh; }
            .chat-panel.open { right: 20px; } 
        }
    </style>
</head>
<body>

    <div class="toast" id="toastMsg"></div>

    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="/">Home</a>
                <a href="/blog">My blog</a>
                <a href="/blogs">Blog</a>
                <a href="/payment">Donet</a>
                <a href="#" class="active">Casino</a>
            </div>
            <button class="theme-toggle"><i class="fa-regular fa-sun"></i></button>
        </nav>

        <div class="game-wrapper">
            <div class="game-header">
                <div class="game-title-box">
                    <div class="game-title">TÀI XỈU REAL-TIME</div>
                    <div class="online-count"><i class="fa-solid fa-eye"></i> Đang trong bàn: <span id="onlineCountText">1</span></div>
                </div>
                <div class="balance-box">
                    <i class="fa-solid fa-coins"></i> <span id="balanceText">10,000,000</span>
                </div>
            </div>

            <div class="game-board">
                <div class="bet-zone" id="zone-tai" onclick="placeBet('tai')">
                    <div class="bet-title">TÀI</div>
                    <div class="jackpot-text"><i class="fa-solid fa-star"></i> NỔ HŨ BÃO 6 (x3) <i class="fa-solid fa-star"></i></div>
                    <div class="fake-pool">Tổng: <span id="poolTai">0</span></div>
                    <div class="my-bet"><i class="fa-solid fa-user-ninja"></i> <span id="betTaiText">0</span></div>
                </div>

                <div class="center-stage">
                    <div class="timer-ring" id="timerText">--</div>
                    
                    <div class="plate-container" id="plateContainer">
                        <div class="plate" id="plateObj">
                            <div class="dice-triangle">
                                <i class="fa-solid fa-dice-one dice" id="d1"></i>
                                <i class="fa-solid fa-dice-two dice" id="d2"></i>
                                <i class="fa-solid fa-dice-three dice" id="d3"></i>
                            </div>
                        </div>
                        <div class="lid" id="lidObj"></div>
                    </div>

                    <div class="game-status" id="gameStatus">Đang tải...</div>
                </div>

                <div class="bet-zone" id="zone-xiu" onclick="placeBet('xiu')">
                    <div class="bet-title">XỈU</div>
                    <div class="jackpot-text"><i class="fa-solid fa-star"></i> NỔ HŨ BÃO 1 (x3) <i class="fa-solid fa-star"></i></div>
                    <div class="fake-pool">Tổng: <span id="poolXiu">0</span></div>
                    <div class="my-bet"><i class="fa-solid fa-user-ninja"></i> <span id="betXiuText">0</span></div>
                </div>
            </div>

            <div class="chip-rack" id="chipRack">
                <div class="chip active" data-val="5000" onclick="selectChip(this, 5000)">5K</div>
                <div class="chip" data-val="10000" onclick="selectChip(this, 10000)">10K</div>
                <div class="chip" data-val="15000" onclick="selectChip(this, 15000)">15K</div>
                <div class="chip" data-val="20000" onclick="selectChip(this, 20000)">20K</div>
                <div class="chip" data-val="50000" onclick="selectChip(this, 50000)">50K</div>
                <div class="chip" data-val="100000" onclick="selectChip(this, 100000)">100K</div>
                <div class="chip" data-val="500000" onclick="selectChip(this, 500000)">500K</div>
                <div class="chip" data-val="all" onclick="selectChip(this, 'all')">ALL</div>
            </div>

            <div class="history-mini">
                <div class="history-dots" id="miniHistoryDots"></div>
                <button class="btn-more-history" onclick="openHistoryModal()"><i class="fa-solid fa-list-ul"></i></button>
            </div>
        </div>

        <footer class="blog-footer">
            <p>© 2026 hakodev. All rights reserved.</p>
        </footer>
    </div>

    <!-- MODAL LỊCH SỬ -->
    <div id="historyModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-btn" onclick="closeHistoryModal()"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-title-box"><div class="modal-title">LỊCH SỬ PHIÊN</div></div>
            <div class="stats-header">
                <div class="stat-pill tai">Tài: <span id="statTai">0</span></div>
                <div class="stat-pill xiu">Xỉu: <span id="statXiu">0</span></div>
            </div>
            <div class="board-container">
                <div class="board-grid" id="historyGrid"></div>
            </div>
        </div>
    </div>

    <!-- KHUNG CHAT LƠ LỬNG -->
    <button class="chat-toggle-btn" onclick="toggleChat()">
        <i class="fa-solid fa-comment-dots"></i>
    </button>

    <div class="chat-panel" id="chatPanel">
        <div class="chat-header">
            <div class="chat-title"><i class="fa-solid fa-users"></i> Kênh Thế Giới</div>
            <button class="close-chat-btn" onclick="toggleChat()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="chat-body" id="chatBody">
            <div style="color:var(--text-muted); text-align:center; font-size:12px; margin-top:20px;">Đang tải tin nhắn...</div>
        </div>
        <div class="chat-footer">
            <input type="text" class="chat-input" placeholder="Nhập tin nhắn..." id="chatInput" onkeypress="handleEnter(event)">
            <button class="chat-send-btn" onclick="sendRealChat()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        // ===============================================
        // MẬT MÃ CHỐNG HACKER F12 (SFC32 + MURMURHASH3)
        // ===============================================
        function xmur3(str) {
            for(var i = 0, h = 1779033703 ^ str.length; i < str.length; i++) {
                h = Math.imul(h ^ str.charCodeAt(i), 3432918353);
                h = h << 13 | h >>> 19;
            } return function() {
                h = Math.imul(h ^ (h >>> 16), 2246822507);
                h = Math.imul(h ^ (h >>> 13), 3266489909);
                return (h ^= h >>> 16) >>> 0;
            }
        }
        function sfc32(a, b, c, d) {
            return function() {
                a >>>= 0; b >>>= 0; c >>>= 0; d >>>= 0; 
                var t = (a + b | 0) + d | 0;
                d = d + 1 | 0;
                a = b ^ b >>> 9;
                b = c + (c << 3) | 0;
                c = c << 21 | c >>> 11;
                c = c + t | 0;
                return (t >>> 0) / 4294967296;
            }
        }
        function lcgFake(seed) {
            return function() {
                seed = (seed * 1664525 + 1013904223) % 4294967296;
                return seed / 4294967296;
            };
        }
        function formatMoney(num) { return num.toLocaleString('en-US'); }

        let balance = 10000000;
        let currentChip = 10000;
        let myBets = { tai: 0, xiu: 0 };
        let hasResolvedRound = -1; 
        const ROUND_TIME = 50; 
        
        let dailyHistory = [];
        let totalTaiToday = 0;
        let totalXiuToday = 0;
        let hasGeneratedHistory = false;

        function sendHeartbeat() {
            fetch('?action=heartbeat')
                .then(res => res.json())
                .then(data => { document.getElementById('onlineCountText').innerText = data.online; })
                .catch(err => console.error(err));
        }
        setInterval(sendHeartbeat, 3000);

        function getDiceResult(roundId) {
            let seedFunc = xmur3("Hako_Secret_Key_Wibu_" + roundId);
            let rng = sfc32(seedFunc(), seedFunc(), seedFunc(), seedFunc());
            
            let randVal = rng();
            let d1, d2, d3;
            
            if (randVal < 0.015) { 
                d1 = 1; d2 = 1; d3 = 1; 
            } else if (randVal < 0.03) { 
                d1 = 6; d2 = 6; d3 = 6; 
            } else {
                do {
                    d1 = Math.floor(rng() * 6) + 1;
                    d2 = Math.floor(rng() * 6) + 1;
                    d3 = Math.floor(rng() * 6) + 1;
                } while (d1+d2+d3 === 3 || d1+d2+d3 === 18);
            }
            return { d1, d2, d3, total: d1+d2+d3, type: (d1+d2+d3 >= 11 ? 'tai' : 'xiu') };
        }

        function generateDailyHistory(currentRoundId) {
            dailyHistory = []; totalTaiToday = 0; totalXiuToday = 0;
            let now = new Date();
            let startOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
            let startRoundId = Math.floor(startOfDay / (ROUND_TIME * 1000));

            for(let i = startRoundId; i < currentRoundId; i++) {
                let res = getDiceResult(i);
                dailyHistory.push({ round: i, total: res.total, type: res.type });
                if(res.type === 'tai') totalTaiToday++; else totalXiuToday++;
            }
            updateHistoryUI();
        }

        function updateHistoryUI() {
            const miniDots = document.getElementById('miniHistoryDots');
            miniDots.innerHTML = '';
            let recent10 = dailyHistory.slice(-10);
            recent10.forEach(match => {
                let dot = document.createElement('div');
                dot.className = `c-dot ${match.type}`;
                dot.innerText = match.total;
                miniDots.appendChild(dot);
            });

            document.getElementById('statTai').innerText = totalTaiToday;
            document.getElementById('statXiu').innerText = totalXiuToday;

            const grid = document.getElementById('historyGrid');
            grid.innerHTML = '';
            let showList = dailyHistory.slice(-120);
            showList.forEach(match => {
                let cell = document.createElement('div');
                cell.className = 'board-cell';
                cell.innerHTML = `<div class="c-dot ${match.type}">${match.total}</div>`;
                grid.appendChild(cell);
            });
            const container = document.querySelector('.board-container');
            container.scrollLeft = container.scrollWidth;
        }

        function openHistoryModal() { document.getElementById('historyModal').classList.add('active'); }
        function closeHistoryModal() { document.getElementById('historyModal').classList.remove('active'); }

        setInterval(syncGameState, 1000);

        function syncGameState() {
            let now = Date.now();
            let roundId = Math.floor(now / (ROUND_TIME * 1000)); 
            let elapsed = Math.floor((now % (ROUND_TIME * 1000)) / 1000); 

            if(!hasGeneratedHistory) {
                generateDailyHistory(roundId);
                hasGeneratedHistory = true;
                sendHeartbeat();
            }

            let diceRes = getDiceResult(roundId);

            let rngFake = lcgFake(roundId + elapsed); 
            if(elapsed < 40) {
                document.getElementById('poolTai').innerText = formatMoney(Math.floor(rngFake() * 5000000) + 1500000 + myBets.tai);
                document.getElementById('poolXiu').innerText = formatMoney(Math.floor(rngFake() * 5000000) + 1500000 + myBets.xiu);
            }

            if (elapsed >= 0 && elapsed < 35) {
                let timeLeft = 35 - elapsed;
                elTimer.innerText = timeLeft; elTimer.className = 'timer-ring';
                elGameStatus.innerText = "Xin mời đặt cược!";
                unlockBoard();
                if (hasResolvedRound !== roundId && elapsed === 0) resetBoard();
            } 
            else if (elapsed >= 35 && elapsed < 40) {
                let timeLeft = 40 - elapsed;
                elTimer.innerText = timeLeft; elTimer.className = 'timer-ring warning';
                elGameStatus.innerText = "Dừng cược!";
                lockBoard();
            } 
            else if (elapsed >= 40 && elapsed < 50) {
                lockBoard();
                if (elapsed === 40) {
                    elTimer.innerText = "LẮC"; elTimer.className = 'timer-ring locked';
                    elPlateContainer.classList.add('shaking');
                    elLid.style.transition = '0.3s'; elLid.style.transform = 'translate(0px, 0px)';
                    isRevealed = false;
                } 
                else if (elapsed === 42) {
                    elPlateContainer.classList.remove('shaking');
                    elTimer.innerText = "NẶN"; elGameStatus.innerText = "Sếp có 6 giây để tự tay nặn bát!";
                    const icons = ['fa-dice-one', 'fa-dice-two', 'fa-dice-three', 'fa-dice-four', 'fa-dice-five', 'fa-dice-six'];
                    document.getElementById('d1').className = `fa-solid ${icons[diceRes.d1-1]} dice`;
                    document.getElementById('d2').className = `fa-solid ${icons[diceRes.d2-1]} dice`;
                    document.getElementById('d3').className = `fa-solid ${icons[diceRes.d3-1]} dice`;
                    canDragLid = true; elLid.style.transition = 'none'; 
                }
                else if (elapsed >= 48 && !isRevealed) {
                    autoReveal(diceRes, roundId);
                }
            }
        }

        const elBalance = document.getElementById('balanceText');
        const elTimer = document.getElementById('timerText');
        const elGameStatus = document.getElementById('gameStatus');
        const elPlateContainer = document.getElementById('plateContainer');
        const zoneTai = document.getElementById('zone-tai');
        const zoneXiu = document.getElementById('zone-xiu');
        const toast = document.getElementById('toastMsg');

        function selectChip(element, val) {
            document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            element.classList.add('active'); currentChip = val;
        }

        function placeBet(type) {
            if (zoneTai.classList.contains('disabled')) { showToast("Hết thời gian cược!", false); return; }
            let amountToBet = currentChip === 'all' ? balance : currentChip;
            if (amountToBet > balance || balance <= 0) { showToast("Sếp hết tiền rồi!", false); return; }
            balance -= amountToBet; myBets[type] += amountToBet;
            elBalance.innerText = formatMoney(balance);
            if(type === 'tai') document.getElementById('betTaiText').innerText = formatMoney(myBets.tai);
            if(type === 'xiu') document.getElementById('betXiuText').innerText = formatMoney(myBets.xiu);
        }

        function lockBoard() {
            zoneTai.classList.add('disabled'); zoneXiu.classList.add('disabled');
            document.querySelectorAll('.chip').forEach(c => c.classList.add('disabled'));
        }
        function unlockBoard() {
            zoneTai.classList.remove('disabled'); zoneXiu.classList.remove('disabled');
            document.querySelectorAll('.chip').forEach(c => c.classList.remove('disabled'));
        }
        function resetBoard() {
            myBets = { tai: 0, xiu: 0 };
            document.getElementById('betTaiText').innerText = "0"; document.getElementById('betXiuText').innerText = "0";
            elLid.style.transition = '0.4s'; elLid.style.transform = 'translate(0px, 0px)';
            isRevealed = false; canDragLid = false;
        }

        function showToast(msg, isWin, isJackpot = false) {
            toast.innerText = msg; 
            toast.className = `toast show ${isWin ? '' : 'lose'} ${isJackpot ? 'jackpot' : ''}`;
            setTimeout(() => { toast.classList.remove('show'); }, 3000);
        }

        // VẬT LÝ DRAG NẶN BÁT
        const elLid = document.getElementById('lidObj');
        let isDragging = false; let canDragLid = false; let isRevealed = false;
        let startX, startY, currentX = 0, currentY = 0;

        elLid.addEventListener('mousedown', startDrag);
        elLid.addEventListener('touchstart', startDrag, {passive: false});
        document.addEventListener('mousemove', drag);
        document.addEventListener('touchmove', drag, {passive: false});
        document.addEventListener('mouseup', endDrag);
        document.addEventListener('touchend', endDrag);

        function startDrag(e) {
            if (!canDragLid || isRevealed) return;
            isDragging = true;
            let clientX = e.clientX || e.touches[0].clientX;
            let clientY = e.clientY || e.touches[0].clientY;
            startX = clientX - currentX; startY = clientY - currentY;
        }

        function drag(e) {
            if (!isDragging) return;
            e.preventDefault(); 
            let clientX = e.clientX || e.touches[0].clientX;
            let clientY = e.clientY || e.touches[0].clientY;
            currentX = clientX - startX; currentY = clientY - startY;
            elLid.style.transform = `translate(${currentX}px, ${currentY}px)`;

            let distance = Math.sqrt(currentX*currentX + currentY*currentY);
            
            // TĂNG KHOẢNG CÁCH NẶN BÁT CỰC ĐẠI ĐỂ SẾP NẶN THẤY ĐƯỢC 3 HỘT
            let maxDistance = window.innerWidth < 800 ? 170 : 220; 
            
            if (distance > maxDistance) {
                isDragging = false;
                let now = Date.now();
                let roundId = Math.floor(now / (ROUND_TIME * 1000));
                let diceRes = getDiceResult(roundId);
                autoReveal(diceRes, roundId);
            }
        }
        function endDrag() { isDragging = false; }

        function autoReveal(diceRes, roundId) {
            if (isRevealed) return;
            isRevealed = true; canDragLid = false;
            elLid.style.transition = '0.5s ease-out';
            elLid.style.transform = `translate(${currentX > 0 ? 300 : -300}px, -200px)`;
            
            let isJackpot = (diceRes.total === 3 || diceRes.total === 18);
            elGameStatus.innerText = `KẾT QUẢ: ${diceRes.type.toUpperCase()} (${diceRes.total})`;

            if (hasResolvedRound !== roundId) {
                hasResolvedRound = roundId; 
                dailyHistory.push({ round: roundId, total: diceRes.total, type: diceRes.type });
                if(diceRes.type === 'tai') totalTaiToday++; else totalXiuToday++;
                updateHistoryUI();
                
                let wonAmount = 0, lostAmount = 0;
                
                if (diceRes.type === 'tai') { 
                    wonAmount = isJackpot ? myBets.tai * 3 : myBets.tai * 2; 
                    lostAmount = myBets.xiu; 
                } else { 
                    wonAmount = isJackpot ? myBets.xiu * 3 : myBets.xiu * 2; 
                    lostAmount = myBets.tai; 
                }
                
                balance += wonAmount; elBalance.innerText = formatMoney(balance);

                if (wonAmount > 0) {
                    if (isJackpot) showToast(`🎉 CHÚC MỪNG NỔ HŨ X3! Húp ${formatMoney(wonAmount - (diceRes.type==='tai'?myBets.tai:myBets.xiu))} 🎉`, true, true);
                    else showToast(`Húp ${formatMoney(wonAmount / 2)}!`, true);
                } 
                else if (lostAmount > 0) showToast(`Toang cmnr!`, false);
            }
            currentX = 0; currentY = 0;
        }

        // ==========================================
        // LOGIC CHAT REAL-TIME 
        // ==========================================
        function toggleChat() { document.getElementById('chatPanel').classList.toggle('open'); }

        function loadChatRealTime() {
            fetch('?action=get_chat')
                .then(res => res.text())
                .then(html => {
                    let chatBody = document.getElementById('chatBody');
                    let isScrolledToBottom = chatBody.scrollHeight - chatBody.clientHeight <= chatBody.scrollTop + 50;
                    if(html.trim() !== '') { chatBody.innerHTML = html; }
                    if(isScrolledToBottom) { chatBody.scrollTop = chatBody.scrollHeight; }
                });
        }

        function handleEnter(e) { if (e.key === 'Enter') sendRealChat(); }

        function sendRealChat() {
            let input = document.getElementById('chatInput');
            let msg = input.value;
            if(!msg.trim()) return;
            
            input.value = ''; 
            let formData = new FormData();
            formData.append('message', msg);
            
            fetch('?action=send_chat', { method: 'POST', body: formData }).then(() => { loadChatRealTime(); });
        }

        loadChatRealTime();
        setInterval(loadChatRealTime, 2000);
    </script>
</body>
</html>
