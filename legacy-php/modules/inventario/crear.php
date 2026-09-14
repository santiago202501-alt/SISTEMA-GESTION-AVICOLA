<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../alertas/verificar_alertas.php';

requireLogin();

$activePage = 'inventario';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $tipo = $_POST['tipo'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $unidad_medida = $_POST['unidad_medida'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;

    $stmt = $db->prepare("
        INSERT INTO inventario
        (
            tipo,
            nombre,
            descripcion,
            unidad_medida,
            stock_actual,
            stock_minimo,
            fecha_vencimiento,
            activo
        )
        VALUES
        (
            ?,?,?,?,?,?,?,1
        )
    ");

    $stmt->execute([
        $tipo,
        $nombre,
        $descripcion,
        $unidad_medida,
        $stock_actual,
        $stock_minimo,
        $fecha_vencimiento
    ]);

    // ALERTA POR STOCK BAJO
    if ($stock_actual <= $stock_minimo) {

        crearAlerta(
            $db,
            1,
            'alimento',
            'El producto "' . $nombre . '" tiene el stock por debajo del mínimo.',
            'advertencia'
        );

    }

    // ALERTA POR PRODUCTO VENCIDO
    if (!empty($fecha_vencimiento)) {

        if (strtotime($fecha_vencimiento) <= strtotime(date('Y-m-d'))) {

            crearAlerta(
                $db,
                1,
                'alimento',
                'El producto "' . $nombre . '" se encuentra vencido.',
                'critica'
            );

        }

    }

    header("Location: index.php");
    exit;

}

include '../../includes/header.php';
?>

<div class="container">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>Nuevo Producto</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Tipo</label>

<select name="tipo" class="form-control" required>

<option value="">Seleccione...</option>

<option value="alimento">Alimento</option>

<option value="medicamento">Medicamento</option>

<option value="insumo">Insumo</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Nombre del producto</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

</div>

<div class="mb-3">

<label>Descripción</label>

<textarea
name="descripcion"
class="form-control"
rows="3"></textarea>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label>Unidad de medida</label>

<input
type="text"
name="unidad_medida"
class="form-control"
placeholder="kg, litros, frascos..."
required>

</div>

<div class="col-md-4 mb-3">

<label>Stock Inicial</label>

<input
type="number"
step="0.01"
name="stock_actual"
class="form-control"
required>

</div>

<div class="col-md-4 mb-3">

<label>Stock mínimo</label>

<input
type="number"
step="0.01"
name="stock_minimo"
class="form-control"
required>

</div>

</div>

<div class="mb-3">

<label>Fecha de vencimiento</label>

<input
type="date"
name="fecha_vencimiento"
class="form-control">

</div>

<button class="btn btn-success">

<i class="fas fa-save"></i>

Guardar Producto

</button>

<a href="index.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Cancelar

</a>

</form>

</div>

</div>

</div>

<?php include '../../includes/footer.php'; ?>