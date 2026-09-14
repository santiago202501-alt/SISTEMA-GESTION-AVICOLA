<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$usuario_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM usuarios
    WHERE id=?
");

$stmt->execute([$usuario_id]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$usuario){
    die("Usuario no encontrado.");
}

$errores=[];

if($_SERVER['REQUEST_METHOD']=='POST'){

    $nombre=trim($_POST['nombre_completo']);
    $email=trim($_POST['email']);
    $telefono=trim($_POST['telefono']);

    if($nombre==""){
        $errores[]="Debe ingresar el nombre.";
    }

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errores[]="Correo inválido.";
    }

    $stmt=$pdo->prepare("
        SELECT COUNT(*)
        FROM usuarios
        WHERE email=?
        AND id<>?
    ");

    $stmt->execute([$email,$usuario_id]);

    if($stmt->fetchColumn()>0){
        $errores[]="Ese correo ya pertenece a otro usuario.";
    }

    if(empty($errores)){

        $stmt=$pdo->prepare("
            UPDATE usuarios
            SET
            nombre_completo=?,
            email=?,
            telefono=?,
            updated_at=NOW()
            WHERE id=?
        ");

        $stmt->execute([
            $nombre,
            $email,
            $telefono,
            $usuario_id
        ]);

        $_SESSION['success']="Perfil actualizado correctamente.";

        header("Location:perfil.php");
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

<i class="fas fa-user-circle"></i>

Mi Perfil

</h4>

</div>

<div class="card-body">

<?php if(!empty($errores)): ?>

<div class="alert alert-danger">

<ul>

<?php foreach($errores as $e): ?>

<li><?= $e ?></li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label>Nombre Completo</label>

<input
type="text"
name="nombre_completo"
class="form-control"
required
value="<?= htmlspecialchars($usuario['nombre_completo']) ?>">

</div>

<div class="mb-3">

<label>Correo Electrónico</label>

<input
type="email"
name="email"
class="form-control"
required
value="<?= htmlspecialchars($usuario['email']) ?>">

</div>

<div class="mb-3">

<label>Teléfono</label>

<input
type="text"
name="telefono"
class="form-control"
value="<?= htmlspecialchars($usuario['telefono']) ?>">

</div>

<div class="d-flex justify-content-between">

<a href="../../dashboard.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Volver

</a>

<button
type="submit"
class="btn btn-primary">

<i class="fas fa-save"></i>

Guardar Cambios

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