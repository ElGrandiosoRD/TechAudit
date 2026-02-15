<?php
session_start();
require '../config/database.php';

$id = (int)$_GET['id'];

/* Obtener datos de la cotización */
$sql = "
SELECT 
    c.*, 
    a.tipo, 
    a.nivel_complejidad, 
    e.nombre AS empresa
FROM cotizaciones c
JOIN auditorias a ON c.auditoria_id = a.id
JOIN empresas e ON a.empresa_id = e.id
WHERE c.id = $id
";

$result = $conn->query($sql);
$cotizacion = $result->fetch_assoc();

if(!$cotizacion){
    die("Cotización no encontrada.");
}

/* Obtener servicios de la cotización */
$servicios = $conn->query("
    SELECT *
    FROM cotizacion_detalle
    WHERE cotizacion_id = $id
");
?>
<?php include "layout/header.php"; ?>
<?php include "layout/sidebar.php"; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Cotización #<?php echo $cotizacion['numero']; ?></h2>
        <div>
            <a href="editar_cotizacion.php?id=<?php echo $id; ?>" class="btn btn-warning">Editar</a>
            <a href="generar_pdf.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-success">Generar PDF</a>
       <div class="mt-3">
   
        <span class="badge bg-info">Estado actual: <?php echo $cotizacion['estado']; ?></span>
    
</div>

        </div>
    </div>

    <hr>

    <div class="card mb-3">
        <div class="card-header">Información General</div>
        <div class="card-body">
            <p><strong>Empresa:</strong> <?php echo $cotizacion['empresa']; ?></p>
            <p><strong>Tipo Auditoría:</strong> <?php echo $cotizacion['tipo']; ?></p>
            <p><strong>Nivel Complejidad:</strong> <?php echo $cotizacion['nivel_complejidad']; ?></p>
            <p><strong>Fecha:</strong> <?php echo $cotizacion['fecha']; ?></p>
            <p><strong>Estado:</strong> <?php echo $cotizacion['estado']; ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Servicios Incluidos</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Servicio</th>
                        <th>Descripción</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($s = $servicios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $s['servicio']; ?></td>
                        <td><?php echo $s['descripcion']; ?></td>
                        <td>RD$ <?php echo number_format($s['precio_unitario'],2); ?></td>
                        <td><?php echo $s['cantidad']; ?></td>
                        <td>RD$ <?php echo number_format($s['total'],2); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Resumen Financiero</div>
        <div class="card-body">
            <p><strong>Subtotal:</strong> RD$ <?php echo number_format($cotizacion['subtotal'],2); ?></p>
            <p><strong>Imprevistos:</strong> RD$ <?php echo number_format($cotizacion['imprevistos'],2); ?></p>
            <p><strong>Impuesto:</strong> RD$ <?php echo number_format($cotizacion['impuesto'],2); ?></p>
            <h4>Total: RD$ <?php echo number_format($cotizacion['total'],2); ?></h4>
        </div>
    </div>
</div>

<?php include "layout/footer.php"; ?>
