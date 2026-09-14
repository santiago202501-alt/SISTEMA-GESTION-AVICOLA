<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'agua';

$db = getDB();

$sql = "SELECT ra.*,
               g.nombre AS galpon,
               u.nombre_completo AS usuario
        FROM registros_agua ra
        LEFT JOIN galpones g ON ra.galpon_id = g.id
        LEFT JOIN usuarios u ON ra.usuario_id = u.id
        ORDER BY ra.fecha DESC";

$registros = $db->query($sql)->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Registro de Agua</h1>

        <a href="crear.php" class="btn btn-primary">
            Nuevo Registro
        </a>
    </div>

    <table class="table">

        <thead>
        <tr>
            <th>Fecha</th>
            <th>Galpón</th>
            <th>Litros</th>
            <th>Usuario</th>
            <th>Observación</th>
            <th>Acciones</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach($registros as $r): ?>

            <tr>

                <td><?= $r['fecha'] ?></td>

                <td><?= htmlspecialchars($r['galpon']) ?></td>

                <td><?= $r['litros'] ?> L</td>

                <td><?= htmlspecialchars($r['usuario']) ?></td>

                <td><?= htmlspecialchars($r['observacion']) ?></td>

                <td>

                    <a href="editar.php?id=<?= $r['id'] ?>"
                       class="btn btn-warning">

                        Editar

                    </a>

                    <a href="eliminar.php?id=<?= $r['id'] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('¿Eliminar registro?')">

                        Eliminar

                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include '../../includes/footer.php'; ?>