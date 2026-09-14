<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'galpones';

$db = getDB();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM galpones WHERE id = ?");
$stmt->execute([$id]);
$galpon = $stmt->fetch();

if (!$galpon) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $db->prepare("
        UPDATE galpones SET
            codigo = :codigo,
            nombre = :nombre,
            ubicacion = :ubicacion,
            capacidad_maxima = :capacidad_maxima,
            cantidad_actual = :cantidad_actual,
            estado = :estado,
            responsable_id = :responsable_id
        WHERE id = :id
    ");

    $stmt->execute([
        ':codigo' => $_POST['codigo'],
        ':nombre' => $_POST['nombre'],
        ':ubicacion' => $_POST['ubicacion'],
        ':capacidad_maxima' => $_POST['capacidad_maxima'],
        ':cantidad_actual' => $_POST['cantidad_actual'],
        ':estado' => $_POST['estado'],
        ':responsable_id' => $_POST['responsable_id'],
        ':id' => $id
    ]);

    header('Location: index.php');
    exit;
}

$usuarios = $db->query("
    SELECT id, nombre_completo
    FROM usuarios
    WHERE activo = 1
")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <h1>Editar Galpón</h1>

    <form method="POST">

        <div class="mb-3">
            <label>Código</label>
            <input type="text"
                   name="codigo"
                   class="form-control"
                   value="<?= htmlspecialchars($galpon['codigo']) ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text"
                   name="nombre"
                   class="form-control"
                   value="<?= htmlspecialchars($galpon['nombre']) ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Ubicación</label>
            <input type="text"
                   name="ubicacion"
                   class="form-control"
                   value="<?= htmlspecialchars($galpon['ubicacion']) ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Capacidad Máxima</label>
            <input type="number"
                   name="capacidad_maxima"
                   class="form-control"
                   value="<?= $galpon['capacidad_maxima'] ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Cantidad Actual</label>
            <input type="number"
                   name="cantidad_actual"
                   class="form-control"
                   value="<?= $galpon['cantidad_actual'] ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Estado</label>

            <select name="estado" class="form-control">

                <option value="activo"
                    <?= $galpon['estado'] == 'activo' ? 'selected' : '' ?>>
                    Activo
                </option>

                <option value="inactivo"
                    <?= $galpon['estado'] == 'inactivo' ? 'selected' : '' ?>>
                    Inactivo
                </option>

                <option value="mantenimiento"
                    <?= $galpon['estado'] == 'mantenimiento' ? 'selected' : '' ?>>
                    Mantenimiento
                </option>

            </select>

        </div>

        <div class="mb-3">

            <label>Responsable</label>

            <select name="responsable_id" class="form-control">

                <?php foreach($usuarios as $u): ?>

                    <option value="<?= $u['id'] ?>"
                        <?= $u['id'] == $galpon['responsable_id'] ? 'selected' : '' ?>>

                        <?= htmlspecialchars($u['nombre_completo']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <button class="btn btn-warning">
            Actualizar
        </button>

        <a href="index.php" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

<?php include '../../includes/footer.php'; ?>