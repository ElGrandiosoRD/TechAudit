<?php
session_start();
require '../config/database.php';

/* Seguridad */
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: auditorias.php");
    exit;
}

$cotizacion_id = (int)$_POST['cotizacion_id'];

/* Actualizar servicios existentes */
if(isset($_POST['detalle_id']) && count($_POST['detalle_id']) > 0){
    foreach($_POST['detalle_id'] as $index => $detalle_id){
        $precio = floatval($_POST['precio'][$index]);
        $cantidad = intval($_POST['cantidad'][$index]);
        $total = $precio * $cantidad;

        $stmt = $conn->prepare("UPDATE cotizacion_detalle SET precio_unitario=?, cantidad=?, total=? WHERE id=?");
        $stmt->bind_param("dddi", $precio, $cantidad, $total, $detalle_id);
        $stmt->execute();
        $stmt->close();
    }
}

/* Agregar nuevos servicios */
if(isset($_POST['servicios_nuevos'])){
    foreach($_POST['servicios_nuevos'] as $serv_id){
        $serv_id = intval($serv_id);
        $cantidad_field = 'cantidad_new_'.$serv_id;
        $cantidad = isset($_POST[$cantidad_field]) ? intval($_POST[$cantidad_field]) : 1;

        /* Obtener datos del servicio */
        $serv = $conn->query("SELECT nombre, descripcion, precio_base FROM servicios WHERE id=$serv_id")->fetch_assoc();
        $total = $serv['precio_base'] * $cantidad;

        $stmt = $conn->prepare("INSERT INTO cotizacion_detalle (cotizacion_id, servicio, descripcion, cantidad, precio_unitario, total, created_at) VALUES (?,?,?,?,?,?,NOW())");
        $stmt->bind_param("issidd", $cotizacion_id, $serv['nombre'], $serv['descripcion'], $cantidad, $serv['precio_base'], $total);
        $stmt->execute();
        $stmt->close();
    }
}

/* Recalcular totales */
$totales = $conn->query("SELECT SUM(total) AS subtotal FROM cotizacion_detalle WHERE cotizacion_id=$cotizacion_id")->fetch_assoc();
$subtotal = floatval($totales['subtotal']);
$imprevistos = $subtotal * 0.10;
$impuesto = $subtotal * 0.18;
$total = $subtotal + $imprevistos + $impuesto;

/* Actualizar cotización */
$estado = $_POST['estado'];
$stmt = $conn->prepare("UPDATE cotizaciones SET subtotal=?, imprevistos=?, impuesto=?, total=?, estado=? WHERE id=?");
$stmt->bind_param("ddddsi", $subtotal, $imprevistos, $impuesto, $total, $estado, $cotizacion_id);
$stmt->execute();
$stmt->close();

header("Location: ver_cotizacion.php?id=$cotizacion_id&success=1");
exit;
