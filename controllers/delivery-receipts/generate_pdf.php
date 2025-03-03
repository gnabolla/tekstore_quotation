<?php
// File path: controllers/delivery-receipts/generate_pdf.php

require 'Database.php';
$config = require 'config.php';
require 'vendor/autoload.php'; // Require Composer's autoloader

// Initialize the database
$db = new Database($config['database']);

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /delivery-receipts?error=Delivery Receipt ID is required');
    exit;
}

$id = (int) $_GET['id'];

// Get the delivery receipt with quotation and agency info
$receipt = $db->query("
    SELECT dr.*, 
           q.quote_number, q.client_name, q.client_email, q.client_address,
           a.name as agency_name, a.address as agency_address, 
           a.contact_person, a.contact_number, a.email as agency_email
    FROM delivery_receipts dr
    JOIN quotations q ON dr.quotation_id = q.id
    LEFT JOIN agencies a ON q.agency_id = a.id
    WHERE dr.id = ?
", [$id])->fetch();

// Check if delivery receipt exists
if (!$receipt) {
    header('Location: /delivery-receipts?error=Delivery Receipt not found');
    exit;
}

// Get delivery receipt items
$items = $db->query("
    SELECT dri.*
    FROM delivery_receipt_items dri
    WHERE dri.delivery_receipt_id = ?
    ORDER BY dri.id ASC
", [$id])->fetchAll();

// Create new PDF document
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        $this->SetY(10);
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
        $this->Cell(0, 10, 'DELIVERY RECEIPT', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Delivery Receipt #' . $receipt['receipt_number']);
$pdf->SetSubject('Delivery Receipt');
$pdf->SetKeywords('Delivery, Receipt, Tekstore, Computer');

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

// Set font for receipt details
$pdf->SetFont('helvetica', '', 10);

// Receipt details
$pdf->Cell(40, 10, 'Receipt Number:', 0, 0);
$pdf->Cell(80, 10, $receipt['receipt_number'], 0, 0);
$pdf->Cell(30, 10, 'Date:', 0, 0);
$pdf->Cell(40, 10, date('F j, Y', strtotime($receipt['receipt_date'])), 0, 1);

$pdf->Cell(40, 10, 'Quotation Number:', 0, 0);
$pdf->Cell(150, 10, $receipt['quote_number'], 0, 1);

if ($receipt['agency_name']) {
    $pdf->Cell(40, 10, 'Agency:', 0, 0);
    $pdf->Cell(150, 10, $receipt['agency_name'], 0, 1);
}

$pdf->Ln(5);

// Client Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Client Information', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$pdf->Cell(40, 8, 'Name:', 0, 0);
$pdf->Cell(150, 8, $receipt['client_name'], 0, 1);

if ($receipt['delivery_address']) {
    $pdf->Cell(40, 8, 'Delivery Address:', 0, 0);
    $pdf->Cell(150, 8, $receipt['delivery_address'], 0, 1);
}

if ($receipt['received_by']) {
    $pdf->Cell(40, 8, 'Received By:', 0, 0);
    $pdf->Cell(150, 8, $receipt['received_by'], 0, 1);
}

if ($receipt['contact_number']) {
    $pdf->Cell(40, 8, 'Contact Number:', 0, 0);
    $pdf->Cell(150, 8, $receipt['contact_number'], 0, 1);
}

$pdf->Ln(5);

// Delivery Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Delivery Information', 0, 1);
$pdf->SetFont('helvetica', '', 10);

if ($receipt['driver_name']) {
    $pdf->Cell(40, 8, 'Driver Name:', 0, 0);
    $pdf->Cell(150, 8, $receipt['driver_name'], 0, 1);
}

if ($receipt['vehicle_details']) {
    $pdf->Cell(40, 8, 'Vehicle Details:', 0, 0);
    $pdf->Cell(150, 8, $receipt['vehicle_details'], 0, 1);
}

$pdf->Ln(5);

// Items
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Delivered Items', 0, 1);
$pdf->SetFont('helvetica', '', 10);

// Items table header
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(10, 10, '#', 1, 0, 'C', 1);
$pdf->Cell(80, 10, 'Item Description', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Quantity', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Unit', 1, 0, 'C', 1);
$pdf->Cell(60, 10, 'Remarks', 1, 1, 'C', 1);

// Items table rows
$pdf->SetFont('helvetica', '', 10);
$i = 0;
foreach ($items as $item) {
    $i++;
    $pdf->Cell(10, 10, $i, 1, 0, 'C');
    $pdf->Cell(80, 10, $item['item_name'], 1, 0, 'L');
    $pdf->Cell(20, 10, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(20, 10, $item['unit'], 1, 0, 'C');
    $pdf->Cell(60, 10, $item['remarks'], 1, 1, 'L');
}

$pdf->Ln(5);

// Notes
if ($receipt['notes']) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Notes', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 10, $receipt['notes'], 0, 'L');
    $pdf->Ln(5);
}

// Status
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Status: ', 0, 0);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, ucfirst($receipt['status']), 0, 1);

$pdf->Ln(20);

// Signature lines
$pdf->Line(20, $pdf->GetY(), 90, $pdf->GetY());
$pdf->Line(110, $pdf->GetY(), 180, $pdf->GetY());

$pdf->Ln(2);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 10, 'Delivered by', 0, 0, 'C');
$pdf->Cell(90, 10, 'Received by', 0, 1, 'C');

$pdf->Cell(90, 5, $receipt['driver_name'], 0, 0, 'C');
$pdf->Cell(90, 5, $receipt['received_by'], 0, 1, 'C');

// Output the PDF
$pdf->Output('Delivery_Receipt_' . $receipt['receipt_number'] . '.pdf', 'I');