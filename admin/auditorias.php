<?php
require "../config/database.php";
include "layout/header.php";
include "layout/sidebar.php";

/* CONSULTA */
$query = "
SELECT auditorias.*, empresas.nombre AS empresa 
FROM auditorias
INNER JOIN empresas ON auditorias.empresa_id = empresas.id
ORDER BY auditorias.id DESC
";

$result = $conn->query($query);
?>

<h3>Auditorías</h3>

<a href="crear_auditoria.php" class="btn btn-primary mb-3">
    + Nueva Auditoría
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Tipo</th>
            <th>Complejidad</th>
            <th>Riesgo</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['empresa'] ?></td>
            <td><?= $row['tipo'] ?></td>
            <td><?= $row['nivel_complejidad'] ?></td>
            <td><?= $row['riesgo_preliminar'] ?></td>
            <td><?= $row['estado'] ?></td>
            <td>
                <a href="editar_auditoria.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
            </td>
            <td>
    <a href="generar_cotizacion.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Generar Cotización</a>
</td>

        </tr>
    <?php endwhile; ?>
    </tbody>
</table>


<?php include "layout/footer.php"; ?>
