<?php
require "../config/database.php";
include "layout/header.php";
include "layout/sidebar.php";

/* ======================
   KPIs DE COTIZACIONES
====================== */
$total_cot = $conn->query("SELECT COUNT(*) AS t FROM cotizaciones")->fetch_assoc()['t'];
$aprobadas = $conn->query("SELECT COUNT(*) AS t FROM cotizaciones WHERE estado='Aprobada'")->fetch_assoc()['t'];
$borrador = $conn->query("SELECT COUNT(*) AS t FROM cotizaciones WHERE estado='Borrador'")->fetch_assoc()['t'];
$rechazadas = $conn->query("SELECT COUNT(*) AS t FROM cotizaciones WHERE estado='Rechazada'")->fetch_assoc()['t'];

/* ======================
   KPIs DE AUDITORÍAS
====================== */
$total_aud = $conn->query("SELECT COUNT(*) AS t FROM auditorias")->fetch_assoc()['t'];
$planificadas = $conn->query("SELECT COUNT(*) AS t FROM auditorias WHERE estado='Planificada'")->fetch_assoc()['t'];
$en_proceso = $conn->query("SELECT COUNT(*) AS t FROM auditorias WHERE estado='En Proceso'")->fetch_assoc()['t'];
$finalizadas = $conn->query("SELECT COUNT(*) AS t FROM auditorias WHERE estado='Finalizada'")->fetch_assoc()['t'];
?>

<div class="container mt-4">
    <h2>Dashboard</h2>

    <!-- Tarjetas Cotizaciones -->
    <!-- Tarjetas Cotizaciones -->
<div class="row g-3 mt-3">
    <div class="col-md-3">
        <div class="card shadow text-center">
            <div class="card-body">
                <h6>Total Cotizaciones</h6>
                <h3><?= $total_cot ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white shadow text-center">
            <div class="card-body">
                <h6>Aprobadas</h6>
                <h3><?= $aprobadas ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white shadow text-center">
            <div class="card-body">
                <h6>Borrador</h6>
                <h3><?= $borrador ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white shadow text-center">
            <div class="card-body">
                <h6>Rechazadas</h6>
                <h3><?= $rechazadas ?></h3>
            </div>
        </div>
    </div>
</div>


    <!-- Tarjetas Auditorías -->
    <div class="row g-3 mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow text-center">
                <div class="card-body">
                    <h6>Total Auditorías</h6>
                    <h3><?= $total_aud ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow text-center">
                <div class="card-body">
                    <h6>Planificadas</h6>
                    <h3><?= $planificadas ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow text-center">
                <div class="card-body">
                    <h6>En Proceso</h6>
                    <h3><?= $en_proceso ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow text-center">
                <div class="card-body">
                    <h6>Finalizadas</h6>
                    <h3><?= $finalizadas ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Cotizaciones por Estado</h5>
                    <canvas id="cotizacionesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Auditorías por Estado</h5>
                    <canvas id="auditoriasChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxCot = document.getElementById('cotizacionesChart').getContext('2d');
const cotizacionesChart = new Chart(ctxCot, {
    type: 'pie',
    data: {
        labels: ['Borrador','Aprobadas','Rechazadas'],
        datasets: [{
            label: 'Cotizaciones',
            data: [<?= $borrador ?>, <?= $aprobadas ?>, <?= $rechazadas ?>],
            backgroundColor: ['#6c757d','#28a745','#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

const ctxAud = document.getElementById('auditoriasChart').getContext('2d');
const auditoriasChart = new Chart(ctxAud, {
    type: 'bar',
    data: {
        labels: ['Planificadas','En Proceso','Finalizadas'],
        datasets: [{
            label: 'Auditorías',
            data: [<?= $planificadas ?>, <?= $en_proceso ?>, <?= $finalizadas ?>],
            backgroundColor: ['#17a2b8','#ffc107','#28a745']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<?php include "layout/footer.php"; ?>
