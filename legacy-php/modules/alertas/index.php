<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$pdo = getDB();

$activePage = 'alertas';

$sql = "
SELECT
    a.*,
    g.nombre AS galpon
FROM alertas a
INNER JOIN galpones g
    ON a.galpon_id = g.id
ORDER BY
    a.leida ASC,
    a.created_at DESC
";

$stmt = $pdo->query($sql);
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>

        <i class="fas fa-bell text-warning"></i>

        Sistema de Alertas

    </h2>

</div>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Galpón</th>

<th>Tipo</th>

<th>Mensaje</th>

<th>Nivel</th>

<th>Estado</th>

<th>Fecha</th>

<th width="150">Acciones</th>

</tr>

</thead>

<tbody>

<?php foreach($alertas as $a): ?>

<tr>

<td><?= $a['id'] ?></td>

<td>

<?= htmlspecialchars($a['galpon']) ?>

</td>

<td>

<?php

switch($a['tipo']){

    case 'amoniaco':

        echo '<span class="badge bg-danger">Amoniaco</span>';

    break;

    case 'agua':

        echo '<span class="badge bg-primary">Agua</span>';

    break;

    case 'alimento':

        echo '<span class="badge bg-success">Alimento</span>';

    break;

    case 'sobrepoblacion':

        echo '<span class="badge bg-warning text-dark">Sobrepoblación</span>';

    break;

    default:

        echo '<span class="badge bg-secondary">'.$a['tipo'].'</span>';

}

?>

</td>

<td>

<?= htmlspecialchars($a['mensaje']) ?>

</td>

<td>
    <?php

switch($a['nivel']){

    case 'info':

        echo '<span class="badge bg-info">Información</span>';

    break;

    case 'advertencia':

        echo '<span class="badge bg-warning text-dark">Advertencia</span>';

    break;

    case 'critica':

        echo '<span class="badge bg-danger">Crítica</span>';

    break;

    default:

        echo '<span class="badge bg-secondary">'.$a['nivel'].'</span>';

}

?>

</td>

<td>

<?php if($a['leida']==0){ ?>

<span class="badge bg-danger">

Pendiente

</span>

<?php }else{ ?>

<span class="badge bg-success">

Leída

</span>

<?php } ?>

</td>

<td>

<?= date('d/m/Y H:i',strtotime($a['created_at'])) ?>

</td>

<td>

<?php if($a['leida']==0){ ?>

<a
href="marcar_leidas.php?id=<?= $a['id'] ?>"
class="btn btn-success btn-sm"
title="Marcar como leída">

<i class="fas fa-check"></i>

</a>

<?php } ?>

<a
href="eliminar.php?id=<?= $a['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Desea eliminar esta alerta?')"
title="Eliminar">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php endforeach; ?>

<?php if(count($alertas)==0){ ?>

<tr>

<td colspan="8" class="text-center">

No existen alertas registradas.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php include '../../includes/footer.php'; ?>