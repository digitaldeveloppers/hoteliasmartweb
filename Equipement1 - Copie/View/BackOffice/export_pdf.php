<?php
// Include config and model
require_once '../../config.php';
require_once '../../Controller/servivecontrolle.php';

// Database connection and data retrieval
$pdo = config::getConnexion();
$services = afficherServices($pdo);

// PDF Header
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="services_list.pdf"');

// PDF Content Setup
$title = "Liste des Services";
$date = date('d/m/Y H:i');
$pageWidth = 595;
$pageHeight = 842;
$margin = 50;
$lineHeight = 16;
$currentY = $pageHeight - $margin;

// Colors (RGB values 0-1)
$titleColor = "0.2 0.4 0.6";       // Dark blue
$headerColor = "0.8 0.1 0.1";      // Red
$textColor = "0 0 0";              // Black

// PDF Objects
$objects = [];

// Catalog
$objects[1] = "1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj";

// Pages
$objects[2] = "2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj";

// Page
$objects[3] = "3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $pageWidth $pageHeight]
   /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >>
   /Contents 6 0 R
>>
endobj";

// Font - Helvetica Regular
$objects[4] = "4 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj";

// Font - Helvetica Bold
$objects[5] = "5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>
endobj";

// Content stream
$stream = "";

// Add title
$stream .= "BT\n";
$stream .= "/F2 20 Tf\n";                  // Bold font, size 20
$stream .= "$titleColor rg\n";             // Set text color
$stream .= ($pageWidth/2 - 60) . " $currentY Td\n";  // Centered position
$stream .= "($title) Tj\n";
$stream .= "ET\n";
$currentY -= 30;

// Add date
$stream .= "BT\n";
$stream .= "/F1 10 Tf\n";                  // Regular font, size 10
$stream .= "$textColor rg\n";              // Set text color
$stream .= ($pageWidth - $margin - 100) . " $currentY Td\n";  // Right aligned
$stream .= "($date) Tj\n";
$stream .= "ET\n";
$currentY -= 20;

// Add separator line
$stream .= "$headerColor RG\n";            // Set stroke color
$stream .= "2 w\n";                        // Line width
$stream .= "$margin $currentY m\n";        // Move to start
$stream .= ($pageWidth - $margin) . " $currentY l\n";  // Line to end
$stream .= "S\n";                          // Stroke
$currentY -= 30;

// Table header
$stream .= "BT\n";
$stream .= "/F2 12 Tf\n";                  // Bold font
$stream .= "$headerColor rg\n";            // Red text
$stream .= "$margin $currentY Td\n";
$stream .= "(No.) Tj\n";
$stream .= "40 0 Td\n";
$stream .= "(Titre) Tj\n";
$stream .= "150 0 Td\n";
$stream .= "(Quantite) Tj\n";
$stream .= "70 0 Td\n";
$stream .= "(Prix) Tj\n";
$stream .= "50 0 Td\n";
$stream .= "(Description) Tj\n";
$stream .= "ET\n";
$currentY -= $lineHeight;

// Services list
if (count($services) > 0) {
    $count = 1;
    
    foreach ($services as $s) {
        // Service details (no alternate background)
        $stream .= "BT\n";
        $stream .= "/F1 10 Tf\n";          // Regular font
        $stream .= "$textColor rg\n";       // Black text
        
        // Number
        $stream .= "$margin $currentY Td\n";
        $stream .= "($count) Tj\n";
        
        // Title
        $stream .= "40 0 Td\n";
        $stream .= "(" . str_replace(['(',')','\\'], ['\\(','\\)','\\\\'], $s['title']) . ") Tj\n";
        
        // Quantity
        $stream .= "150 0 Td\n";
        $stream .= "(" . $s['quantity'] . ") Tj\n";
        
        // Price
        $stream .= "70 0 Td\n";
        $stream .= "(" . number_format($s['price'], 2) . " DT) Tj\n";
        
        // Description
        $stream .= "50 0 Td\n";
        $desc = strlen($s['description']) > 30 ? substr($s['description'], 0, 27) . '...' : $s['description'];
        $stream .= "(" . str_replace(['(',')','\\'], ['\\(','\\)','\\\\'], $desc) . ") Tj\n";
        
        $stream .= "ET\n";
        
        $count++;
        $currentY -= $lineHeight;
        
        // Page break if needed
        if ($currentY < $margin) {
            $stream .= "BT\n";
            $stream .= "/F1 10 Tf\n";
            $stream .= "$textColor rg\n";
            $stream .= "$margin $currentY Td\n";
            $stream .= "(-- Plus de services disponibles --) Tj\n";
            $stream .= "ET\n";
            break;
        }
    }
} else {
    $stream .= "BT\n";
    $stream .= "/F1 12 Tf\n";
    $stream .= "$textColor rg\n";
    $stream .= ($pageWidth/2 - 60) . " $currentY Td\n";
    $stream .= "(Aucun service trouve.) Tj\n";
    $stream .= "ET\n";
}

// Footer
$currentY = $margin - 20;
$stream .= "BT\n";
$stream .= "/F1 8 Tf\n";
$stream .= "0.5 0.5 0.5 rg\n";            // Gray text
$stream .= ($pageWidth/2 - 40) . " $currentY Td\n";
$stream .= "(Page 1 sur 1) Tj\n";
$stream .= "ET\n";

// Add content stream to objects
$objects[6] = "6 0 obj
<< /Length " . strlen($stream) . " >>
stream
$stream
endstream
endobj";

// Calculate xref offsets
$offsets = [];
$pos = 0;
$pdf = "%PDF-1.7\n";
$pos += strlen($pdf);

foreach ($objects as $id => $o) {
    $offsets[$id] = $pos;
    $pdf .= $o . "\n";
    $pos += strlen($o) + 1;
}

// Build xref
$pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
$pdf .= str_pad("0", 10, "0", STR_PAD_LEFT) . " 65535 f \n";
for ($i = 1; $i <= count($objects); $i++) {
    $pdf .= str_pad($offsets[$i], 10, "0", STR_PAD_LEFT) . " 00000 n \n";
}

// Trailer and EOF
$pdf .= "trailer
<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>
startxref
$pos
%%EOF";

// Output PDF
echo $pdf;
exit;
?>