<?php
session_start();
require '../config/database.php';

/* Seguridad */
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
    exit;
}

if(!isset($_GET['id'])){
    die("Cotización no válida");
}

$cotizacion_id = (int)$_GET['id'];

/* Obtener cotización y empresa */
$sql = "
SELECT c.*, a.nivel_complejidad, e.nombre AS empresa, e.email AS email_empresa
FROM cotizaciones c
JOIN auditorias a ON c.auditoria_id = a.id
JOIN empresas e ON a.empresa_id = e.id
WHERE c.id = $cotizacion_id
";
$cotizacion = $conn->query($sql)->fetch_assoc();

if(!$cotizacion){
    die("Cotización no encontrada.");
}

/* Cambiar estado a Enviada si está en Borrador */
if($cotizacion['estado'] != 'Borrador'){
    die("Solo se pueden enviar cotizaciones en estado Borrador.");
}

$conn->query("UPDATE cotizaciones SET estado='Enviada', fecha=NOW() WHERE id=$cotizacion_id");

/* -----------------------------
   Simular envío de correo
------------------------------ */

// Aquí puedes usar PHPMailer o mail() si lo quieres real
$empresa_email = $cotizacion['email_empresa'] ?? "empresa@example.com"; // Email de prueba
$asunto = "Cotización #".$cotizacion['numero']." enviada por TechAudit";
$mensaje = "Estimado cliente,\n\nSe ha enviado su cotización con número ".$cotizacion['numero'].".\nTotal: RD$ ".number_format($cotizacion['total'],2)."\n\nGracias por confiar en TechAudit.\n\nAtentamente,\nTechAudit";

// Simulación: Guardar en log
file_put_contents("../logs/correos.log", "[".date('Y-m-d H:i:s')."] Enviado a $empresa_email: $mensaje\n", FILE_APPEND);

// Si quieres enviar correo real, descomenta esta línea y configura XAMPP con sendmail
// mail($empresa_email, $asunto, $mensaje);

/* Redirigir con mensaje */
header("Location: cotizaciones.php?enviado=1");
exit;
