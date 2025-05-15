<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');
require_once(__DIR__ . '/../../fpdf/fpdf.php');

// Fetch all equipment items
$equipementC = new equipementController();
$listeEquipements = $equipementC->listEquipement();

// Create new PDF document
class PDF extends FPDF {
    function Header() {
        // Set font for header
        $this->SetFont('Arial', 'B', 20);
        
        // Add logo
        $this->Image('../FrontOffice/assets/HS.png', 10, 6, 30);
        
        // Title
        $this->Cell(0, 10, 'HOTELIA SMART', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Equipment List', 0, 1, 'C');
        $this->Ln(10);

        // Calculate total table width and center position
        $tableWidth = 230; // Total of all column widths
        $pageWidth = $this->GetPageWidth();
        $leftMargin = ($pageWidth - $tableWidth) / 2;
        $this->SetX($leftMargin);

        // Table Header with adjusted widths
        $this->SetFillColor(0, 48, 135); // Dark blue background
        $this->SetTextColor(255, 255, 255); // White text
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(45, 12, 'Reference', 1, 0, 'C', true);
        $this->Cell(70, 12, 'Name', 1, 0, 'C', true);
        $this->Cell(35, 12, 'Price (TND)', 1, 0, 'C', true);
        $this->Cell(35, 12, 'Quantity', 1, 0, 'C', true);
        $this->Cell(45, 12, 'Type', 1, 0, 'C', true);
        $this->Ln();
        
        // Reset text color for content
        $this->SetTextColor(0, 0, 0);
    }

    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Instantiate PDF object
$pdf = new PDF('L'); // Set to Landscape orientation
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// Set cell padding
$pdf->SetFillColor(255, 255, 255);
$pdf->SetLineWidth(0.3);

// Add data rows with adjusted heights, alignment and padding
foreach ($listeEquipements as $equipement) {
    // Calculate required height for the name cell based on content
    $nameHeight = max(10, $pdf->GetStringWidth($equipement['nom'])/65 * 10);
    $rowHeight = max($nameHeight, 10);
    
    // Center the row
    $leftMargin = ($pdf->GetPageWidth() - 230) / 2;
    $pdf->SetX($leftMargin);
    
    $pdf->Cell(45, $rowHeight, $equipement['reference'], 1, 0, 'C', true);
    // Use MultiCell for name to handle long text
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(70, $rowHeight/2, $equipement['nom'], 1, 'L', true);
    $pdf->SetXY($x + 70, $y);
    $pdf->Cell(35, $rowHeight, number_format($equipement['prix'], 2), 1, 0, 'R', true);
    $pdf->Cell(35, $rowHeight, $equipement['quantite'], 1, 0, 'C', true);
    $pdf->Cell(45, $rowHeight, $equipement['type'], 1, 1, 'C', true);
}

// Output PDF
$pdf->Output('Equipment_List.pdf', 'D');