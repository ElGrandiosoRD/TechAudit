<?php
require "../config/database.php";
include "layout/header.php";
include "layout/sidebar.php";

/* Seguridad */
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
    exit;
}

if(!isset($_GET['id'])){
    echo "<div class='alert alert-danger'>Auditoría no válida</div>";
    exit;
}

$auditoria_id = (int)$_GET['id'];

/* Verificar si ya existe cotización */
$verificar = $conn->query("SELECT id FROM cotizaciones WHERE auditoria_id = $auditoria_id");

if($verificar->num_rows > 0){
    echo "<div class='alert alert-warning'>
            Esta auditoría ya tiene una cotización registrada.
          </div>";
    include "layout/footer.php";
    exit;
}

/* Obtener datos auditoría */
$query = "
SELECT auditorias.*, empresas.nombre
FROM auditorias
INNER JOIN empresas ON auditorias.empresa_id = empresas.id
WHERE auditorias.id = $auditoria_id
";

$result = $conn->query($query);

if($result->num_rows == 0){
    echo "<div class='alert alert-danger'>Auditoría no encontrada</div>";
    exit;
}

$data = $result->fetch_assoc();

/* ==========================
   CÁLCULO PROFESIONAL
========================== */

/* Base por tipo */
switch($data['tipo']){
    case 'Infraestructura':
        $base = 60000;
        break;
    case 'Seguridad':
        $base = 80000;
        break;
    case 'Integral':
        $base = 120000;
        break;
    default:
        $base = 50000;
}

/* Multiplicador por complejidad */
$factorComplejidad = 1;

switch($data['nivel_complejidad']){
    case 'Media':
        $factorComplejidad = 1.3;
        break;
    case 'Alta':
        $factorComplejidad = 1.6;
        break;
}

/* Ajuste por riesgo */
$factorRiesgo = 1;

switch($data['riesgo_preliminar']){
    case 'Medio':
        $factorRiesgo = 1.10;
        break;
    case 'Alto':
        $factorRiesgo = 1.20;
        break;
}

$subtotal = $base * $factorComplejidad * $factorRiesgo;

$imprevistos = $subtotal * 0.05;
$impuesto = ($subtotal + $imprevistos) * 0.18;
$total = $subtotal + $imprevistos + $impuesto;

/* ==========================
   NUMERACIÓN AUTOMÁTICA
========================== */

$last = $conn->query("SELECT numero FROM cotizaciones ORDER BY id DESC LIMIT 1");

$nextNumber = 1;

if($last->num_rows > 0){
    $row = $last->fetch_assoc();
    $lastNumber = intval(substr($row['numero'], 4));
    $nextNumber = $lastNumber + 1;
}

$numero = "COT-" . str_pad($nextNumber, 5, "0", STR_PAD_LEFT);
?>

<div class="container mt-4">

<div class="card shadow">
<div class="card-body">

<h3 class="mb-4">Vista Previa Cotización <?= $numero ?></h3>

<p><strong>Empresa:</strong> <?= $data['nombre'] ?></p>

<table class="table table-bordered">

<tr>
    <td>Tipo Auditoría</td>
    <td><?= $data['tipo'] ?></td>
</tr>

<tr>
    <td>Complejidad</td>
    <td><?= $data['nivel_complejidad'] ?></td>
</tr>

<tr>
    <td>Riesgo Preliminar</td>
    <td><?= $data['riesgo_preliminar'] ?></td>
</tr>

<tr>
    <td>Base</td>
    <td class="text-end">RD$ <?= number_format($base,2) ?></td>
</tr>

<tr>
    <td>Subtotal Ajustado</td>
    <td class="text-end">RD$ <?= number_format($subtotal,2) ?></td>
</tr>

<tr>
    <td>Imprevistos (5%)</td>
    <td class="text-end">RD$ <?= number_format($imprevistos,2) ?></td>
</tr>

<tr>
    <td>ITBIS (18%)</td>
    <td class="text-end">RD$ <?= number_format($impuesto,2) ?></td>
</tr>

<tr class="table-success">
    <td><strong>Total</strong></td>
    <td class="text-end"><strong>RD$ <?= number_format($total,2) ?></strong></td>
</tr>

</table>

<form action="guardar_cotizacion.php" method="POST">

    <input type="hidden" name="auditoria_id" value="<?= $auditoria_id ?>">
    <input type="hidden" name="numero" value="<?= $numero ?>">
    <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
    <input type="hidden" name="imprevistos" value="<?= $imprevistos ?>">
    <input type="hidden" name="impuesto" value="<?= $impuesto ?>">
    <input type="hidden" name="total" value="<?= $total ?>">

    <h4>Servicios Disponibles</h4>

<?php
$servicios = $conn->query("SELECT * FROM servicios");
while($serv = $servicios->fetch_assoc()):
?>

<div style="border:1px solid #ccc; padding:10px; margin-bottom:5px;">
    <input type="checkbox" name="servicios[]" value="<?= $serv['id'] ?>">
    <strong><?= $serv['nombre'] ?></strong><br>
    <?= $serv['descripcion'] ?><br>
    Precio Base: RD$ <?= number_format($serv['precio_base'],2) ?><br>

    Cantidad:
    <input type="number" name="cantidad_<?= $serv['id'] ?>" min="1" value="1">
</div>

<?php endwhile; ?>

    <button class="btn btn-success">
        Confirmar y Guardar
    </button>

    <a href="auditorias.php" class="btn btn-secondary">
        Cancelar
    </a>

</form>

</div>
</div>

</div>

<?php include "layout/footer.php"; ?>
