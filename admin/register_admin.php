<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT role FROM acc WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'super_admin') {
    exit("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["email"])) {
    $email = trim($_POST["email"]);

    // Check if already exists
    $check = $conn->prepare("SELECT id FROM acc WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $check->close();
        header("Location: dashboard.php?exists=1");
        exit;
    }
    $check->close();

    // Insert with default password
    $defaultPassword = password_hash("Default@123", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO acc (email, password, role) VALUES (?, ?, 'admin')");
    $stmt->bind_param("ss", $email, $defaultPassword);
    $stmt->execute();
    $stmt->close();

    $_SESSION['new_admin_email'] = $email;
}

header("Location: dashboard.php?new_admin=1");
exit;
?>
