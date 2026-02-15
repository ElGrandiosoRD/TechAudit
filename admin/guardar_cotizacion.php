<?php
require "../config/database.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $auditoria_id = (int)$_POST['auditoria_id'];
    $auditoria = $conn->query("
    SELECT nivel_complejidad 
    FROM auditorias 
    WHERE id = $auditoria_id
")->fetch_assoc();

$nivel_complejidad = strtolower($auditoria['nivel_complejidad']);

/* Definir multiplicador */

switch($nivel_complejidad){
    case 'baja':
        $multiplicador = 1;
        break;
    case 'media':
        $multiplicador = 1.5;
        break;
    case 'alta':
        $multiplicador = 2;
        break;
    default:
        $multiplicador = 1;
}

    $numero = $_POST['numero'];
    $fecha = date("Y-m-d");

    /* =========================
       INSERTAR COTIZACION BASE
    ========================== */

    $stmt = $conn->prepare("
        INSERT INTO cotizaciones 
        (auditoria_id, numero, fecha, subtotal, imprevistos, impuesto, total)
        VALUES (?, ?, ?, 0, 0, 0, 0)
    ");

    $stmt->bind_param("iss", $auditoria_id, $numero, $fecha);
    $stmt->execute();

    $cotizacion_id = $conn->insert_id;

    $subtotal = 0;

    /* =========================
       GUARDAR SERVICIOS
    ========================== */

    if(isset($_POST['servicios'])){

        foreach($_POST['servicios'] as $servicio_id){

            $servicio_id = (int)$servicio_id;
            $cantidad = (int)$_POST['cantidad_'.$servicio_id];

            $servicio = $conn->query("SELECT * FROM servicios WHERE id=$servicio_id")->fetch_assoc();

            $precio = $servicio['precio_base'] * $multiplicador;

            $total_servicio = $precio * $cantidad;

            $subtotal += $total_servicio;

            $stmtDetalle = $conn->prepare("
                INSERT INTO cotizacion_detalle
                (cotizacion_id, servicio, descripcion, cantidad, precio_unitario, total)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmtDetalle->bind_param(
                "issidd",
                $cotizacion_id,
                $servicio['nombre'],
                $servicio['descripcion'],
                $cantidad,
                $precio,
                $total_servicio
            );

            $stmtDetalle->execute();
        }
    }

    /* =========================
       CALCULOS AUTOMATICOS
    ========================== */

    $imprevistos = $subtotal * 0.05;
    $impuesto = ($subtotal + $imprevistos) * 0.18;
    $total_final = $subtotal + $imprevistos + $impuesto;

    $conn->query("
        UPDATE cotizaciones SET
        subtotal = $subtotal,
        imprevistos = $imprevistos,
        impuesto = $impuesto,
        total = $total_final
        WHERE id = $cotizacion_id
    ");

    header("Location: ver_cotizacion.php?id=".$cotizacion_id);
    exit;
}
