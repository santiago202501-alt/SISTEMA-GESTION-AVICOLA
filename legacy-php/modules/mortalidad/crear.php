<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'mortalidad';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stmt = $db->prepare("
        INSERT INTO mortalidad
        (galpon_id, fecha, cantidad, causa, usuario_id, observacion)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['galpon_id'],
        $_POST['fecha'],
        $_POST['cantidad'],
        $_POST['causa'],
        $_POST['usuario_id'],
        $_POST['observacion']
    ]);

    header("Location: index.php");
    exit;
}

$galpones = $db->query("SELECT * FROM galpones ORDER BY nombre")->fetchAll();
$usuarios = $db->query("SELECT * FROM usuarios WHERE activo = 1 ORDER BY nombre_completo")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <div class="card shadow">

        <div class="card-header bg-danger text-white">

            <h3>Registrar Mortalidad</h3>

        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Galpón</label>

                        <select name="galpon_id" class="form-control" required>

                            <option value="">Seleccione...</option>

                            <?php foreach($galpones as $g): ?>

                                <option value="<?= $g['id']; ?>">

                                    <?= htmlspecialchars($g['nombre']); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Fecha</label>

                        <input
                            type="date"
                            name="fecha"
                            class="form-control"
                            value="<?= date('Y-m-d'); ?>"
                            required>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Cantidad de aves</label>

                        <input
                            type="number"
                            name="cantidad"
                            min="1"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Causa</label>

                        <input
                            type="text"
                            name="causa"
                            class="form-control"
                            placeholder="Ej. Enfermedad, Asfixia..."
                            required>

                    </div>

                </div>

                <div class="mb-3">

                    <label>Usuario</label>

                    <select
                        name="usuario_id"
                        class="form-control"
                        required>

                        <?php foreach($usuarios as $u): ?>

                            <option value="<?= $u['id']; ?>">

                                <?= htmlspecialchars($u['nombre_completo']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label>Observación</label>

                    <textarea
                        name="observacion"
                        class="form-control"
                        rows="4"></textarea>

                </div>

                <button class="btn btn-success">

                    Guardar Registro

                </button>

                <a href="index.php" class="btn btn-secondary">

                    Cancelar

                </a>

            </form>

        </div>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>