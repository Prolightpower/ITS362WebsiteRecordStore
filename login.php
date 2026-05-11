<?php
$pageTitle = 'Log In';
require_once __DIR__ . '/config.php';

if (isLoggedIn()) { header("Location: index.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter your username and password.";
    } else {
        $conn = getDB();
        $hashed = hash('sha256', $password);

        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $hashed);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect username or password.";
        }
        $stmt->close();
        $conn->close();
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to your Vinyl Vault account.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo sanitize($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="50"
                       value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>"
                       placeholder="Your username" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Your password" autocomplete="current-password">
            </div>
            <button type="submit" class="btn-primary">Log In</button>
        </form>

        <div class="divider"></div>

        <div class="auth-switch">
            Don't have an account? <a href="signup.php">Sign up</a>
        </div>

        <div style="margin-top:14px; padding:12px; background:rgba(201,168,76,0.06); border:1px solid rgba(201,168,76,0.2); border-radius:6px; font-size:12px; color:var(--muted);">
            <strong style="color:var(--gold);">Demo Admin:</strong> username <code style="color:var(--text);">admin</code> / password <code style="color:var(--text);">admin123</code>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
