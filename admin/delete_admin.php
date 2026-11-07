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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $admin_id = $_POST["id"];

    // Prevent deleting self or super admins
    $stmt = $conn->prepare("SELECT role FROM acc WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_role);
    $stmt->fetch();
    $stmt->close();

    if ($admin_role === 'admin') {
        $stmt = $conn->prepare("DELETE FROM acc WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: dashboard.php");
exit;
?>
