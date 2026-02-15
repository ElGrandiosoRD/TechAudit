<?php
session_start();
require "../config/database.php";

$usuario_id = $_SESSION['usuario_id'];

$query = "
SELECT cotizaciones.*, empresas.nombre AS empresa
FROM cotizaciones
INNER JOIN auditorias ON cotizaciones.auditoria_id = auditorias.id
INNER JOIN empresas ON auditorias.empresa_id = empresas.id
WHERE empresas.usuario_id = $usuario_id
";

$result = $conn->query($query);
?>

