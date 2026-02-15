<?php
session_start();
require '../config/database.php';

$id = (int)$_POST['id'];
$empresa_id = (int)$_POST['empresa_id'];
$tipo = $_POST['tipo'];
$nivel_complejidad = $_POST['nivel_complejidad'];
$riesgo_preliminar = $_POST['riesgo_preliminar'];
$estado = $_POST['estado'];

$stmt = $conn->prepare("UPDATE auditorias SET empresa_id=?, tipo=?, nivel_complejidad=?, riesgo_preliminar=?, estado=? WHERE id=?");
$stmt->bind_param("issssi", $empresa_id, $tipo, $nivel_complejidad, $riesgo_preliminar, $estado, $id);
$stmt->execute();

header("Location: auditorias.php");
exit;
