<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'galpones';

$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $db->prepare("
        INSERT INTO galpones
        (
            codigo,
            nombre,
            ubicacion,
            capacidad_maxima,
            cantidad_actual,
            estado,
            responsable_id
        )
        VALUES
        (
            :codigo,
            :nombre,
            :ubicacion,
            :capacidad_maxima,
            :cantidad_actual,
            :estado,
            :responsable_id
        )
    ");

    $stmt->execute([

        ':codigo' => $_POST['codigo'],
        ':nombre' => $_POST['nombre'],
        ':ubicacion' => $_POST['ubicacion'],
        ':capacidad_maxima' => $_POST['capacidad_maxima'],
        ':cantidad_actual' => $_POST['cantidad_actual'],
        ':estado' => $_POST['estado'],
        ':responsable_id' => $_POST['responsable_id']

    ]);

    header('Location: index.php');
    exit;
}

$usuarios = $db->query("
    SELECT id, nombre_completo
    FROM usuarios
    WHERE activo = 1
    ORDER BY nombre_completo
")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

    <h1>Crear Nuevo Galpón</h1>

    <form method="POST">

        <div class="mb-3">
            <label>Código</label>
            <input type="text" name="codigo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Ubicación</label>
            <input type="text" name="ubicacion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Capacidad Máxima</label>
            <input type="number" name="capacidad_maxima" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Cantidad Actual</label>
            <input type="number" name="cantidad_actual" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Estado</label>

            <select name="estado" class="form-control">

                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
                <option value="mantenimiento">Mantenimiento</option>

            </select>

        </div>

        <div class="mb-3">

            <label>Responsable</label>

            <select name="responsable_id" class="form-control">

                <?php foreach($usuarios as $u): ?>

                    <option value="<?= $u['id'] ?>">
                        <?= htmlspecialchars($u['nombre_completo']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <button class="btn btn-primary">
            Guardar Galpón
        </button>

        <a href="index.php" class="btn btn-secondary">
            Volver
        </a>

    </form>

</div>

<?php include '../../includes/footer.php'; 
?>