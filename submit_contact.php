<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name     = $_POST['name'] ?? '';
  $email    = $_POST['email'] ?? '';
  $phone    = $_POST['phone'] ?? '';
  $category = $_POST['category'] ?? '';
  $other    = $_POST['other'] ?? '';

  // Optional: Basic validation
  if (empty($name) || empty($email) || empty($phone) || empty($category)) {
    echo "Please fill in all required fields.";
    exit;
  }

  // If "Other" is selected, use user input from the other field
  $finalCategory = ($category === 'Other') ? $other : $category;

  try {
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, category) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $finalCategory]);
    echo "Success! Your contact request has been submitted.";
  } catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
  }
} else {
  echo "Invalid request method.";
}
