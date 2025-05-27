<?php
// Start output buffering to capture unintended output
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

// Enable error logging, disable display (for production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Include database configuration
include "config.php";

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

try {
    // Establish database connection
    $pdo = connectDatabase(); // Changed from $dpo to $pdo
} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get JSON data from the request
$rawData = file_get_contents('php://input');
if ($rawData === false) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No input data received']);
    exit;
}

// Decode JSON data
$data = json_decode($rawData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
}

// Extract fields
$ttgui = $data['ttgui'] ?? '';
$ttnhan = $data['ttnhan'] ?? '';
$noidung = $data['noidung'] ?? '';
$thoigian = date('Y-m-d H:i:s'); // Current timestamp

// Validate input
if (empty($ttgui) || empty($ttnhan) || empty($noidung)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Insert into database
try {
    $stmt = $pdo->prepare("INSERT INTO telegram (ttgui, ttnhan, noidung, thoigian) VALUES (?, ?, ?, ?)");
    $stmt->execute([$ttgui, $ttnhan, $noidung, $thoigian]);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Message saved successfully']);
} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save message: ' . $e->getMessage()]);
}

// Clean up output buffer
ob_end_flush();
?>