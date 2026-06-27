<?php
session_start();

// ==========================================
// CẤU HÌNH DATABASE CỦA SẾP
// ==========================================
$db_host = 'localhost';
$db_name = 'hakoxjnk449_hakoxjnk'; 
$db_user = 'hakoxjnk449_hakoxjnk';     
$db_pass = 'hakoxjnk449_hakoxjnk'; // Sếp nhớ gõ lại pass vào đây nha
$admin_password = "123456"; // Pass để đăng nhập vào trang Admin (Sếp có thể tự đổi)

// Kết nối PDO
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
// Xử lý thêm, sửa, xóa bài viết
if (isset($_POST['create_post']) && isset($_SESSION['admin'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (title, image, content, date_published) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['image'], $_POST['content'], date("F j, Y")]);
        header("Location: admin.php?msg=Thêm bài thành công!"); exit;
    } catch (PDOException $e) {
        $error = "LỖI LƯU BÀI: Dung lượng ảnh dán vào quá lớn hoặc chưa chạy lệnh SQL LONGTEXT! (" . $e->getMessage() . ")";
    }
}

if (isset($_POST['edit_post']) && isset($_SESSION['admin'])) {
    try {
        $stmt = $pdo->prepare("UPDATE posts SET title=?, image=?, content=? WHERE id=?");
        $stmt->execute([$_POST['title'], $_POST['image'], $_POST['content'], $_POST['post_id']]);
        header("Location: admin.php?msg=Sửa bài thành công!"); exit;
    } catch (PDOException $e) {
        $error = "LỖI LƯU BÀI: Dung lượng ảnh dán vào quá lớn hoặc chưa chạy lệnh SQL LONGTEXT! (" . $e->getMessage() . ")";
    }
}

if (isset($_GET['delete']) && isset($_SESSION['admin'])) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin.php?msg=Đã xóa bài!"); exit;
}

