<?php
session_start();
require_once '../config/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the post
$stmt = $pdo->prepare("SELECT * FROM blogPost WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

// If post doesn't exist or user doesn't own it, redirect
if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
    header("Location: ../index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $id, $_SESSION['user_id']]);

        header("Location: view.php?id=$id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
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
        <h1>Edit Post</h1>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

            <label>Content</label>
            <textarea name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>

            <button type="submit">Update Post</button>
            <a href="view.php?id=<?php echo $post['id']; ?>" class="btn-cancel">Cancel</a>
        </form>
    </div>

</body>
</html>
