<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();
$pdo = getDB();
if (!isset($_GET['id']) || !isset($_GET['accion'])) {
    header("Location:index.php");
    exit;
}

$id = (int) $_GET['id'];
$accion = $_GET['accion'];

// Evitar que el usuario actual se desactive a sí mismo
if ($id == $_SESSION['user']['id']) {
    $_SESSION['error'] = "No puedes desactivar tu propio usuario.";
    header("Location:index.php");
    exit;
}

// Verificar que exista el usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    $_SESSION['error'] = "Usuario no encontrado.";
    header("Location:index.php");
    exit;
}

if ($accion == "activar") {

    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET activo=1,
            updated_at=NOW()
        WHERE id=?
    ");

    $stmt->execute([$id]);

    $_SESSION['success'] = "Usuario activado correctamente.";

} elseif ($accion == "desactivar") {

    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET activo=0,
            updated_at=NOW()
        WHERE id=?
    ");

    $stmt->execute([$id]);

    $_SESSION['success'] = "Usuario desactivado correctamente.";

}

header("Location:index.php");
exit;