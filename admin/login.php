<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"]);
  $password = trim($_POST["password"]);
  $remember = isset($_POST["remember"]);

  $stmt = $conn->prepare("SELECT id, email, password, role FROM acc WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $email_db, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION["user_id"] = $id;
      $_SESSION["email"] = $email_db;
      $_SESSION["role"] = $role;

      // Set cookie if "remember me" is checked
      if ($remember) {
        setcookie("remember_email", $email_db, time() + (86400 * 30), "/"); // 30 days
      } else {
        setcookie("remember_email", "", time() - 3600, "/");
      }

      header("Location: dashboard.php");
      exit;
    } else {
      $error = "❌ Incorrect password.";
    }
  } else {
    $error = "❌ Account not found or unauthorized.";
  }

  $stmt->close();
}

// Get remembered email
$rememberedEmail = $_COOKIE['remember_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="login.css" />
</head>

<body>
  <div class="login-container">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($rememberedEmail) ?>" required />
      <input type="password" name="password" placeholder="Password" required />

      <div class="remember-row">
        <label><input type="checkbox" name="remember" <?= $rememberedEmail ? 'checked' : '' ?>> Remember me</label>
        <a href="forgot_password.php" class="forgot">Forgot Password?</a>
      </div>

      <button type="submit">Login</button>
    </form>
  </div>
</body>

</html>