<?php
require 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT id FROM acc WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // valid 1 hour

        $conn->query("DELETE FROM password_resets WHERE email = '$email'");
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        // Send reset link (this is just a placeholder, adapt to your real email system)
        $resetLink = "https://yourdomain.com/reset_password.php?token=$token";
        // mail($email, "Reset Password", "Click to reset: $resetLink");

        $message = "✅ Reset link sent to your email.";
    } else {
        $message = "❌ Email not found.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <div class="login-container">
        <h2>Forgot Password</h2>
        <?php if ($message): ?>
            <p class="error"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required />
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="login.php">← Back to Login</a>
    </div>
</body>

</html>