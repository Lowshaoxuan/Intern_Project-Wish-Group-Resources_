<?php
session_start();
require 'db.php'; // adjust if db.php is elsewhere

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_cv']) && is_array($_POST['selected_cv'])) {
    $ids = $_POST['selected_cv'];
    if (empty($ids)) {
        exit("No CVs selected.");
    }

    // Prepare statement
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt = $conn->prepare("SELECT filename FROM cv_submissions WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    // Set paths
    $zipFilename = 'cv_files_' . time() . '.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipFilename;
    $zip = new ZipArchive();

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $uploadDir = realpath(__DIR__ . '/../uploads/'); // make sure this is correct

        while ($row = $result->fetch_assoc()) {
            $filePath = $uploadDir . '/' . $row['filename'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath));
            }
        }

        $zip->close();

        if (file_exists($zipPath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFilename) . '"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);
            unlink($zipPath); // cleanup
            exit;
        } else {
            exit("ZIP file was not created.");
        }
    } else {
        exit("Failed to create ZIP.");
    }
} else {
    exit("Invalid request.");
}
