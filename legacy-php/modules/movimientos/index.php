<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'movimientos';

$db = getDB();

$sql = "SELECT
m.*,
i.nombre AS producto,
u.nombre_completo AS usuario

FROM movimientos_inventario m

INNER JOIN inventario i
ON m.inventario_id=i.id

INNER JOIN usuarios u
ON m.usuario_id=u.id

ORDER BY m.created_at DESC";

$movimientos = $db->query($sql)->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Movimientos de Inventario</h2>

<div>

<a href="entrada.php" class="btn btn-success">

Entrada

</a>

<a href="salida.php" class="btn btn-danger">

Salida

</a>

</div>

</div>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Producto</th>

<th>Movimiento</th>

<th>Cantidad</th>

<th>Motivo</th>

<th>Usuario</th>

<th>Fecha</th>

</tr>

</thead>

<tbody>

<?php foreach($movimientos as $m): ?>

<tr>

<td><?= htmlspecialchars($m['producto']) ?></td>

<td>

<?php if($m['tipo_movimiento']=="entrada"): ?>

<span class="badge bg-success">

Entrada

</span>

<?php else: ?>

<span class="badge bg-danger">

Salida

</span>

<?php endif; ?>

</td>

<td><?= $m['cantidad'] ?></td>

<td><?= htmlspecialchars($m['motivo']) ?></td>

<td><?= htmlspecialchars($m['usuario']) ?></td>

<td><?= $m['created_at'] ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php include '../../includes/footer.php'; ?>