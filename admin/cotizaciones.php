<?php
if(isset($_SESSION['mensaje'])){
    echo "<div class='alert alert-success'>".$_SESSION['mensaje']."</div>";
    unset($_SESSION['mensaje']);
}
?>

<?php if(isset($_GET['enviado'])): ?>
<div class="alert alert-success">La cotización ha sido enviada correctamente (simulado).</div>
<?php endif; ?>

<?php
require "../config/database.php";
include "layout/header.php";
include "layout/sidebar.php";

/* FILTRO */
$filtro = "";
if(isset($_GET['estado']) && $_GET['estado'] != ""){
    $estado = $_GET['estado'];
    $filtro = "WHERE cotizaciones.estado='$estado'";
}

/* CONSULTA */
$query = "
SELECT cotizaciones.*, empresas.nombre AS empresa
FROM cotizaciones
INNER JOIN auditorias ON cotizaciones.auditoria_id = auditorias.id
INNER JOIN empresas ON auditorias.empresa_id = empresas.id
$filtro
ORDER BY cotizaciones.id DESC
";

$result = $conn->query($query);

/* MÉTRICAS RÁPIDAS */
$total = $conn->query("SELECT COUNT(*) as t FROM cotizaciones")->fetch_assoc()['t'];
$aprobadas = $conn->query("SELECT COUNT(*) as t FROM cotizaciones WHERE estado='Aprobada'")->fetch_assoc()['t'];
$borrador = $conn->query("SELECT COUNT(*) as t FROM cotizaciones WHERE estado='Borrador'")->fetch_assoc()['t'];
?>
<h2 class="mb-4">Cotizaciones</h2>

<div class="row mb-4">

    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body text-center">
                <h6>Total</h6>
                <h3><?= $total ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white shadow">
            <div class="card-body text-center">
                <h6>Aprobadas</h6>
                <h3><?= $aprobadas ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-secondary text-white shadow">
            <div class="card-body text-center">
                <h6>Borrador</h6>
                <h3><?= $borrador ?></h3>
            </div>
        </div>
    </div>

</div>

<div class="mb-3">
    <a href="cotizaciones.php" class="btn btn-outline-dark btn-sm">Todas</a>
    <a href="?estado=Borrador" class="btn btn-outline-secondary btn-sm">Borrador</a>
    <a href="?estado=Aprobada" class="btn btn-outline-success btn-sm">Aprobadas</a>
    <a href="?estado=Rechazada" class="btn btn-outline-danger btn-sm">Rechazadas</a>
</div>
<div class="card shadow">
<div class="card-body">

<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Número</th>
            <th>Empresa</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>

    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><strong><?= $row['numero'] ?></strong></td>
            <td><?= $row['empresa'] ?></td>
            <td>RD$ <?= number_format($row['total'],2) ?></td>
            <td>
                <?php
                    if($row['estado'] == 'Aprobada'){
                        echo "<span class='badge bg-success'>Aprobada</span>";
                    } elseif($row['estado'] == 'Rechazada'){
                        echo "<span class='badge bg-danger'>Rechazada</span>";
                    } else {
                        echo "<span class='badge bg-secondary'>Borrador</span>";
                    }
                ?>
            </td>
            <td><?= $row['fecha'] ?></td>
            <td class="text-center">
    <a href="ver_cotizacion.php?id=<?= $row['id'] ?>" 
       class="btn btn-sm btn-outline-primary">
       Ver
    </a>

    <a href="generar_pdf.php?id=<?= $row['id'] ?>" target="_blank"
       class="btn btn-sm btn-outline-dark">
       PDF
    </a>

    <?php if($row['estado'] == 'Borrador' || $row['estado'] == 'Enviada'): ?>
        <!-- Botón para modal -->
        <button type="button" 
                class="btn btn-sm btn-outline-warning" 
                data-bs-toggle="modal" 
                data-bs-target="#enviarModal<?= $row['id'] ?>">
            <?= $row['estado'] == 'Borrador' ? 'Enviar' : 'Reenviar' ?>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="enviarModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Cotización <?= $row['numero'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                La cotización ha sido enviada al cliente.
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
    <?php endif; ?>
</td>


        </tr>
    <?php endwhile; ?>

    </tbody>
</table>

</div>
</div>
<?php include "layout/footer.php"; ?>
