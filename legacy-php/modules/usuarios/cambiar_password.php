<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();
$pdo = getDB();
if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT id,nombre_completo FROM usuarios WHERE id=?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener mínimo 6 caracteres.";
    }

    if ($password != $confirmar) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET password_hash=?,
                updated_at=NOW()
            WHERE id=?
        ");

        $stmt->execute([$hash, $id]);

        $_SESSION['success'] = "Contraseña actualizada correctamente.";

        header("Location:index.php");
        exit;
    }
}

include '../../includes/header.php';
?>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-info text-white">

<h4>

<i class="fas fa-key"></i>

Cambiar Contraseña

</h4>

</div>

<div class="card-body">

<p>

<strong>Usuario:</strong>

<?= htmlspecialchars($usuario['nombre_completo']) ?>

</p>

<?php if(!empty($errores)): ?>

<div class="alert alert-danger">

<ul class="mb-0">

<?php foreach($errores as $e): ?>

<li><?= $e ?></li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label>Nueva Contraseña</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Confirmar Contraseña</label>

<input
type="password"
name="confirmar"
class="form-control"
required>

</div>

<div class="d-flex justify-content-between">

<a href="index.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Volver

</a>

<button type="submit" class="btn btn-info text-white">

<i class="fas fa-save"></i>

Guardar Contraseña

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include '../../includes/footer.php'; 
?>