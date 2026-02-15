<?php
require "../config/database.php";
include "layout/header.php";
include "layout/sidebar.php";
?>
<?php
session_start();
require "../config/database.php";

if($_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
}

$stmt = $conn->prepare("INSERT INTO empresas 
(nombre,sector,empleados,equipos,servidores,valor_infraestructura,telefono,email,direccion)
VALUES (?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    "ssiiidsss",
    $_POST['nombre'],
    $_POST['sector'],
    $_POST['empleados'],
    $_POST['equipos'],
    $_POST['servidores'],
    $_POST['valor_infraestructura'],
    $_POST['telefono'],
    $_POST['email'],
    $_POST['direccion']
);

$stmt->execute();

header("Location: empresas.php");
?>
