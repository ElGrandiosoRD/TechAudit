<?php
require '../config/database.php';
session_start();

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
    exit;
}

if(!isset($_GET['id']) || !isset($_GET['estado'])){
    die("Acción inválida.");
}

$id = (int)$_GET['id'];
$estado = $_GET['estado'];

// Validar estados permitidos
$estadosPermitidos = ['Borrador','Enviada','Aprobada','Rechazada'];
if(!in_array($estado, $estadosPermitidos)){
    die("Estado inválido.");
}

/* Actualizar cotización */
$conn->query("UPDATE cotizaciones SET estado='$estado' WHERE id=$id");

/* Obtener datos de la cotización y empresa */
$sql = "
SELECT c.numero, e.nombre AS empresa
FROM cotizaciones c
JOIN auditorias a ON c.auditoria_id = a.id
JOIN empresas e ON a.empresa_id = e.id
WHERE c.id=$id
";
$data = $conn->query($sql)->fetch_assoc();

/* Simulación de envío de correo */
$to = "cliente@empresa.com"; // Aquí podrías reemplazar con un campo de email real
$subject = "Cotización {$data['numero']} - TechAudit";
$message = "Estimado cliente,\n\nLe enviamos la cotización de auditoría para su empresa {$data['empresa']}.\n\nGracias.\nTechAudit";

// Para simular:
$_SESSION['mensaje'] = "Correo simulado enviado a $to para la cotización {$data['numero']}.";

// Si quieres enviar realmente:
// mail($to, $subject, $message);

header("Location: cotizaciones.php");
exit;
?>

