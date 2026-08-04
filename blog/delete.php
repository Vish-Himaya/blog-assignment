<?php
session_start();
require_once '../config/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the post first to verify ownership
$stmt = $pdo->prepare("SELECT * FROM blogPost WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

// If post doesn't exist or user doesn't own it, redirect
if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
    header("Location: ../index.php");
    exit();
}

// Delete the post
$stmt = $pdo->prepare("DELETE FROM blogPost WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

// Redirect to home
header("Location: ../index.php");
exit();
?>
