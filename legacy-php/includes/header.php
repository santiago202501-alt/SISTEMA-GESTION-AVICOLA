<?php
require_once __DIR__ . '/../config/session.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';
$rolUsuario = $_SESSION['rol'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGA Avícola</title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/siga.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="sidebar">

    <div class="sidebar-header">
        <h2><i class="fa-solid fa-egg"></i> SIGA</h2>
        <p>Sistema Avícola</p>
    </div>

    <ul class="sidebar-menu">

        <!-- Dashboard -->
        <li>
            <a href="<?= BASE_URL ?>/dashboard.php"
               class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Galpones -->
        <li>
            <a href="<?= BASE_URL ?>/modules/galpones/index.php"
               class="<?= ($activePage ?? '') === 'galpones' ? 'active' : '' ?>">
                <i class="fa-solid fa-warehouse"></i>
                <span>Galpones</span>
            </a>
        </li>

        <!-- Registro Agua -->
        <li>
            <a href="<?= BASE_URL ?>/modules/agua/index.php"
               class="<?= ($activePage ?? '') === 'agua' ? 'active' : '' ?>">
                <i class="fa-solid fa-droplet"></i>
                <span>Registro Agua</span>
            </a>
        </li>

        <!-- Registro Alimento -->
        <li>
            <a href="<?= BASE_URL ?>/modules/alimento/index.php"
               class="<?= ($activePage ?? '') === 'alimento' ? 'active' : '' ?>">
                <i class="fa-solid fa-wheat-awn"></i>
                <span>Registro Alimento</span>
            </a>
        </li>

        <!-- Amoniaco -->
        <li>
            <a href="<?= BASE_URL ?>/modules/amoniaco/index.php"
               class="<?= ($activePage ?? '') === 'amoniaco' ? 'active' : '' ?>">
                <i class="fa-solid fa-flask"></i>
                <span>Amoniaco</span>
            </a>
        </li>

        <!-- Mortalidad -->
        <li>
            <a href="<?= BASE_URL ?>/modules/mortalidad/index.php"
               class="<?= ($activePage ?? '') === 'mortalidad' ? 'active' : '' ?>">
                <i class="fa-solid fa-skull-crossbones"></i>
                <span>Mortalidad</span>
            </a>
        </li>

        <!-- Inventario -->
        <li>
            <a href="<?= BASE_URL ?>/modules/inventario/index.php"
               class="<?= ($activePage ?? '') === 'inventario' ? 'active' : '' ?>">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventario</span>
            </a>
        </li>

        <!-- Alertas -->
        <li>
            <a href="<?= BASE_URL ?>/modules/alertas/index.php"
               class="<?= ($activePage ?? '') === 'alertas' ? 'active' : '' ?>">
                <i class="fa-solid fa-bell"></i>
                <span>Alertas</span>
            </a>
        </li>

        <!-- Usuarios -->
        <li>
            <a href="<?= BASE_URL ?>/modules/usuarios/index.php"
               class="<?= ($activePage ?? '') === 'usuarios' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>
                <span>Usuarios</span>
            </a>
            <li>
    <a href="<?= BASE_URL ?>/modules/reportes/index.php"
       class="<?= ($activePage == 'reportes') ? 'active' : '' ?>">
        <i class="fas fa-file-alt"></i>
        Reportes
    </a>
</li>
        </li>

    </ul>

    <!-- Usuario -->
    <div class="sidebar-footer">

        <div class="user-info">
            <strong><?= htmlspecialchars($nombreUsuario) ?></strong>
            <small><?= htmlspecialchars($rolUsuario) ?></small>
        </div>

        <a href="<?= BASE_URL ?>/logout.php"
           class="btn-logout"
           title="Cerrar sesión">

            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Cerrar sesión</span>

        </a>

    </div>

</div>

<div class="main-content">
    
    <div class="topbar">

    <div class="topbar-left">

        <h4>

            <?= ucfirst($activePage ?? 'Dashboard') ?>

        </h4>

    </div>

    <div class="topbar-right">

        <a href="<?= BASE_URL ?>/modules/alertas/index.php" class="notification">

            <i class="fa-solid fa-bell"></i>

            <span class="notification-count">

                <?php

                $db = getDB();

                $stmt = $db->query("
                    SELECT COUNT(*)
                    FROM alertas
                    WHERE leida=0
                ");

                echo $stmt->fetchColumn();

                ?>

            </span>

        </a>

        <div class="user-top">

            <i class="fa-solid fa-circle-user fa-2x"></i>

            <div>

                <strong><?= htmlspecialchars($nombreUsuario) ?></strong>

                <br>

                <small><?= htmlspecialchars($rolUsuario) ?></small>

            </div>

        </div>

    </div>

</div>