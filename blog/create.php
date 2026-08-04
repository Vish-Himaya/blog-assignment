<?php
session_start();
require_once '../config/db.php';

// Authorization - must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content]);

        // Redirect to home page after creating
        header("Location: ../index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <nav>
        <a href="../index.php"><strong>BlogApp</strong></a>
        <div>
            <a href="../auth/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        </div>
    </nav>

    <div class="container">
        <h1>Create New Post</h1>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Title</label>
            <input type="text" name="title" required>

            <label>Content</label>
            <textarea name="content" rows="10" required></textarea>

            <button type="submit">Publish Post</button>
            <a href="../index.php" class="btn-cancel">Cancel</a>
        </form>
    </div>

</body>
</html>