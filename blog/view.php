<?php
session_start();
require_once '../config/db.php';

// Get the post id from the URL e.g. view.php?id=1
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the post with author username
$stmt = $pdo->prepare("
    SELECT blogPost.*, user.username 
    FROM blogPost 
    JOIN user ON blogPost.user_id = user.id 
    WHERE blogPost.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch();

// If no post found, go back to home
if (!$post) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <nav>
        <a href="../index.php"><strong>BlogApp</strong></a>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create.php">+ New Post</a>
                <a href="../auth/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
            <?php else: ?>
                <a href="../auth/login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="meta">
            By <?php echo htmlspecialchars($post['username']); ?> 
            on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
        </p>

        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <div class="post-actions">
                <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
            </div>
        <?php endif; ?>

        <a href="../index.php">← Back to all posts</a>
    </div>

</body>
</html>