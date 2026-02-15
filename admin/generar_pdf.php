<?php
require "../config/database.php";
require "../lib/fpdf/fpdf.php";

if (!isset($_GET['id'])) {
    die("Cotización no válida");
}

$id = (int)$_GET['id'];

/* ==============================
   OBTENER DATOS PRINCIPALES
============================== */

$query = "
SELECT 
    c.*,
    a.tipo,
    a.nivel_complejidad,
    a.riesgo_preliminar,
    e.nombre AS empresa_nombre,
    e.email AS empresa_email
FROM cotizaciones c
INNER JOIN auditorias a ON c.auditoria_id = a.id
INNER JOIN empresas e ON a.empresa_id = e.id
WHERE c.id = $id
";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("No encontrada");
}

$data = $result->fetch_assoc();

/* ==============================
   MULTIPLICADOR SEGUN COMPLEJIDAD
============================== */

$nivel = strtolower($data['nivel_complejidad']);

switch($nivel){
    case 'baja':
        $multiplicador = 1;
        break;
    case 'media':
        $multiplicador = 1.5;
        break;
    case 'alta':
        $multiplicador = 2;
        break;
    default:
        $multiplicador = 1;
}

/* ==============================
   OBTENER DETALLE DE SERVICIOS
============================== */

$detalleQuery = "
SELECT servicio, descripcion, cantidad, precio_unitario, total
FROM cotizacion_detalle
WHERE cotizacion_id = $id
";

$detalleResult = $conn->query($detalleQuery);

/* ==============================
   CLASE PDF PERSONALIZADA
============================== */

class PDF extends FPDF {

    function Header() {
        $this->Image('../assets/logo.png',10,8,30);

        $this->SetFont('Arial','B',13);
        $this->Cell(0,6,'TechAudit Solutions SRL',0,1,'R');

        $this->SetFont('Arial','',9);
        $this->Cell(0,5,'RNC: 1-23-45678-9',0,1,'R');
        $this->Cell(0,5,'Santo Domingo, Republica Dominicana',0,1,'R');
        $this->Cell(0,5,'Tel: (809) 000-0000 | info@techaudit.com',0,1,'R');

        $this->Ln(8);
        $this->Line(10,38,200,38);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,5,'Documento confidencial - TechAudit',0,1,'L');
        $this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
    }
}

/* ==============================
   CREAR PDF
============================== */

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

/* TITULO */

$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,10,'COTIZACION No: '.$data['numero'],0,1,'C');
$pdf->Ln(5);

/* ==============================
   CLIENTE
============================== */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Datos del Cliente',0,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,6,'Empresa: '.$data['empresa_nombre'],0,1);
$pdf->Cell(0,6,'Email: '.$data['empresa_email'],0,1);
$pdf->Cell(0,6,'Fecha: '.$data['fecha'],0,1);
$pdf->Ln(8);

/* ==============================
   DETALLES AUDITORIA
============================== */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Detalles de la Auditoria',0,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,6,'Tipo: '.$data['tipo'],0,1);
$pdf->Cell(0,6,'Nivel de Complejidad: '.$data['nivel_complejidad'],0,1);
$pdf->Cell(0,6,'Riesgo Preliminar: '.$data['riesgo_preliminar'],0,1);
$pdf->Cell(0,6,'Multiplicador Aplicado: x'.$multiplicador,0,1);
$pdf->Ln(8);

/* ==============================
   TABLA DETALLADA
============================== */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Detalle de Servicios',0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,8,'Servicio',1);
$pdf->Cell(40,8,'Cantidad',1,0,'C');
$pdf->Cell(50,8,'Precio Unitario',1,0,'R');
$pdf->Cell(0,8,'Total',1,1,'R');

$pdf->SetFont('Arial','',10);

$subtotal_calculado = 0;

if($detalleResult->num_rows > 0){
    while($row = $detalleResult->fetch_assoc()){

        $subtotal_calculado += $row['total'];

        $pdf->Cell(50,8,$row['servicio'],1);
        $pdf->Cell(40,8,$row['cantidad'],1,0,'C');
        $pdf->Cell(50,8,'RD$ '.number_format($row['precio_unitario'],2),1,0,'R');
        $pdf->Cell(0,8,'RD$ '.number_format($row['total'],2),1,1,'R');
    }
}else{
    $pdf->Cell(0,8,'No hay servicios registrados.',1,1,'C');
}

$pdf->Ln(8);

/* ==============================
   RESUMEN FINANCIERO
============================== */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Resumen Financiero',0,1);

$pdf->SetFont('Arial','',11);

$pdf->Cell(120,8,'Subtotal',1);
$pdf->Cell(0,8,'RD$ '.number_format($subtotal_calculado,2),1,1,'R');

$pdf->Cell(120,8,'Imprevistos (5%)',1);
$pdf->Cell(0,8,'RD$ '.number_format($data['imprevistos'],2),1,1,'R');

$pdf->Cell(120,8,'ITBIS (18%)',1);
$pdf->Cell(0,8,'RD$ '.number_format($data['impuesto'],2),1,1,'R');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(120,8,'TOTAL GENERAL',1);
$pdf->Cell(0,8,'RD$ '.number_format($data['total'],2),1,1,'R');

$pdf->Ln(10);

/* ==============================
   CRONOGRAMA DINAMICO
============================== */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Cronograma Estimado',0,1);

$pdf->SetFont('Arial','',11);

if($nivel == "baja"){
    $dur1="3 dias"; $dur2="5 dias"; $dur3="2 dias";
}elseif($nivel == "media"){
    $dur1="1 semana"; $dur2="2 semanas"; $dur3="1 semana";
}else{
    $dur1="2 semanas"; $dur2="4 semanas"; $dur3="2 semanas";
}

$pdf->Cell(90,8,'Planificacion',1);
$pdf->Cell(0,8,$dur1,1,1);

$pdf->Cell(90,8,'Ejecucion Tecnica',1);
$pdf->Cell(0,8,$dur2,1,1);

$pdf->Cell(90,8,'Informe Final',1);
$pdf->Cell(0,8,$dur3,1,1);

$pdf->Ln(10);

/* ==============================
   TERMINOS
============================== */

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Terminos y Condiciones',0,1);

$pdf->SetFont('Arial','',10);

$pdf->MultiCell(0,6,"
1. Validez de 15 dias.
2. Pago 50% inicial y 50% contra entrega.
3. Servicios adicionales se cotizan por separado.
4. Cronograma sujeto a aprobacion.
5. Confidencialidad garantizada.
");

$pdf->Output('I','Cotizacion_'.$data['numero'].'.pdf');
exit;
