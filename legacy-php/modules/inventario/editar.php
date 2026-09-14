<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'inventario';

$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM inventario WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stmt = $db->prepare("
        UPDATE inventario
        SET
            tipo=?,
            nombre=?,
            descripcion=?,
            unidad_medida=?,
            stock_actual=?,
            stock_minimo=?,
            fecha_vencimiento=?,
            activo=?
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['tipo'],
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['unidad_medida'],
        $_POST['stock_actual'],
        $_POST['stock_minimo'],
        !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null,
        $_POST['activo'],
        $id
    ]);

    header("Location: index.php");
    exit;
}

include '../../includes/header.php';
?>

<div class="container">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Editar Producto</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Tipo</label>

<select name="tipo" class="form-control">

<option value="alimento" <?= $producto['tipo']=='alimento'?'selected':'' ?>>Alimento</option>

<option value="medicamento" <?= $producto['tipo']=='medicamento'?'selected':'' ?>>Medicamento</option>

<option value="insumo" <?= $producto['tipo']=='insumo'?'selected':'' ?>>Insumo</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Nombre</label>

<input type="text"
name="nombre"
class="form-control"
value="<?= htmlspecialchars($producto['nombre']) ?>"
required>

</div>

</div>

<div class="mb-3">

<label>Descripción</label>

<textarea
name="descripcion"
class="form-control"><?= htmlspecialchars($producto['descripcion']) ?></textarea>

</div>

<div class="row">

<div class="col-md-4">

<label>Unidad</label>

<input
type="text"
name="unidad_medida"
class="form-control"
value="<?= $producto['unidad_medida'] ?>">

</div>

<div class="col-md-4">

<label>Stock Actual</label>

<input
type="number"
step="0.01"
name="stock_actual"
class="form-control"
value="<?= $producto['stock_actual'] ?>">

</div>

<div class="col-md-4">

<label>Stock Mínimo</label>

<input
type="number"
step="0.01"
name="stock_minimo"
class="form-control"
value="<?= $producto['stock_minimo'] ?>">

</div>

</div>

<div class="row mt-3">

<div class="col-md-6">

<label>Fecha Vencimiento</label>

<input
type="date"
name="fecha_vencimiento"
class="form-control"
value="<?= $producto['fecha_vencimiento'] ?>">

</div>

<div class="col-md-6">

<label>Estado</label>

<select name="activo" class="form-control">

<option value="1" <?= $producto['activo']==1?'selected':'' ?>>Activo</option>

<option value="0" <?= $producto['activo']==0?'selected':'' ?>>Inactivo</option>

</select>

</div>

</div>

<br>

<button class="btn btn-warning">

Actualizar

</button>

<a href="index.php" class="btn btn-secondary">

Cancelar

</a>

</form>

</div>

</div>

</div>

<?php include '../../includes/footer.php'; ?>