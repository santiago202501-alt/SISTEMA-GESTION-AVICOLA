<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$db=getDB();

$error='';

if($_SERVER['REQUEST_METHOD']=='POST'){

$stmt=$db->prepare("SELECT stock_actual FROM inventario WHERE id=?");
$stmt->execute([$_POST['inventario_id']]);
$producto=$stmt->fetch();

if($producto['stock_actual']<$_POST['cantidad']){

$error="No hay suficiente stock.";

}else{

$db->beginTransaction();

try{

$stmt=$db->prepare("
INSERT INTO movimientos_inventario
(inventario_id,tipo_movimiento,cantidad,motivo,usuario_id)
VALUES
(?,'salida',?,?,?)
");

$stmt->execute([

$_POST['inventario_id'],

$_POST['cantidad'],

$_POST['motivo'],

$_POST['usuario_id']

]);

$stmt=$db->prepare("
UPDATE inventario
SET stock_actual=stock_actual-?
WHERE id=?
");

$stmt->execute([

$_POST['cantidad'],

$_POST['inventario_id']

]);

$db->commit();

header("Location:index.php");

exit;

}catch(Exception $e){

$db->rollBack();

die($e->getMessage());

}

}

}

$productos=$db->query("SELECT * FROM inventario WHERE activo=1 ORDER BY nombre")->fetchAll();

$usuarios=$db->query("SELECT * FROM usuarios WHERE activo=1 ORDER BY nombre_completo")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

<h2>Registrar Salida</h2>

<?php if($error): ?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php endif; ?>

<form method="POST">

<label>Producto</label>

<select name="inventario_id" class="form-control mb-3">

<?php foreach($productos as $p): ?>

<option value="<?= $p['id'] ?>">

<?= $p['nombre'] ?>

(Stock: <?= $p['stock_actual'] ?>)

</option>

<?php endforeach; ?>

</select>

<label>Cantidad</label>

<input
type="number"
step="0.01"
name="cantidad"
class="form-control mb-3"
required>

<label>Motivo</label>

<input
type="text"
name="motivo"
class="form-control mb-3"
required>

<label>Usuario</label>

<select name="usuario_id" class="form-control mb-3">

<?php foreach($usuarios as $u): ?>

<option value="<?= $u['id'] ?>">

<?= $u['nombre_completo'] ?>

</option>

<?php endforeach; ?>

</select>

<button class="btn btn-danger">

Registrar Salida

</button>

<a href="index.php" class="btn btn-secondary">

Cancelar

</a>

</form>

</div>

<?php include '../../includes/footer.php'; 
?>