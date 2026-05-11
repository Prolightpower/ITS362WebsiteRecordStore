<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' — ' : ''; ?>Vinyl Vault</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="nav-logo">
        <span class="logo-icon">◉</span> Vinyl Vault
    </a>
    <div class="nav-links">
        <a href="index.php">Shop</a>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="admin/dashboard.php" class="admin-link">Admin</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-nav">Log Out</a>
            <span class="nav-user">👋 <?php echo sanitize($_SESSION['username']); ?></span>
        <?php else: ?>
            <a href="login.php">Log In</a>
            <a href="signup.php" class="btn-nav">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>
            