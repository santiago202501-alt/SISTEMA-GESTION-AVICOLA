<?php
/**
 * SIGA - Registro de usuarios
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$errors  = [];
$success = '';
$data    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de seguridad inválido.';
    } else {
        $data['nombre_completo'] = trim($_POST['nombre_completo'] ?? '');
        $data['documento']       = trim($_POST['documento'] ?? '');
        $data['email']           = trim($_POST['email'] ?? '');
        $data['telefono']        = trim($_POST['telefono'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (strlen($data['nombre_completo']) < 3) $errors[] = 'Nombre demasiado corto.';
        if (!preg_match('/^\d{6,15}$/', $data['documento'])) $errors[] = 'Documento inválido.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo inválido.';
        if (strlen($password) < 8) $errors[] = 'La contraseña debe tener mínimo 8 caracteres.';
        if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

        if (empty($errors)) {
            $db   = getDB();
            $stmt = $db->prepare("SELECT id FROM usuarios WHERE email=:e OR documento=:d LIMIT 1");
            $stmt->execute([':e' => $data['email'], ':d' => $data['documento']]);
            if ($stmt->fetch()) {
                $errors[] = 'El correo o documento ya está registrado.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $ins  = $db->prepare(
                    "INSERT INTO usuarios (nombre_completo,documento,email,telefono,password_hash,rol_id)
                     VALUES (:n,:d,:e,:t,:p,2)"
                );
                $ins->execute([
                    ':n' => $data['nombre_completo'],
                    ':d' => $data['documento'],
                    ':e' => $data['email'],
                    ':t' => $data['telefono'],
                    ':p' => $hash,
                ]);
                $success = 'Cuenta creada correctamente. Ahora puedes iniciar sesión.';
                $data = [];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SIGA — Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/siga.css" rel="stylesheet">
</head>
<body>
<div class="login-page">
  <div class="login-card" style="max-width:500px">
    <div class="login-logo">
      <div class="logo-icon"><i class="fa-solid fa-egg"></i></div>
      <h1>SIGA</h1>
      <p>Crear nueva cuenta</p>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-check"></i><?= e($success) ?>
      </div>
      <a href="<?= BASE_URL ?>/index.php" class="btn-siga-primary w-100 text-center d-block py-2">
        <i class="fa-solid fa-right-to-bracket me-2"></i>Ir al login
      </a>
    <?php else: ?>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><i class="fa-solid fa-triangle-exclamation me-1"></i><?= e($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="nombre_completo" class="form-control"
                   value="<?= e($data['nombre_completo'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Documento (CC)</label>
            <input type="text" name="documento" class="form-control"
                   value="<?= e($data['documento'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="tel" name="telefono" class="form-control"
                   value="<?= e($data['telefono'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control"
                   value="<?= e($data['email'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required minlength="8">
          </div>
          <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password2" class="form-control" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-siga-success w-100 py-2">
              <i class="fa-solid fa-user-plus me-2"></i>Registrarse
            </button>
          </div>
        </div>
      </form>
      <div class="text-center mt-3">
        <a href="<?= BASE_URL ?>/index.php" style="font-size:.83rem;color:#64748b">
          ← Volver al login
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
