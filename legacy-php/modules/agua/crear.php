<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'agua';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $db->prepare("
        INSERT INTO registros_agua
        (galpon_id, fecha, litros, usuario_id, observacion)
        VALUES
        (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['galpon_id'],
        $_POST['fecha'],
        $_POST['litros'],
        $_POST['usuario_id'],
        $_POST['observacion']
    ]);

    header('Location: index.php');
    exit;
}

$galpones = $db->query("SELECT * FROM galpones")->fetchAll();
$usuarios = $db->query("SELECT * FROM usuarios WHERE activo = 1")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

<h1>Nuevo Registro de Agua</h1>

<form method="POST">

    <div class="mb-3">

        <label>Galpón</label>

        <select name="galpon_id" class="form-control">

            <?php foreach($galpones as $g): ?>

                <option value="<?= $g['id'] ?>">
                    <?= $g['nombre'] ?>
                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="mb-3">
        <label>Fecha</label>
        <input type="date" name="fecha"
               class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Litros</label>
        <input type="number" step="0.01"
               name="litros"
               class="form-control" required>
    </div>

    <div class="mb-3">

        <label>Usuario</label>

        <select name="usuario_id" class="form-control">

            <?php foreach($usuarios as $u): ?>

                <option value="<?= $u['id'] ?>">
                    <?= $u['nombre_completo'] ?>
                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="mb-3">

        <label>Observación</label>

        <textarea name="observacion"
                  class="form-control"></textarea>

    </div>

    <button class="btn btn-primary">
        Guardar
    </button>

</form>

</div>

<?php include '../../includes/footer.php'; ?>