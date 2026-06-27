<?php
// ==========================================
// CẤU HÌNH DATABASE
// ==========================================
$db_host = 'localhost';
$db_name = 'hakoxjnk449_hakoxjnk'; 
$db_user = 'hakoxjnk449_hakoxjnk';     
$db_pass = 'hakoxjnk449_hakoxjnk';    

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi MySQL thực sự là: " . $e->getMessage());
}

$postId = isset($_GET['id']) ? $_GET['id'] : null;
$currentPost = null;
$posts = [];

// Nếu có ID -> Load 1 bài chi tiết. Nếu không -> Load toàn bộ danh sách bài.
if ($postId) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=?");
    $stmt->execute([$postId]);
    $currentPost = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT * FROM posts ORDER BY id DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HakoXjnk Blog | <?php echo $currentPost ? htmlspecialchars($currentPost['title']) : "Web Development"; ?></title>
    <link rel="icon" type="image/x-icon" href="https://i.pinimg.com/736x/2b/7d/10/2b7d103bfadc92765cce6c79a5ab6924.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/blog.css">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="/">Home</a>
                 <a href="/blog" class="active">My blog</a>
                <a href="/blogs">Blog</a>
                <a href="/payment">Donate</a> 
                <a href="/taixiu">Casio</a>
            </div>
            <button class="theme-toggle"><i class="fa-regular fa-sun"></i></button>
        </nav>

        <?php if ($currentPost): ?>
            <article class="post-detail">
                <a href="/blogs" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Quay lại Blog</a>
                <h1 class="detail-title"><?php echo htmlspecialchars($currentPost['title']); ?></h1>
                <span class="detail-meta">Published on <?php echo htmlspecialchars($currentPost['date_published']); ?></span>
                
                <div class="detail-content">
                    <?php echo $currentPost['content']; ?>
                </div>
            </article>

        <?php else: ?>
            <section class="blog-hero">
                <div class="hero-avatar">
                    <img src="https://i.pinimg.com/736x/d5/28/89/d52889f524ac770e96440d2e0a90b82f.jpg" alt="Hako Avatar">
                </div>
                <div class="hero-info">
                    <h1>Welcome to Hako Blog</h1>
                    <p>This is a blog about my journey learning web development, sharing tips, and exploring new technologies. Stay tuned for updates and feel free to reach out if you have any questions or suggestions!</p>
                    <div class="hero-social">
                        <a href="#"><i class="fa-brands fa-github"></i></a>
                        <a href="#"><i class="fa-brands fa-discord"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-telegram"></i></a>
                        <a href="#"><i class="fa-brands fa-facebook-messenger"></i></a>
                    </div>
                </div>
            </section>

            <section class="recent-posts">
                <h2 class="section-title">Recent Posts</h2>
                <div class="post-list">
                    <?php if (empty($posts)): ?>
                        <p style="color: var(--text-muted);">Chưa có bài viết nào ở đây cả! Chờ Hako updet thêm nhiều skills nhé !</p>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <a href="?id=<?php echo htmlspecialchars($post['id']); ?>" class="post-item">
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Thumbnail" class="post-thumbnail">
                                <div class="post-item-info">
                                    <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <span class="post-meta">Published on <?php echo htmlspecialchars($post['date_published']); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <footer class="blog-footer">
            <p>© 2026 hakodev. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>