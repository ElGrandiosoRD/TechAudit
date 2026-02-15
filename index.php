<?php
session_start();
if(isset($_SESSION['rol'])){
    if($_SESSION['rol'] == 'admin'){
        header("Location: admin/dashboard.php");
    } else {
        header("Location: cliente/dashboard.php");
    }
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>TechAudit - Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="width:350px;">
    <h3 class="text-center mb-3">TechAudit</h3>
    <form action="auth/login.php" method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Correo" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>
        <button class="btn btn-primary w-100">Ingresar</button>
    </form>
</div>

</body>
</html>
