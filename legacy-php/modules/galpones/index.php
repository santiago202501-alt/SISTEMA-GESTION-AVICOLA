<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'galpones';

$db = getDB();

$sql = "SELECT g.*, u.nombre_completo AS responsable
        FROM galpones g
        LEFT JOIN usuarios u
        ON g.responsable_id = u.id
        ORDER BY g.id DESC";

$galpones = $db->query($sql)->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <div class="page-header">
        <h1>Gestión de Galpones</h1>

        <a href="crear.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Nuevo Galpón
        </a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Capacidad</th>
            <th>Cantidad Actual</th>
            <th>Estado</th>
            <th>Responsable</th>
            <th>Acciones</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach($galpones as $g): ?>

        <tr>

            <td><?= htmlspecialchars($g['codigo']) ?></td>

            <td><?= htmlspecialchars($g['nombre']) ?></td>

            <td><?= htmlspecialchars($g['ubicacion']) ?></td>

            <td><?= $g['capacidad_maxima'] ?></td>

            <td><?= $g['cantidad_actual'] ?></td>

            <td><?= ucfirst($g['estado']) ?></td>

            <td><?= htmlspecialchars($g['responsable']) ?></td>

            <td>

                <a href="editar.php?id=<?= $g['id'] ?>"
                   class="btn btn-warning">

                    Editar

                </a>

                <a href="eliminar.php?id=<?= $g['id'] ?>"
                   class="btn btn-danger"
                   onclick="return confirm('¿Eliminar galpón?')">

                    Eliminar

                </a>

            </td>

        </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include '../../includes/footer.php'; 
?>