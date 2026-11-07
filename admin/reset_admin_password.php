<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Make sure only super_admin can reset
$stmt = $conn->prepare("SELECT role FROM acc WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'super_admin') {
    exit("Access denied.");
}

$admin_id = $_POST["id"];
$default = password_hash("Default@123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE acc SET password = ?, password_changed = 0 WHERE id = ?");
$stmt->bind_param("si", $default, $admin_id);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php");
exit;
