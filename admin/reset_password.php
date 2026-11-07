<?php
require 'db.php';
$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"];
    $newPassword = $_POST["password"];
    $confirmPassword = $_POST["confirm"];

    if ($newPassword !== $confirmPassword) {
        $error = "❌ Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at >= NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($email);
        if ($stmt->fetch()) {
            $stmt->close();
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE acc SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed, $email);
            $stmt->execute();

            $conn->query("DELETE FROM password_resets WHERE email = '$email'");
            $success = "✅ Password has been reset. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "❌ Invalid or expired token.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <div class="login-container">
        <h2>Reset Password</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
                <input type="password" name="password" placeholder="New password" required />
                <input type="password" name="confirm" placeholder="Confirm password" required />
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>