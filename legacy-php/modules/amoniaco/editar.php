<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'amoniaco';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $db->prepare("
        INSERT INTO registros_amoniaco
        (galpon_id, fecha, nivel_ppm, usuario_id, observacion)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['galpon_id'],
        $_POST['fecha'],
        $_POST['nivel_ppm'],
        $_POST['usuario_id'],
        $_POST['observacion']
    ]);

    header('Location: index.php');
    exit;
}

$galpones = $db->query("SELECT * FROM galpones ORDER BY nombre")->fetchAll();
$usuarios = $db->query("SELECT * FROM usuarios WHERE activo = 1 ORDER BY nombre_completo")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <h1 class="mb-4">Nuevo Registro de Amoniaco</h1>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Galpón</label>

            <select name="galpon_id" class="form-control" required>

                <option value="">Seleccione...</option>

                <?php foreach($galpones as $g): ?>

                    <option value="<?= $g['id']; ?>">
                        <?= htmlspecialchars($g['nombre']); ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="mb-3">

            <label class="form-label">Fecha</label>

            <input
                type="date"
                name="fecha"
                class="form-control"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Nivel de Amoniaco (PPM)</label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="nivel_ppm"
                class="form-control"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Usuario</label>

            <select name="usuario_id" class="form-control" required>

                <option value="">Seleccione...</option>

                <?php foreach($usuarios as $u): ?>

                    <option value="<?= $u['id']; ?>">
                        <?= htmlspecialchars($u['nombre_completo']); ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="mb-3">

            <label class="form-label">Observación</label>

            <textarea
                name="observacion"
                class="form-control"
                rows="4"></textarea>

        </div>

        <button type="submit" class="btn btn-primary">
            Guardar Registro
        </button>

        <a href="index.php" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

<?php include '../../includes/footer.php'; ?>