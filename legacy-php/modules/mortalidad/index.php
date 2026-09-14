<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'mortalidad';

$db = getDB();

$sql = "SELECT m.*,
               g.nombre AS galpon,
               u.nombre_completo AS usuario
        FROM mortalidad m
        INNER JOIN galpones g ON m.galpon_id = g.id
        INNER JOIN usuarios u ON m.usuario_id = u.id
        ORDER BY m.fecha DESC";

$registros = $db->query($sql)->fetchAll();

$total = $db->query("SELECT SUM(cantidad) total FROM mortalidad")->fetch();
$cantidadRegistros = count($registros);

include '../../includes/header.php';
?>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Módulo de Mortalidad</h2>
            <small class="text-muted">Control de aves muertas por galpón</small>
        </div>

        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Registro
        </a>

    </div>

    <div class="row mb-4">

        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6>Total de registros</h6>

                    <h2><?= $cantidadRegistros ?></h2>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card shadow-sm border-danger">

                <div class="card-body">

                    <h6>Total aves muertas</h6>

                    <h2 class="text-danger">

                        <?= $total['total'] ?? 0 ?>

                    </h2>

                </div>

            </div>

        </div>

    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">

            <tr>

                <th>Fecha</th>
                <th>Galpón</th>
                <th>Cantidad</th>
                <th>Causa</th>
                <th>Usuario</th>
                <th>Acciones</th>

            </tr>

        </thead>

        <tbody>

        <?php foreach($registros as $r): ?>

            <tr>

                <td><?= $r['fecha'] ?></td>

                <td><?= htmlspecialchars($r['galpon']) ?></td>

                <td>

                    <span class="badge bg-danger">

                        <?= $r['cantidad'] ?>

                    </span>

                </td>

                <td><?= htmlspecialchars($r['causa']) ?></td>

                <td><?= htmlspecialchars($r['usuario']) ?></td>

                <td>

                    <a href="editar.php?id=<?= $r['id'] ?>"
                       class="btn btn-warning btn-sm">

                        Editar

                    </a>

                    <a href="eliminar.php?id=<?= $r['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar este registro?')">

                        Eliminar

                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include '../../includes/footer.php'; ?>