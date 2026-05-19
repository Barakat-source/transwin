<?php
require_once 'core/Database.php';
require_once 'fpdf/fpdf.php';

if (!isset($_GET['id'])) die('ID manquant');

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM voyages WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$v = $stmt->fetch();

if (!$v) die('Voyage introuvable');

// If mode is raw, output the actual PDF content
if (isset($_GET['mode']) && $_GET['mode'] === 'raw') {
    class PDF extends FPDF {
        function Header() {
            if (file_exists('assets/images/logo.png')) {
                $this->Image('assets/images/logo.png', 10, 10, 50);
            }
            $this->SetFont('Arial', 'B', 16);
            $this->SetTextColor(37, 99, 235);
            $this->Cell(0, 10, 'ORDRE DE MISSION', 0, 1, 'R');
            $this->Ln(20);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'RECEPISSE DE TRANSPORT #' . $v['id'], 1, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(45, 10, 'DATE:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(50, 10, date('d/m/Y', strtotime($v['date_voyage'])), 1, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 10, 'IMP/EXP:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $v['flux_type'], 1, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 10, 'CLIENT:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, utf8_decode($v['client']), 1, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 10, 'CHAUFFEUR:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(50, 10, utf8_decode($v['chauffeur']), 1, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 10, 'MATRICULE:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $v['matricule'], 1, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 10, 'TC (CONTENEUR):', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(50, 10, $v['tc'], 1, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45, 10, 'TYPE REMORQUE:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $v['remorque_type'], 1, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, 'TRAJET:', 1, 1, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 10, 'DEPART: ' . utf8_decode($v['depart']) . "\n" . 'DESTINATION: ' . utf8_decode($v['destination']), 1, 'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, 'OBSERVATIONS:', 1, 1, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 10, utf8_decode($v['observation']), 1, 'L');
    $pdf->Ln(20);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(95, 10, 'Cachet Exploitation', 0, 0, 'C');
    $pdf->Cell(95, 10, 'Signature Chauffeur', 0, 1, 'C');
    $pdf->Cell(95, 30, '', 1, 0);
    $pdf->Cell(95, 30, '', 1, 1);
    $pdf->Output('I', 'OM_' . $v['id'] . '.pdf');
    exit;
}

// Otherwise, show HTML wrapper with PDF Favicon
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PDF - Ordre de Mission #<?= $v['id'] ?></title>
    <link rel="icon" type="image/png" href="assets/images/pdf_icon.png">
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background: #525659; }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>
    <iframe src="pdf.php?id=<?= $v['id'] ?>&mode=raw"></iframe>
</body>
</html>
