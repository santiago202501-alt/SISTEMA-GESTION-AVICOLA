<?php

/**
 * Función para crear una alerta.
 * Evita crear alertas repetidas que aún no han sido leídas.
 */

function crearAlerta($pdo, $galpon_id, $tipo, $mensaje, $nivel = 'advertencia')
{

    // Verificar si ya existe una alerta igual pendiente
    $sql = "SELECT id
            FROM alertas
            WHERE galpon_id = ?
            AND tipo = ?
            AND mensaje = ?
            AND leida = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $galpon_id,
        $tipo,
        $mensaje
    ]);

    if ($stmt->fetch()) {
        return;
    }

    // Crear la alerta
    $sql = "INSERT INTO alertas
            (
                galpon_id,
                tipo,
                mensaje,
                nivel
            )
            VALUES
            (
                ?,?,?,?
            )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $galpon_id,
        $tipo,
        $mensaje,
        $nivel
    ]);
}