<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$db = getDB();

$id = $_GET['id'];

$stmt = $db->prepare("DELETE FROM registros_alimento WHERE id=?");
$stmt->execute([$id]);

header('Location:index.php');
exit;