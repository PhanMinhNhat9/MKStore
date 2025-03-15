<?php
require_once '../config.php';
$pdo = connectDatabase();

$idmgg = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>