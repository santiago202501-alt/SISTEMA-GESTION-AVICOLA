<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'amoniaco';

$db = getDB();

$sql = "SELECT ra.*,
               g.nombre AS galpon,
               u.nombre_completo AS usuario
        FROM registros_amoniaco ra
        LEFT JOIN galpones g ON ra.galpon_id = g.id
        LEFT JOIN usuarios u ON ra.usuario_id = u.id
        ORDER BY ra.fecha DESC";

$registros = $db->query($sql)->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1>Registro de Amoniaco</h1>

        <a href="crear.php" class="btn btn-primary">
            Nuevo Registro
        </a>
    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">
            <tr>
                <th>Fecha</th>
                <th>Galpón</th>
                <th>Nivel (PPM)</th>
                <th>Usuario</th>
                <th>Observación</th>
                <th width="180">Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php if(count($registros)>0): ?>

            <?php foreach($registros as $r): ?>

                <tr>

                    <td><?= htmlspecialchars($r['fecha']) ?></td>

                    <td><?= htmlspecialchars($r['galpon']) ?></td>

                    <td><?= htmlspecialchars($r['nivel_ppm']) ?> ppm</td>

                    <td><?= htmlspecialchars($r['usuario']) ?></td>

                    <td><?= htmlspecialchars($r['observacion']) ?></td>

                    <td>

                        <a href="editar.php?id=<?= $r['id'] ?>"
                           class="btn btn-warning btn-sm">
                           Editar
                        </a>

                        <a href="eliminar.php?id=<?= $r['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Desea eliminar este registro?');">
                           Eliminar
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6" class="text-center">
                    No existen registros.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<?php include '../../includes/footer.php'; ?>