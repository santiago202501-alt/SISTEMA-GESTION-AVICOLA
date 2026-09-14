<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../fpdf/fpdf.php';

requireLogin();

$db = getDB();

if (
    !isset($_GET['modulo']) ||
    !isset($_GET['inicio']) ||
    !isset($_GET['fin'])
) {
    die("Parámetros incompletos.");
}

$modulo = $_GET['modulo'];
$inicio = $_GET['inicio'];
$fin = $_GET['fin'];

$tablas = [
    'galpones'   => 'galpones',
    'agua'       => 'registros_agua',
    'alimento'   => 'registros_alimento',
    'amoniaco'   => 'registros_amoniaco',
    'mortalidad' => 'mortalidad',
    'inventario' => 'inventario',
    'usuarios'   => 'usuarios'
];

if (!array_key_exists($modulo, $tablas)) {
    die("Módulo no válido.");
}

$tabla = $tablas[$modulo];

$sql = "
SELECT *
FROM $tabla
WHERE created_at BETWEEN ? AND ?
ORDER BY created_at DESC
";

$stmt = $db->prepare($sql);

$stmt->execute([
    $inicio . " 00:00:00",
    $fin . " 23:59:59"
]);

$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

class PDF extends FPDF
{

    function Header()
    {

        $this->SetFont('Arial','B',18);

        $this->Cell(0,10,'SIGA',0,1,'C');

        $this->SetFont('Arial','',11);

        $this->Cell(0,7,'Sistema Integral de Gestion Avicola',0,1,'C');

        $this->Ln(5);

        $this->SetFont('Arial','B',14);

        global $modulo;

        $this->Cell(
            0,
            8,
            'Reporte de '.ucfirst($modulo),
            0,
            1,
            'C'
        );

        $this->Ln(4);

        $this->SetFont('Arial','',10);

        $this->Cell(
            0,
            6,
            'Fecha de generacion: '.date('d/m/Y H:i'),
            0,
            1
        );

        $this->Cell(
            0,
            6,
            'Usuario: '.$_SESSION['nombre'],
            0,
            1
        );

        $this->Ln(5);

    }

    function Footer()
    {

        $this->SetY(-15);

        $this->SetFont('Arial','I',8);

        $this->Cell(
            0,
            10,
            'Pagina '.$this->PageNo(),
            0,
            0,
            'C'
        );

    }

}
$pdf = new PDF('L', 'mm', 'A4');

$pdf->AliasNbPages();

$pdf->AddPage();

$pdf->SetFont('Arial','B',9);

if(count($datos)>0){

    $columnas = array_keys($datos[0]);

    $ancho = 270 / count($columnas);

    foreach($columnas as $columna){

        $pdf->Cell(
            $ancho,
            8,
            utf8_decode(ucwords(str_replace('_',' ',$columna))),
            1,
            0,
            'C'
        );

    }

    $pdf->Ln();

    $pdf->SetFont('Arial','',8);

    foreach($datos as $fila){

        foreach($fila as $valor){

            if($valor===null){

                $valor='';

            }

            $pdf->Cell(

                $ancho,

                7,

                utf8_decode(substr($valor,0,35)),

                1,

                0,

                'C'

            );

        }

        $pdf->Ln();

    }

}else{

    $pdf->SetFont('Arial','B',12);

    $pdf->Cell(

        0,

        10,

        utf8_decode('No existen registros para el rango seleccionado.'),

        1,

        1,

        'C'

    );

}$nombreArchivo =
"Reporte_" .
ucfirst($modulo) .
"_" .
date("Ymd_His") .
".pdf";

$pdf->Output(

'I',

$nombreArchivo

);

exit;