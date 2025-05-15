<?php
require_once(__DIR__ . '/../../Controller/commandeController.php');
require_once(__DIR__ . '/../../fpdf/fpdf.php');

// Define font path
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/../../fpdf/font/');
}

class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('../FrontOffice/assets/HS.png', 10, 6, 30);
        
        // Title
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, 'HOTELIA SMART', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'ORDERS REPORT', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Generated: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$commandeController = new commandeController();
$commandes = $commandeController->listCommandes();

// Create PDF
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Table Settings
$header = ['ID', 'Date', 'Client', 'Address', 'Email', 'Payment', 'Total'];
$widths = [15, 25, 30, 60, 45, 25, 20];
$aligns = ['C', 'C', 'L', 'L', 'L', 'C', 'R'];

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(0, 48, 135);
$pdf->SetTextColor(255);
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 8, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);
$fill = false;

$allCommandes = $commandes->fetchAll(PDO::FETCH_ASSOC);

foreach ($allCommandes as $commande) {
    $pdf->SetFillColor($fill ? 240 : 255);
    
    // ID, Date, Client
    $pdf->Cell($widths[0], 6, $commande['idCommande'], 'LR', 0, $aligns[0], $fill);
    $pdf->Cell($widths[1], 6, date('Y-m-d', strtotime($commande['date'])), 'LR', 0, $aligns[1], $fill);
    $pdf->Cell($widths[2], 6, $commande['nomClient'] . ' ' . $commande['prenomClient'], 'LR', 0, $aligns[2], $fill);
    
    // Address with MultiCell
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell($widths[3], 6, $commande['adresseClient'], 'LR', $aligns[3], $fill);
    $max_y = $pdf->GetY();
    $pdf->SetXY($x + $widths[3], $y);
    
    // Email, Payment, Total
    $pdf->Cell($widths[4], $max_y - $y, $commande['mailClient'], 'LR', 0, $aligns[4], $fill);
    $pdf->Cell($widths[5], $max_y - $y, $commande['modePaiment'], 'LR', 0, $aligns[5], $fill);
    $pdf->Cell($widths[6], $max_y - $y, number_format($commande['total'], 2) . ' €', 'LR', 0, $aligns[6], $fill);
    
    $pdf->SetY($max_y);
    $fill = !$fill;
}

// Closing line
$pdf->Cell(array_sum($widths), 0, '', 'T');
$pdf->Output('Orders_Report_'.date('Ymd').'.pdf', 'D');
?>