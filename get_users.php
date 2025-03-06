<?php
require_once 'config.php'; 

header('Content-Type: application/json');
$iduser = isset($_GET['id']) ? intval($_GET['id']) : 0;
$users = getAllUsers($iduser); 

echo json_encode($users); 
?>
