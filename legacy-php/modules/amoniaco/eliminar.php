<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$db = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {

    $stmt = $db->prepare("DELETE FROM registros_amoniaco WHERE id = ?");
    $stmt->execute([$id]);

}

header("Location: index.php");
exit;