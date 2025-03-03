<?php
// File path: controllers/quotations/generate_pdf.php

require 'Database.php';
$config = require 'config.php';
require 'vendor/autoload.php'; // Require Composer's autoloader

// Initialize the database
$db = new Database($config['database']);

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /quotations?error=Quotation ID is required');
    exit;
}

$id = (int) $_GET['id'];

// Get the quotation with agency info
$quotation = $db->query("
    SELECT q.*, a.name as agency_name, a.address as agency_address, a.contact_person, a.contact_number, a.email as agency_email
    FROM quotations q
    LEFT JOIN agencies a ON q.agency_id = a.id
    WHERE q.id = ?
", [$id])->fetch();

// Check if quotation exists
if (!$quotation) {
    header('Location: /quotations?error=Quotation not found');
    exit;
}

// Get quotation items
$items = $db->query("
    SELECT * FROM quotation_items 
    WHERE quotation_id = ?
    ORDER BY id ASC
", [$id])->fetchAll();

// Create new PDF document
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        $this->SetY(10);
        // Logo
        //$this->Image('logo.png', 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 14);
        // Title
        $this->Cell(0, 15, 'Tekstore Computer Parts and Accessories Trading', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Slogan
        $this->Ln(8);
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 10, 'Fast and Quality Business Solution', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Address
        $this->Ln(6);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 10, 'Magsaysay Street, Bantug, Roxas, Isabela', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        
        $this->Ln(10);
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'QUOTATION', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(15);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Initialize PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Tekstore Quotation System');
$pdf->SetAuthor('Tekstore');
$pdf->SetTitle('Quotation #' . $quotation['quote_number']);
$pdf->SetSubject('Quotation');
$pdf->SetKeywords('Quotation, Tekstore, Computer');

// Set default header and footer data
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font for quotation details
$pdf->SetFont('helvetica', '', 10);

// Quotation details
$pdf->Cell(40, 10, 'Quote Number:', 0, 0);
$pdf->Cell(80, 10, $quotation['quote_number'], 0, 0);
$pdf->Cell(30, 10, 'Date:', 0, 0);
$pdf->Cell(40, 10, date('F j, Y', strtotime($quotation['quote_date'])), 0, 1);

if ($quotation['agency_name']) {
    $pdf->Cell(40, 10, 'Agency:', 0, 0);
    $pdf->Cell(150, 10, $quotation['agency_name'], 0, 1);
}

$pdf->Ln(5);

// Client Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Client Information', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$pdf->Cell(40, 8, 'Name:', 0, 0);
$pdf->Cell(150, 8, $quotation['client_name'], 0, 1);

if ($quotation['client_email']) {
    $pdf->Cell(40, 8, 'Email:', 0, 0);
    $pdf->Cell(150, 8, $quotation['client_email'], 0, 1);
}

if ($quotation['client_address']) {
    $pdf->Cell(40, 8, 'Address:', 0, 0);
    $pdf->Cell(150, 8, $quotation['client_address'], 0, 1);
}

if ($quotation['budget']) {
    $pdf->Cell(40, 8, 'Budget:', 0, 0);
    $pdf->Cell(150, 8, '₱' . number_format($quotation['budget'], 2), 0, 1);
}

$pdf->Ln(5);

// Items
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Quotation Items', 0, 1);
$pdf->SetFont('helvetica', '', 10);

// Items table header
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(10, 10, '#', 1, 0, 'C', 1);
$pdf->Cell(70, 10, 'Item Description', 1, 0, 'C', 1);
$pdf->Cell(25, 10, 'Quantity', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Unit Price', 1, 0, 'C', 1);
$pdf->Cell(25, 10, 'Final Price', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Amount', 1, 1, 'C', 1);

// Items table rows
$pdf->SetFont('helvetica', '', 10);
$i = 0;
foreach ($items as $item) {
    $i++;
    $pdf->Cell(10, 10, $i, 1, 0, 'C');
    $pdf->Cell(70, 10, $item['item_name'], 1, 0, 'L');
    $pdf->Cell(25, 10, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, '₱' . number_format($item['unit_price'], 2), 1, 0, 'R');
    $pdf->Cell(25, 10, '₱' . number_format($item['final_price'], 2), 1, 0, 'R');
    $pdf->Cell(30, 10, '₱' . number_format($item['amount'], 2), 1, 1, 'R');
}

// Total
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(160, 10, 'Total', 1, 0, 'R', 1);
$pdf->Cell(30, 10, '₱' . number_format($quotation['total'], 2), 1, 1, 'R', 1);

$pdf->Ln(5);

// Notes
if ($quotation['notes']) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Notes', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 10, $quotation['notes'], 0, 'L');
    $pdf->Ln(5);
}

// Status
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Status: ', 0, 0);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, ucfirst($quotation['status']), 0, 1);

$pdf->Ln(10);

// Signature lines
$pdf->Line(20, $pdf->GetY(), 90, $pdf->GetY());
$pdf->Line(110, $pdf->GetY(), 180, $pdf->GetY());

$pdf->Ln(2);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 10, 'Authorized by', 0, 0, 'C');
$pdf->Cell(90, 10, 'Accepted by', 0, 1, 'C');

// Output the PDF
$pdf->Output('Quotation_' . $quotation['quote_number'] . '.pdf', 'I');