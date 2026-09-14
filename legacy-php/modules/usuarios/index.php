<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$pdo = getDB();

$activePage = 'usuarios';

try {

    $sql = "SELECT
                u.id,
                u.nombre_completo,
                u.documento,
                u.email,
                u.telefono,
                u.activo,
                u.created_at,
                r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r
                ON u.rol_id = r.id
            ORDER BY u.nombre_completo ASC";

    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

    $usuariosActivos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE activo=1")->fetchColumn();

    $usuariosInactivos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE activo=0")->fetchColumn();

    $administradores = $pdo->query("
        SELECT COUNT(*)
        FROM usuarios u
        INNER JOIN roles r
            ON u.rol_id=r.id
        WHERE r.nombre='Administrador'
    ")->fetchColumn();

} catch(PDOException $e){

    die("Error: ".$e->getMessage());

}

include '../../includes/header.php';
?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="fas fa-users text-primary"></i>

Administración de Usuarios

</h2>

<p class="text-muted">

Gestione todos los usuarios del sistema

</p>

</div>

<a href="crear.php" class="btn btn-primary">

<i class="fas fa-user-plus"></i>

Nuevo Usuario

</a>

</div>

<div class="row mb-4">

<div class="col-md-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-users fa-2x text-primary mb-2"></i>

<h5>Total Usuarios</h5>

<h2><?= $totalUsuarios ?></h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-user-check fa-2x text-success mb-2"></i>

<h5>Activos</h5>

<h2><?= $usuariosActivos ?></h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-user-times fa-2x text-danger mb-2"></i>

<h5>Inactivos</h5>

<h2><?= $usuariosInactivos ?></h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-user-shield fa-2x text-warning mb-2"></i>

<h5>Administradores</h5>

<h2><?= $administradores ?></h2>

</div>

</div>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-dark text-white">

<div class="row">

<div class="col-md-6">

<h5 class="mb-0">

Lista de Usuarios

</h5>

</div>

<div class="col-md-6">

<input
type="text"
id="buscarUsuario"
class="form-control"
placeholder="Buscar usuario...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
class="table table-hover align-middle"
id="tablaUsuarios">

<thead class="table-light">

<tr>

<th>ID</th>

<th>Nombre</th>

<th>Documento</th>

<th>Email</th>

<th>Teléfono</th>

<th>Rol</th>

<th>Estado</th>

<th>Registro</th>

<th width="250">

Acciones

</th>

</tr>

</thead>

<tbody>

<?php foreach($usuarios as $u): ?>

<tr>

<td>

<?= $u['id'] ?>

</td>

<td>

<strong>

<?= htmlspecialchars($u['nombre_completo']) ?>

</strong>

</td>

<td>

<?= htmlspecialchars($u['documento']) ?>

</td>

<td>

<?= htmlspecialchars($u['email']) ?>

</td>

<td>

<?= htmlspecialchars($u['telefono']) ?>

</td>

<td>

<?php

$color='secondary';

if($u['rol']=='Administrador'){

$color='danger';

}elseif($u['rol']=='Supervisor'){

$color='primary';

}elseif($u['rol']=='Operario'){

$color='success';

}

?>

<span class="badge bg-<?= $color ?>">

<?= htmlspecialchars($u['rol']) ?>

</span>

</td>

<td>

<?php if($u['activo']): ?>

<span class="badge bg-success">

Activo

</span>

<?php else: ?>

<span class="badge bg-danger">

Inactivo

</span>

<?php endif; ?>

</td>

<td>

<?= date('d/m/Y',strtotime($u['created_at'])) ?>

</td>

<td>
    <a href="editar.php?id=<?= $u['id'] ?>"
class="btn btn-sm btn-warning"
title="Editar">

<i class="fas fa-edit"></i>

</a>

<a href="cambiar_password.php?id=<?= $u['id'] ?>"
class="btn btn-sm btn-info text-white"
title="Cambiar contraseña">

<i class="fas fa-key"></i>

</a>

<?php if($u['activo']): ?>

<a href="eliminar.php?id=<?= $u['id'] ?>&accion=desactivar"
class="btn btn-sm btn-danger"
onclick="return confirm('¿Desea desactivar este usuario?')"
title="Desactivar">

<i class="fas fa-user-slash"></i>

</a>

<?php else: ?>

<a href="eliminar.php?id=<?= $u['id'] ?>&accion=activar"
class="btn btn-sm btn-success"
onclick="return confirm('¿Desea activar este usuario?')"
title="Activar">

<i class="fas fa-user-check"></i>

</a>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script>

const buscar=document.getElementById('buscarUsuario');

buscar.addEventListener('keyup',function(){

let filtro=this.value.toLowerCase();

let filas=document.querySelectorAll('#tablaUsuarios tbody tr');

filas.forEach(function(fila){

let texto=fila.textContent.toLowerCase();

fila.style.display=texto.includes(filtro)?'':'none';

});

});

</script>

<?php include '../../includes/footer.php'; 
?>