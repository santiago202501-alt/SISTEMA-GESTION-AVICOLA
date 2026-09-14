<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$db = getDB();

$id = $_GET['id'] ?? null;

if ($id) {

    $stmt = $db->prepare("DELETE FROM galpones WHERE id = ?");
    $stmt->execute([$id]);

}

header('Location: index.php');
exit;