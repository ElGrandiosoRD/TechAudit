<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente'){
    header("Location: ../index.php");
}
?>

<h2>Bienvenido <?php echo $_SESSION['nombre']; ?></h2>
<a href="../auth/logout.php">Cerrar sesión</a>
<a href="aprobar.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Aprobar</a>
<a href="rechazar.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Rechazar</a>