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

$empresa_id = $_POST['empresa_id'];
$tipo = $_POST['tipo'];
$riesgo = $_POST['riesgo_preliminar'];

/* Obtener datos técnicos de la empresa */
$stmt = $conn->prepare("SELECT empleados,equipos,servidores FROM empresas WHERE id = ?");
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

/* Calcular complejidad automáticamente */
$complejidad = "Baja";

if($empresa['empleados'] > 50 || $empresa['equipos'] > 40){
    $complejidad = "Media";
}

if($empresa['empleados'] > 150 || $empresa['servidores'] > 5){
    $complejidad = "Alta";
}

/* Insertar auditoría */
$stmt = $conn->prepare("
INSERT INTO auditorias (empresa_id,tipo,nivel_complejidad,riesgo_preliminar)
VALUES (?,?,?,?)
");

$stmt->bind_param("isss", $empresa_id,$tipo,$complejidad,$riesgo);
$stmt->execute();

header("Location: auditorias.php");
?>
