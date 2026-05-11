<?php
$pageTitle = 'Sign Up';
require_once __DIR__ . '/config.php';

if (isLoggedIn()) { header("Location: index.php"); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = "Username must be between 3 and 50 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username can only contain letters, numbers, and underscores.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $conn = getDB();
        $hashed = hash('sha256', $password);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed);

        if ($stmt->execute()) {
            $success = "Account created! Redirecting to login…";
            header("Refresh: 2; url=login.php");
        } else {
            $error = "Username or email already taken.";
        }
        $stmt->close();
        $conn->close();
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">
        <h2>Create Account</h2>
        <p class="subtitle">Join Vinyl Vault and start collecting.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo sanitize($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo sanitize($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="50"
                       value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>"
                       placeholder="letters, numbers, underscores" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="100"
                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>"
                       placeholder="you@example.com" autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="At least 6 characters" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm"
                       placeholder="Repeat your password" autocomplete="new-password">
            </div>
            <button type="submit" class="btn-primary">Create Account</button>
        </form>

        <div class="auth-switch">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
