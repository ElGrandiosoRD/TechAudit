<?php
require "../config/database.php";
$id = $_GET['id'];
$conn->query("UPDATE cotizaciones SET estado='Aprobada' WHERE id=$id");
header("Location: cotizaciones.php");
