<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name    = $_POST['name'] ?? '';
  $email   = $_POST['email'] ?? '';
  $phone   = $_POST['phone'] ?? '';
  $message = $_POST['message'] ?? '';
  $cv      = $_FILES['cv'];

  $allowedTypes = ['pdf', 'doc', 'docx'];
  $fileExt = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));

  if (!in_array($fileExt, $allowedTypes)) {
    die("Invalid file type.");
  }

  // Upload file to temp directory
  $uploadDir = 'uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $uniqueFilename = uniqid('cv_') . '.' . $fileExt;
  $uploadPath = $uploadDir . $uniqueFilename;

  if (!move_uploaded_file($cv['tmp_name'], $uploadPath)) {
    die("Error uploading file.");
  }

  // Send email using Mailjet SMTP
  $mail = new PHPMailer(true);

  try {
    // SMTP config for Mailjet
    $mail->isSMTP();
    $mail->Host       = 'in-v3.mailjet.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '0021672e239f33418c014abff2c765c2';    // Replace with your actual API key
    $mail->Password   = '55455e7cb2b37a8ef6c368b430f2438b'; // Replace with your actual secret key
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Email details
    $mail->setFrom('shaoxuan828@gmail.com', 'CV Submission'); // Use a verified sender if required
    $mail->addAddress('fsdfsdfsadf4@gmail.com');

    $mail->addAttachment($uploadPath, $cv['name']); // Keep original filename
    $mail->isHTML(true);
    $mail->Subject = "New CV Submission from $name";
    $mail->Body    = "
            <h3>New CV Submission</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Message:</strong> $message</p>
        ";

    $mail->send();
    echo "Success! Your CV has been submitted and emailed.";

    // Optional: delete uploaded file
    // unlink($uploadPath);

  } catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
} else {
  echo "Invalid request method.";
}
