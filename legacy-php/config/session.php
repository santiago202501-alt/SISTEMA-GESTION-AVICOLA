<?php
/**
 * SIGA - Control de Sesiones y Autenticación
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si hay sesión activa; redirige al login si no.
 */
function requireLogin(): void {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Verifica que el usuario tenga rol de administrador.
 */
function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['rol'] !== 'administrador') {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}

/**
 * Retorna true si el usuario está logueado.
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['usuario_id']);
}

/**
 * Retorna true si el usuario es administrador.
 */
function isAdmin(): bool {
    return ($_SESSION['rol'] ?? '') === 'administrador';
}

/**
 * Inicia sesión con email y contraseña.
 * Retorna el array del usuario o false.
 */
function login(string $email, string $password): array|false {
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT u.*, r.nombre AS rol FROM usuarios u
         JOIN roles r ON r.id = u.rol_id
         WHERE u.email = :email AND u.activo = 1 LIMIT 1"
    );
    $stmt->execute([':email' => trim($email)]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['usuario_id']      = $user['id'];
        $_SESSION['nombre']          = $user['nombre_completo'];
        $_SESSION['email']           = $user['email'];
        $_SESSION['rol']             = $user['rol'];
        $_SESSION['last_activity']   = time();
        return $user;
    }
    return false;
}

/**
 * Cierra la sesión actual.
 */
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

/**
 * Sanitiza un string para salida HTML.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Genera un token CSRF y lo guarda en sesión.
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida el token CSRF enviado en el formulario.
 */
function verifyCsrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
