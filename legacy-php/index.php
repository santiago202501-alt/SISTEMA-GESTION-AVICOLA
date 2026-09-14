<?php
/**
 * SIGA - Página de Login
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

// Si ya hay sesión, redirigir
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido. Recarga la página.';
    } else {
        $email    = filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos.';
        } else {
            $user = login($email, $password);
            if ($user) {
                header('Location: ' . BASE_URL . '/dashboard.php');
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGA — Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/siga.css" rel="stylesheet">
</head>
<body>
<div class="login-page">
  <div class="login-card">

    <!-- Logo -->
    <div class="login-logo">
      <div class="logo-icon"><i class="fa-solid fa-egg"></i></div>
      <h1>SIGA</h1>
      <p>Sistema Integral de Gestión Avícola</p>
    </div>

    <!-- Error -->
    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= e($error) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="fa-regular fa-envelope text-muted small"></i>
          </span>
          <input type="email" id="email" name="email" class="form-control border-start-0 ps-0"
                 placeholder="correo@ejemplo.com"
                 value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="fa-solid fa-lock text-muted small"></i>
          </span>
          <input type="password" id="password" name="password"
                 class="form-control border-start-0 ps-0"
                 placeholder="••••••••" required>
          <button type="button" class="input-group-text bg-white border-start-0"
                  onclick="togglePass()" title="Mostrar/Ocultar">
            <i class="fa-regular fa-eye text-muted small" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-siga-primary w-100 py-2 mb-3">
        <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar al sistema
      </button>

      <div class="text-center">
        <a href="<?= BASE_URL ?>/register.php"
           class="text-muted" style="font-size:.83rem">
          ¿No tienes cuenta? <strong>Regístrate</strong>
        </a>
      </div>
    </form>

    <!-- Credenciales demo -->
    <div class="mt-4 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
      <p class="mb-1" style="font-size:.75rem;font-weight:600;color:#64748b;">ACCESOS DE PRUEBA</p>
      <div style="font-size:.78rem;color:#334155;">
        <div><i class="fa-solid fa-shield-halved text-primary me-1"></i>
          <strong>Admin:</strong> admin@siga.com / Admin123!</div>
        <div class="mt-1"><i class="fa-solid fa-user-gear text-success me-1"></i>
          <strong>Operario:</strong> operario@siga.com / Admin123!</div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
  const inp = document.getElementById('password');
  const ico = document.getElementById('eyeIcon');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.classList.replace('fa-eye','fa-eye-slash');
  } else {
    inp.type = 'password';
    ico.classList.replace('fa-eye-slash','fa-eye');
  }
}
</script>
</body>
</html>
