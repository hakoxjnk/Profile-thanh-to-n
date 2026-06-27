<?php
// CẤU HÌNH DATABASE
$db_host = 'localhost';
$db_name = 'hakoxjnk449_hakoxjnk'; 
$db_user = 'hakoxjnk449_hakoxjnk';     
$db_pass = 'hakoxjnk449_hakoxjnk'; 

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Hệ thống đang bảo trì!");
}

$stmt = $pdo->query("SELECT * FROM posts ORDER BY id DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hako Blogs | Archive</title>
    <link rel="icon" type="image/x-icon" href="https://i.pinimg.com/736x/2b/7d/10/2b7d103bfadc92765cce6c79a5ab6924.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="./assets/css/blogs.css">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="/">Home</a>
                <a href="/blog">My Blog</a>
                <a href="/blogs" class="active">Blog</a>
                <a href="/payment">Donate</a>
                <a href="/taixiu">Casio</a> </div>
            <button class="theme-toggle"><i class="fa-regular fa-sun"></i></button>
        </nav>

        <div class="blogs-header">
            <h1>Blog</h1>
            <p>Umm... Welcome to my blog! I'm still working on it, but feel free to check back later for updates and new posts.</p>
        </div>

        <div class="blogs-list">
            <?php if (empty($posts)): ?>
                <p style="color: #ffffff; text-align: center;">Chưa có bài viết nào được phát hành.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <a href="/blog?id=<?php echo $post['id']; ?>" class="blogs-card">
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Thumbnail" class="blogs-thumb">
                        <div class="blogs-info">
                            <h3 class="blogs-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="blogs-desc">
                                <?php 
                                $desc = strip_tags($post['content']);
                                echo mb_strlen($desc) > 180 ? mb_substr($desc, 0, 180) . '...' : $desc; 
                                ?>
                            </p>
                            <span class="blogs-date">Published on <?php echo htmlspecialchars($post['date_published']); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <footer class="blog-footer">
            <p>© 2026 hakodev. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>