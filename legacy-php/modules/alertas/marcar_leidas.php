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
    UPDATE alertas
    SET leida = 1
    WHERE id = ?
");

$stmt->execute([$id]);

$_SESSION['success'] = "Alerta marcada como leída.";

header("Location:index.php");
exit;