// Lấy dữ liệu hiển thị
$stmt = $pdo->query("SELECT * FROM posts ORDER BY id DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$editPost = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editPost = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hako XJnk  Blog | Premium Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

    <style>
        body { font-family: 'Quicksand', sans-serif; background: #050505; color: #fff; margin: 0; }

        /* --- GIAO DIỆN ĐĂNG NHẬP KÍNH MỜ (GLASSMORPHISM) --- */
        .login-wrapper {
            height: 100vh; display: flex; align-items: center; justify-content: center;
            background: radial-gradient(circle at top right, #1a1a2e 0%, #050505 80%);
        }
        .login-card {
            background: rgba(20, 20, 25, 0.4); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.05); padding: 50px 40px;
            border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%; max-width: 400px; text-align: center;
        }
        .login-card img { width: 90px; height: 90px; object-fit: cover; margin-bottom: 20px; border-radius: 50%; border: 2px solid #27272a; }
        .login-card h2 { font-size: 28px; font-weight: 800; margin-bottom: 5px; color: #fff; letter-spacing: -0.5px;}
        .login-card p { color: #888; margin-bottom: 30px; font-size: 15px; font-weight: 500;}
        .login-input {
            width: 100%; padding: 16px 20px; background: rgba(0, 0, 0, 0.5);
            border: 1px solid #333; border-radius: 12px; color: #fff; font-size: 16px;
            margin-bottom: 25px; transition: 0.3s ease; box-sizing: border-box; outline: none;
            font-family: 'Quicksand', sans-serif; font-weight: 600; text-align: center; letter-spacing: 2px;
        }
        .login-input:focus { border-color: #a1c4fd; box-shadow: 0 0 15px rgba(161, 196, 253, 0.15); }
        .login-btn {
            width: 100%; padding: 16px; background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            color: #000; font-weight: 800; font-size: 17px; border: none; border-radius: 12px;
            cursor: pointer; transition: 0.3s; font-family: 'Quicksand', sans-serif;
        }
        .login-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(161, 196, 253, 0.4); }
        .error-msg { background: rgba(255, 71, 87, 0.1); color: #ff4757; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; border: 1px solid rgba(255, 71, 87, 0.2); }

        /* --- GIAO DIỆN BẢNG ĐIỀU KHIỂN (DASHBOARD) --- */
        .admin-container { max-width: 1000px; margin: 50px auto; background: #0d0d0d; padding: 50px; border-radius: 24px; border: 1px solid #27272a; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #27272a; padding-bottom: 20px; margin-bottom: 40px; }
        .admin-header h2 { margin: 0; color: #fff; font-size: 32px; font-weight: 800; }
        .logout-btn { background: rgba(255, 71, 87, 0.1); color: #ff4757; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; border: 1px solid rgba(255, 71, 87, 0.2); }
        .logout-btn:hover { background: #ff4757; color: #fff; }

        label { display: block; margin-bottom: 10px; font-weight: 700; color: #9ca3af; font-size: 16px; }
        input[type="text"], textarea {
            width: 100%; padding: 18px; margin-bottom: 30px; background: #050505;
            border: 1px solid #27272a; color: #fff; border-radius: 12px; box-sizing: border-box; font-size: 16px; font-family: 'Quicksand', sans-serif; transition: 0.3s;
        }
        input[type="text"]:focus, textarea:focus { border-color: #a1c4fd; outline: none; box-shadow: 0 0 15px rgba(161, 196, 253, 0.1); background: #0a0b10;}
        textarea { height: 350px; resize: vertical; line-height: 1.7; }

        .btn-submit { background: #a1c4fd; color: #000; font-weight: 800; padding: 16px 35px; border: none; border-radius: 12px; cursor: pointer; font-size: 17px; transition: 0.3s;}
        .btn-submit:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(161,196,253,0.3);}
        .btn-cancel { background: transparent; color: #ff4757; padding: 15px 30px; border-radius: 12px; text-decoration: none; display: inline-block; border: 1px solid #ff4757; margin-left: 15px; font-weight: 700; transition: 0.3s; }
        .btn-cancel:hover { background: #ff4757; color: #fff; }

        .post-list-admin { margin-top: 70px; border-top: 1px solid #27272a; padding-top: 40px; }
        .post-list-admin h2 { font-size: 26px; font-weight: 800; margin-bottom: 30px; }
        .admin-item { display: flex; justify-content: space-between; align-items: center; background: #050505; padding: 25px; border-radius: 16px; margin-bottom: 20px; border: 1px solid #27272a; transition: 0.3s;}
        .admin-item:hover { border-color: #444; transform: translateX(5px); background: #0a0b10; }
        .admin-item strong { font-size: 20px; color: #fff; font-weight: 700; display: block; margin-bottom: 5px;}
        .actions a { text-decoration: none; padding: 10px 20px; border-radius: 8px; margin-left: 10px; font-weight: 700; font-size: 14px; transition: 0.2s;}
        .edit-btn { background: rgba(241, 196, 15, 0.1); color: #f1c40f; border: 1px solid rgba(241, 196, 15, 0.3); }
        .edit-btn:hover { background: #f1c40f; color: #000; }
        .del-btn { background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); }
        .del-btn:hover { background: #e74c3c; color: #fff; }
        .success-msg { background: rgba(46, 213, 115, 0.1); color: #2ed573; padding: 15px; border-radius: 12px; font-weight: 700; border: 1px solid rgba(46, 213, 115, 0.3); margin-bottom: 30px; text-align: center;}
    </style>
</head>
<body>

<?php if (!isset($_SESSION['admin'])): ?>
    <div class="login-wrapper">
        <div class="login-card">
            <img src="https://i.pinimg.com/736x/2b/7d/10/2b7d103bfadc92765cce6c79a5ab6924.jpg" alt="Admin Avatar">
            <h2>Hệ Thống Quản Trị</h2>
            <p>Xác thực danh tính để vào hệ thống</p>
            
            <?php if (isset($error)) echo "<div class='error-msg'>$error</div>"; ?>

            <form method="POST">
                <input type="password" name="password" class="login-input" placeholder="••••••••" required>
                <button type="submit" name="login" class="login-btn">Truy Cập Ngay</button>
            </form>
        </div>
    </div>

<?php else: ?>
    <div class="admin-container">
        <?php if (isset($_GET['msg'])) echo "<div class='success-msg'>".$_GET['msg']."</div>"; ?>

        <div class="admin-header">
            <h2><?php echo $editPost ? "Chỉnh sửa bài viết" : "Tạo bài viết mới"; ?></h2>
            <a href="?logout=1" class="logout-btn">Đăng xuất</a>
        </div>

        <form method="POST">
            <?php if ($editPost): ?>
                <input type="hidden" name="post_id" value="<?php echo $editPost['id']; ?>">
            <?php endif; ?>

            <label>Tiêu đề bài viết:</label>
            <input type="text" name="title" placeholder="Nhập tiêu đề thật ấn tượng..." required value="<?php echo $editPost ? htmlspecialchars($editPost['title']) : ''; ?>">
            
            <label>Đường dẫn ảnh Thumbnail (URL):</label>
            <input type="text" name="image" placeholder="https://..." required value="<?php echo $editPost ? htmlspecialchars($editPost['image']) : ''; ?>">
            
            <label>Nội dung bài viết:</label>
            <textarea id="editor" name="content" placeholder="Bắt đầu viết những chia sẻ của bạn tại đây..."><?php echo $editPost ? htmlspecialchars($editPost['content']) : ''; ?></textarea>
            
            <?php if ($editPost): ?>
                <button type="submit" name="edit_post" class="btn-submit">Lưu Thay Đổi</button>
                <a href="admin.php" class="btn-cancel">Hủy Bỏ</a>
            <?php else: ?>
                <button type="submit" name="create_post" class="btn-submit">Phát Hành Bài Viết</button>
            <?php endif; ?>
        </form>

        <div class="post-list-admin">
            <h2>Kho Lưu Trữ (<?php echo count($posts); ?> bài viết)</h2>
            <?php foreach ($posts as $post): ?>
                <div class="admin-item">
                    <div>
                        <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                        <span style="color: #6b7280; font-size: 14px; font-weight: 500;">Phát hành: <?php echo htmlspecialchars($post['date_published']); ?></span>
                    </div>
                    <div class="actions">
                        <a href="?edit=<?php echo $post['id']; ?>" class="edit-btn">Sửa</a>
                        <a href="?delete=<?php echo $post['id']; ?>" class="del-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này vĩnh viễn không?');">Xóa</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <p style="text-align:center; margin-top: 50px;">
            <a href="/blog" target="_blank" style="color: #a1c4fd; text-decoration: none; font-weight: 700; letter-spacing: 1px;">&rarr; Mở trang Blog để kiểm tra</a>
        </p>
    </div>

    <script>
        tinymce.init({
            selector: '#editor', // Trỏ đúng ID của cái khung Textarea
            plugins: 'image link lists code wordcount fullscreen table',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | blockquote | link image | code fullscreen',
            paste_data_images: true, // TÍNH NĂNG QUAN TRỌNG: Cho phép copy/paste ảnh thẳng vào Word
            skin: 'oxide-dark',      // Giao diện Dark mode
            content_css: 'dark',
            height: 600,             // Chiều cao khung soạn thảo
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave(); // Đảm bảo ấn nút Lưu là nội dung được đẩy qua PHP
                });
            }
        });
    </script>
<?php endif; ?>

</body>
</html>