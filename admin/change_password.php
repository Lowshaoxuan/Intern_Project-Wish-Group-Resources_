<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$current = $_POST['current_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    exit("New passwords do not match.");
}

$stmt = $conn->prepare("SELECT password FROM acc WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($hashed);
$stmt->fetch();
$stmt->close();

if (!password_verify($current, $hashed)) {
    exit("Incorrect current password.");
}

$newHash = password_hash($new, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE acc SET password = ?, password_changed = 1 WHERE id = ?");
$stmt->bind_param("si", $newHash, $_SESSION["user_id"]);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php");
exit;
