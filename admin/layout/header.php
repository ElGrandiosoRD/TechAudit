<?php
if(!isset($_SESSION)){
    session_start();
}

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TechAudit - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">
        <i class="bi bi-shield-lock"></i> TechAudit Admin
    </span>

    <div>
        <span class="text-white me-3">
            <i class="bi bi-person-circle"></i> <?= $_SESSION['nombre'] ?>
        </span>
        <a href="../auth/logout.php" class="btn btn-sm btn-outline-light">
            <i class="bi bi-box-arrow-right"></i> Salir
        </a>
    </div>
</nav>

<div class="container-fluid">
<div class="row">
