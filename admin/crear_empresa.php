<?php
session_start();
if($_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
}
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
<title>Nueva Empresa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3>Nueva Empresa</h3>

    <form action="guardar_empresa.php" method="POST">

        <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre empresa" required>
        <input type="text" name="sector" class="form-control mb-2" placeholder="Sector">
        <input type="number" name="empleados" class="form-control mb-2" placeholder="Cantidad empleados">
        <input type="number" name="equipos" class="form-control mb-2" placeholder="Cantidad equipos">
        <input type="number" name="servidores" class="form-control mb-2" placeholder="Cantidad servidores">
        <input type="number" step="0.01" name="valor_infraestructura" class="form-control mb-2" placeholder="Valor infraestructura RD$">
        <input type="text" name="telefono" class="form-control mb-2" placeholder="Teléfono">
        <input type="email" name="email" class="form-control mb-2" placeholder="Email">
        <textarea name="direccion" class="form-control mb-2" placeholder="Dirección"></textarea>

        <button class="btn btn-success">Guardar</button>
        <a href="empresas.php" class="btn btn-secondary">Volver</a>

    </form>
</div>

</body>
</html>
