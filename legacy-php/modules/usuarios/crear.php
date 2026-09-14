<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();
$pdo = getDB();
$activePage = 'usuarios';

$errores = [];

$stmt = $pdo->query("SELECT * FROM roles ORDER BY nombre");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = trim($_POST['nombre_completo']);
    $documento = trim($_POST['documento']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];
    $rol = $_POST['rol_id'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre == '') {
        $errores[] = "Debe ingresar el nombre.";
    }

    if ($documento == '') {
        $errores[] = "Debe ingresar el documento.";
    }

    if ($email == '') {
        $errores[] = "Debe ingresar el correo.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    }

    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener mínimo 6 caracteres.";
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE documento=?");
    $stmt->execute([$documento]);

    if ($stmt->fetchColumn() > 0) {
        $errores[] = "El documento ya existe.";
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email=?");
    $stmt->execute([$email]);

    if ($stmt->fetchColumn() > 0) {
        $errores[] = "El correo ya está registrado.";
    }

    if (empty($errores)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios
            (
                nombre_completo,
                documento,
                email,
                telefono,
                password_hash,
                rol_id,
                activo
            )
            VALUES
            (?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $nombre,
            $documento,
            $email,
            $telefono,
            $hash,
            $rol,
            $activo
        ]);

        $_SESSION['success'] = "Usuario creado correctamente.";

        header("Location:index.php");
        exit;

    }

}

include '../../includes/header.php';
?>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-user-plus"></i>

Nuevo Usuario

</h4>

</div>

<div class="card-body">

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

<div class="row">

<div class="col-md-6">

<label>Nombre Completo</label>

<input
type="text"
name="nombre_completo"
class="form-control"
required>

</div>

<div class="col-md-6">

<label>Documento</label>

<input
type="text"
name="documento"
class="form-control"
required>

</div>

</div>

<div class="row mt-3">

<div class="col-md-6">

<label>Correo</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="col-md-6">

<label>Teléfono</label>

<input
type="text"
name="telefono"
class="form-control">

</div>

</div>

<div class="row mt-3">

<div class="col-md-6">

<label>Contraseña</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="col-md-6">

<label>Rol</label>

<select
name="rol_id"
class="form-select"
required>

<?php foreach($roles as $r): ?>

<option value="<?= $r['id'] ?>">

<?= $r['nombre'] ?>

</option>

<?php endforeach; ?>

</select>

</div>

</div>
<div class="row mt-3">

    <div class="col-md-6">

        <label class="form-label">Estado</label>

        <div class="form-check">

            <input
                class="form-check-input"
                type="checkbox"
                name="activo"
                id="activo"
                checked>

            <label class="form-check-label" for="activo">

                Usuario Activo

            </label>

        </div>

    </div>

</div>

<hr>

<div class="d-flex justify-content-between">

    <a href="index.php" class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Volver

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="fas fa-save"></i>

        Guardar Usuario

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