<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();
$pdo = getDB();
$activePage = 'usuarios';

if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

$stmt = $pdo->query("SELECT * FROM roles ORDER BY nombre");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = trim($_POST['nombre_completo']);
    $documento = trim($_POST['documento']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $rol = $_POST['rol_id'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre == "") {
        $errores[] = "Ingrese el nombre.";
    }

    if ($documento == "") {
        $errores[] = "Ingrese el documento.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM usuarios
        WHERE documento=?
        AND id<>?
    ");

    $stmt->execute([$documento,$id]);

    if($stmt->fetchColumn()>0){
        $errores[]="El documento ya existe.";
    }

    $stmt=$pdo->prepare("
        SELECT COUNT(*)
        FROM usuarios
        WHERE email=?
        AND id<>?
    ");

    $stmt->execute([$email,$id]);

    if($stmt->fetchColumn()>0){
        $errores[]="El correo ya existe.";
    }

    if(empty($errores)){

        $stmt=$pdo->prepare("
            UPDATE usuarios
            SET
            nombre_completo=?,
            documento=?,
            email=?,
            telefono=?,
            rol_id=?,
            activo=?
            WHERE id=?
        ");

        $stmt->execute([
            $nombre,
            $documento,
            $email,
            $telefono,
            $rol,
            $activo,
            $id
        ]);

        $_SESSION['success']="Usuario actualizado correctamente.";

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

<div class="card-header bg-warning">

<h4>

<i class="fas fa-user-edit"></i>

Editar Usuario

</h4>

</div>

<div class="card-body">

<?php if($errores): ?>

<div class="alert alert-danger">

<ul>

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
required
value="<?= htmlspecialchars($usuario['nombre_completo']) ?>">

</div>

<div class="col-md-6">

<label>Documento</label>

<input
type="text"
name="documento"
class="form-control"
required
value="<?= htmlspecialchars($usuario['documento']) ?>">

</div>

</div>

<div class="row mt-3">

<div class="col-md-6">

<label>Correo</label>

<input
type="email"
name="email"
class="form-control"
required
value="<?= htmlspecialchars($usuario['email']) ?>">

</div>

<div class="col-md-6">

<label>Teléfono</label>

<input
type="text"
name="telefono"
class="form-control"
value="<?= htmlspecialchars($usuario['telefono']) ?>">
</div>

</div>

<div class="row mt-3">

    <div class="col-md-6">

        <label>Rol</label>

        <select
            name="rol_id"
            class="form-select"
            required>

            <?php foreach($roles as $r): ?>

                <option
                    value="<?= $r['id'] ?>"
                    <?= ($usuario['rol_id']==$r['id']) ? 'selected' : '' ?>>

                    <?= htmlspecialchars($r['nombre']) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-md-6">

        <label>Estado</label>

        <div class="form-check mt-2">

            <input
                class="form-check-input"
                type="checkbox"
                id="activo"
                name="activo"
                <?= $usuario['activo'] ? 'checked' : '' ?>>

            <label class="form-check-label" for="activo">

                Usuario Activo

            </label>

        </div>

    </div>

</div>

<hr>

<div class="d-flex justify-content-between">

    <a
        href="index.php"
        class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Volver

    </a>

    <button
        type="submit"
        class="btn btn-warning">

        <i class="fas fa-save"></i>

        Actualizar Usuario

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