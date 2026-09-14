<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$db = getDB();

$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM registros_alimento WHERE id=?");
$stmt->execute([$id]);

$registro = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $db->prepare("
        UPDATE registros_alimento SET
        galpon_id=?,
        fecha=?,
        kilogramos=?,
        usuario_id=?,
        observacion=?
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['galpon_id'],
        $_POST['fecha'],
        $_POST['kilogramos'],
        $_POST['usuario_id'],
        $_POST['observacion'],
        $id
    ]);

    header('Location:index.php');
    exit;
}

$galpones = $db->query("SELECT * FROM galpones")->fetchAll();
$usuarios = $db->query("SELECT * FROM usuarios WHERE activo=1")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">

<h1>Editar Registro</h1>

<form method="POST">

    <select name="galpon_id" class="form-control mb-3">

        <?php foreach($galpones as $g): ?>

            <option value="<?= $g['id'] ?>"
            <?= $g['id']==$registro['galpon_id'] ? 'selected' : '' ?>>

                <?= $g['nombre'] ?>

            </option>

        <?php endforeach; ?>

    </select>

    <input type="date"
           name="fecha"
           value="<?= $registro['fecha'] ?>"
           class="form-control mb-3">

    <input type="number"
           step="0.01"
           name="kilogramos"
           value="<?= $registro['kilogramos'] ?>"
           class="form-control mb-3">

    <select name="usuario_id" class="form-control mb-3">

        <?php foreach($usuarios as $u): ?>

            <option value="<?= $u['id'] ?>"
            <?= $u['id']==$registro['usuario_id'] ? 'selected' : '' ?>>

                <?= $u['nombre_completo'] ?>

            </option>

        <?php endforeach; ?>

    </select>

    <textarea name="observacion"
              class="form-control mb-3"><?= $registro['observacion'] ?></textarea>

    <button class="btn btn-warning">
        Actualizar
    </button>

</form>

</div>

<?php include '../../includes/footer.php'; ?>