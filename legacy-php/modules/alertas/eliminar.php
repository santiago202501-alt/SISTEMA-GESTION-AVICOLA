<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    DELETE FROM alertas
    WHERE id = ?
");

$stmt->execute([$id]);

$_SESSION['success'] = "Alerta eliminada correctamente.";

header("Location:index.php");
exit;