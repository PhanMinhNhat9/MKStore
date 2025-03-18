<?php
require_once 'config.php';

$pdo = connectDatabase();
$stmt = $pdo->query("SELECT * FROM messages ORDER BY timestamp DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>