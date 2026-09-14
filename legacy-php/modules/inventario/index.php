<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();
$activePage = 'inventario';
$db = getDB();

$sql = "SELECT * FROM inventario ORDER BY nombre ASC";
$productos = $db->query($sql)->fetchAll();

$totalProductos = $db->query("SELECT COUNT(*) FROM inventario WHERE activo = 1")->fetchColumn();

$stockBajo = $db->query("SELECT COUNT(*) FROM inventario WHERE stock_actual <= stock_minimo AND activo = 1")->fetchColumn();

include '../../includes/header.php';
?>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Inventario</h2>

            <small class="text-muted">
                Administración de productos
            </small>

        </div>

        <a href="crear.php" class="btn btn-primary">

            <i class="fas fa-plus"></i>

            Nuevo Producto

        </a>

    </div>

    <div class="row mb-4">

        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6>Total Productos</h6>

                    <h2><?= $totalProductos ?></h2>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card shadow-sm border-danger">

                <div class="card-body">

                    <h6>Stock Bajo</h6>

                    <h2 class="text-danger">

                        <?= $stockBajo ?>

                    </h2>

                </div>

            </div>

        </div>

    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">

            <tr>

                <th>Tipo</th>
                <th>Producto</th>
                <th>Unidad</th>
                <th>Stock</th>
                <th>Stock Mínimo</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th>Acciones</th>

            </tr>

        </thead>

        <tbody>

        <?php foreach($productos as $p): ?>

            <tr>

                <td><?= ucfirst($p['tipo']) ?></td>

                <td><?= htmlspecialchars($p['nombre']) ?></td>

                <td><?= htmlspecialchars($p['unidad_medida']) ?></td>

                <td>

                    <?php if($p['stock_actual'] <= $p['stock_minimo']): ?>

                        <span class="badge bg-danger">

                            <?= $p['stock_actual'] ?>

                        </span>

                    <?php else: ?>

                        <span class="badge bg-success">

                            <?= $p['stock_actual'] ?>

                        </span>

                    <?php endif; ?>

                </td>

                <td><?= $p['stock_minimo'] ?></td>

                <td>

                    <?= $p['fecha_vencimiento'] ?: '-' ?>

                </td>

                <td>

                    <?php if($p['activo']): ?>

                        <span class="badge bg-success">

                            Activo

                        </span>

                    <?php else: ?>

                        <span class="badge bg-secondary">

                            Inactivo

                        </span>

                    <?php endif; ?>

                </td>

                <td>

                    <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">

                        Editar

                    </a>

                    <a href="eliminar.php?id=<?= $p['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Desea eliminar este producto?')">

                        Eliminar

                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include '../../includes/footer.php'; ?>