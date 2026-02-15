<?php
session_start();
require "../config/database.php";

if($_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
}

$empresas = $conn->query("SELECT * FROM empresas");
?>
<?php
require "../config/database.php";
include "layout/header.php";
include "layout/sidebar.php";
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Nueva Auditoría</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3>Nueva Auditoría</h3>

    <form action="guardar_auditoria.php" method="POST">

        <label>Empresa</label>
        <select name="empresa_id" class="form-control mb-2" required>
            <option value="">Seleccionar</option>
            <?php while($e = $empresas->fetch_assoc()): ?>
                <option value="<?= $e['id'] ?>">
                    <?= $e['nombre'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Tipo de Auditoría</label>
        <select name="tipo" class="form-control mb-2">
            <option>Infraestructura</option>
            <option>Seguridad</option>
            <option>Integral</option>
        </select>

        <label>Riesgo Preliminar</label>
        <select name="riesgo_preliminar" class="form-control mb-3">
            <option>Bajo</option>
            <option>Medio</option>
            <option>Alto</option>
        </select>

        <button class="btn btn-success">Guardar</button>
        <a href="auditorias.php" class="btn btn-secondary">Volver</a>

    </form>
</div>

</body>
</html>
