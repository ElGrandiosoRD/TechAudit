<?php
session_start();
require '../config/database.php';

$id = (int)$_GET['id'];

/* Obtener datos de la cotización */
$sql = "
SELECT c.*, a.nivel_complejidad
FROM cotizaciones c
JOIN auditorias a ON c.auditoria_id = a.id
WHERE c.id = $id
";
$cotizacion = $conn->query($sql)->fetch_assoc();
if(!$cotizacion) die("Cotización no encontrada.");

/* Obtener servicios de la cotización */
$detalle = $conn->query("
    SELECT *
    FROM cotizacion_detalle
    WHERE cotizacion_id = $id
");

/* Obtener todos los servicios disponibles */
$servicios_disponibles = $conn->query("SELECT * FROM servicios")->fetch_all(MYSQLI_ASSOC);

/* Crear array con los nombres de servicios ya agregados */
$servicios_agregados = [];
$detalle->data_seek(0);
while($d = $detalle->fetch_assoc()){
    $servicios_agregados[] = $d['servicio'];
}

?>
<?php include "layout/header.php"; ?>
<?php include "layout/sidebar.php"; ?>

<div class="container mt-4">
    <h2>Editar Cotización #<?php echo $cotizacion['numero']; ?></h2>

    <form method="POST" action="actualizar_cotizacion.php">
    <input type="hidden" name="cotizacion_id" value="<?php echo $id; ?>">

    <h4>Servicios Agregados</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Servicio</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $detalle->data_seek(0);
        while($d = $detalle->fetch_assoc()): ?>
            <tr class="fila">
                <td>
                    <?php echo $d['servicio']; ?>
                    <input type="hidden" name="detalle_id[]" value="<?php echo $d['id']; ?>">
                </td>
                <td>
                    <input type="number" step="0.01" name="precio[]" 
                    value="<?php echo $d['precio_unitario']; ?>" 
                    class="form-control precio" onkeyup="recalcular()">
                </td>
                <td>
                    <input type="number" name="cantidad[]" 
                    value="<?php echo $d['cantidad']; ?>" 
                    class="form-control cantidad" onkeyup="recalcular()">
                </td>
                <td>RD$ <span class="total"><?php echo $d['total']; ?></span></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h4>Agregar Servicios Nuevos</h4>
    <?php foreach($servicios_disponibles as $s): 
        if(in_array($s['nombre'], $servicios_agregados)) continue; // ya agregado
    ?>
    <div class="border p-3 mb-2 rounded">
        <input type="checkbox" name="servicios_nuevos[]" value="<?= $s['id'] ?>" class="form-check-input me-2">
        <strong><?= htmlspecialchars($s['nombre']) ?></strong><br>
        <?= htmlspecialchars($s['descripcion']) ?><br>
        Precio Base: RD$ <?= number_format($s['precio_base'],2) ?><br>
        Cantidad:
        <input type="number" name="cantidad_new_<?= $s['id'] ?>" value="1" min="1" class="form-control w-25 mt-1">
    </div>
    <?php endforeach; ?>

    <div class="mt-3">
        <h5>Resumen</h5>
        <p>Subtotal: RD$ <span id="subtotal"><?php echo $cotizacion['subtotal']; ?></span></p>
        <p>Imprevistos (10%): RD$ <span id="imprevistos"><?php echo $cotizacion['imprevistos']; ?></span></p>
        <p>ITBIS (18%): RD$ <span id="impuesto"><?php echo $cotizacion['impuesto']; ?></span></p>
        <h4>Total: RD$ <span id="totalFinal"><?php echo $cotizacion['total']; ?></span></h4>
    </div>

    <div class="mb-3">
        <label for="estado" class="form-label"><strong>Estado de la Cotización</strong></label>
        <select name="estado" id="estado" class="form-select">
            <option value="Borrador" <?= $cotizacion['estado']=='Borrador'?'selected':'' ?>>Borrador</option>
            <option value="Enviada" <?= $cotizacion['estado']=='Enviada'?'selected':'' ?>>Enviada</option>
            <option value="Aprobada" <?= $cotizacion['estado']=='Aprobada'?'selected':'' ?>>Aprobada</option>
            <option value="Rechazada" <?= $cotizacion['estado']=='Rechazada'?'selected':'' ?>>Rechazada</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar Cotización</button>
    <a href="ver_cotizacion.php?id=<?php echo $id; ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
function recalcular(){
    let filas = document.querySelectorAll(".fila");
    let subtotal = 0;
    filas.forEach(fila => {
        let precio = parseFloat(fila.querySelector(".precio").value) || 0;
        let cantidad = parseFloat(fila.querySelector(".cantidad").value) || 0;
        let total = precio * cantidad;
        fila.querySelector(".total").innerText = total.toFixed(2);
        subtotal += total;
    });
    document.querySelectorAll('input[name^="servicios_nuevos[]"]:checked').forEach(cb => {
        let id = cb.value;
        let cantidad = parseFloat(document.querySelector('input[name="cantidad_new_'+id+'"]').value) || 0;
        let precio = parseFloat(cb.dataset.precio) || 0;
        subtotal += precio * cantidad;
    });

    document.getElementById("subtotal").innerText = subtotal.toFixed(2);
    let imprevistos = subtotal * 0.10;
    let impuesto = subtotal * 0.18;
    let totalFinal = subtotal + imprevistos + impuesto;
    document.getElementById("imprevistos").innerText = imprevistos.toFixed(2);
    document.getElementById("impuesto").innerText = impuesto.toFixed(2);
    document.getElementById("totalFinal").innerText = totalFinal.toFixed(2);
}
</script>

<?php include "layout/footer.php"; ?>
