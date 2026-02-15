<?php
session_start();
require "../config/database.php";

if($_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
}

$result = $conn->query("SELECT * FROM empresas ORDER BY id DESC");
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
<title>Empresas - TechAudit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3>Empresas</h3>
    <a href="crear_empresa.php" class="btn btn-primary mb-3">+ Nueva Empresa</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Sector</th>
                <th>Empleados</th>
                <th>Equipos</th>
                <th>Servidores</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['nombre'] ?></td>
                <td><?= $row['sector'] ?></td>
                <td><?= $row['empleados'] ?></td>
                <td><?= $row['equipos'] ?></td>
                <td><?= $row['servidores'] ?></td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
