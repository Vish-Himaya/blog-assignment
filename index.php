<?php
session_start();
require_once 'config/db.php';

// Fetch all blog posts with author username, newest first
$stmt = $pdo->prepare("
    SELECT blogPost.id, blogPost.title, blogPost.created_at, user.username 
    FROM blogPost 
    JOIN user ON blogPost.user_id = user.id 
    ORDER BY blogPost.created_at DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav>
        <a href="index.php"><strong>BlogApp</strong></a>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="blog/create.php">+ New Post</a>
                <a href="auth/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
            <?php else: ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h1>Latest Posts</h1>

        <?php if (count($posts) === 0): ?>
            <p>No blog posts yet. <a href="blog/create.php">Create the first one!</a></p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <h2>
                        <a href="blog/view.php?id=<?php echo $post['id']; ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </h2>
                    <p class="meta">
                        By <?php echo htmlspecialchars($post['username']); ?> 
                        on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>