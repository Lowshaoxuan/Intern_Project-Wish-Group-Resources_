<?php
require 'db.php';

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=contacts.csv");
echo "\xEF\xBB\xBF"; // UTF-8 BOM

$output = fopen("php://output", "w");
fputcsv($output, ['Name', 'Email', 'Phone', 'Category', 'Message', 'Time']);

$query = "SELECT name, email, phone, category, message, created_at FROM contacts WHERE 1=1";

if (!empty($_GET['from_date'])) {
    $query .= " AND created_at >= '" . $conn->real_escape_string($_GET['from_date']) . "'";
}
if (!empty($_GET['to_date'])) {
    $query .= " AND created_at <= '" . $conn->real_escape_string($_GET['to_date']) . " 23:59:59'";
}
if (!empty($_GET['category'])) {
    $query .= " AND category = '" . $conn->real_escape_string($_GET['category']) . "'";
}
$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $row['phone'] = '="' . $row['phone'] . '"'; // preserve leading zero
    $row['message'] = str_replace(["\r", "\n"], ' ', $row['message']); // clean message
    fputcsv($output, [
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['category'],
        $row['message'],
        $row['created_at']
    ]);
}

fclose($output);
exit;
