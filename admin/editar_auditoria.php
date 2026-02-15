<?php
session_start();
require '../config/database.php';

$id = (int)$_GET['id'];

/* Obtener datos de la auditoría */
$sql = "SELECT * FROM auditorias WHERE id = $id";
$auditoria = $conn->query($sql)->fetch_assoc();
if(!$auditoria) die("Auditoría no encontrada.");

/* Obtener todas las empresas para el select */
$empresas = $conn->query("SELECT id, nombre FROM empresas ORDER BY nombre ASC");
?>
<?php include "layout/header.php"; ?>
<?php include "layout/sidebar.php"; ?>

<div class="container mt-4">
    <h2>Editar Auditoría #<?= $auditoria['id'] ?></h2>

    <form method="POST" action="actualizar_auditoria.php">
        <input type="hidden" name="id" value="<?= $auditoria['id'] ?>">

        <div class="mb-3">
            <label for="empresa_id" class="form-label">Empresa</label>
            <select name="empresa_id" id="empresa_id" class="form-select">
                <?php while($e = $empresas->fetch_assoc()): ?>
                    <option value="<?= $e['id'] ?>" <?= $e['id']==$auditoria['empresa_id']?'selected':'' ?>>
                        <?= $e['nombre'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de Auditoría</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="Infraestructura" <?= $auditoria['tipo']=='Infraestructura'?'selected':'' ?>>Infraestructura</option>
                <option value="Seguridad" <?= $auditoria['tipo']=='Seguridad'?'selected':'' ?>>Seguridad</option>
                <option value="Integral" <?= $auditoria['tipo']=='Integral'?'selected':'' ?>>Integral</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="nivel_complejidad" class="form-label">Nivel de Complejidad</label>
            <select name="nivel_complejidad" id="nivel_complejidad" class="form-select">
                <option value="Baja" <?= $auditoria['nivel_complejidad']=='Baja'?'selected':'' ?>>Baja</option>
                <option value="Media" <?= $auditoria['nivel_complejidad']=='Media'?'selected':'' ?>>Media</option>
                <option value="Alta" <?= $auditoria['nivel_complejidad']=='Alta'?'selected':'' ?>>Alta</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="riesgo_preliminar" class="form-label">Riesgo Preliminar</label>
            <select name="riesgo_preliminar" id="riesgo_preliminar" class="form-select">
                <option value="Bajo" <?= $auditoria['riesgo_preliminar']=='Bajo'?'selected':'' ?>>Bajo</option>
                <option value="Medio" <?= $auditoria['riesgo_preliminar']=='Medio'?'selected':'' ?>>Medio</option>
                <option value="Alto" <?= $auditoria['riesgo_preliminar']=='Alto'?'selected':'' ?>>Alto</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="Planificada" <?= $auditoria['estado']=='Planificada'?'selected':'' ?>>Planificada</option>
                <option value="En Proceso" <?= $auditoria['estado']=='En Proceso'?'selected':'' ?>>En Proceso</option>
                <option value="Finalizada" <?= $auditoria['estado']=='Finalizada'?'selected':'' ?>>Finalizada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Auditoría</button>
        <a href="auditorias.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include "layout/footer.php"; ?>
