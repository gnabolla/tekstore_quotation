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
    SELECT dri.*, qi.unit_price, qi.final_price 
    FROM delivery_receipt_items dri
    LEFT JOIN quotation_items qi ON dri.quotation_item_id = qi.id
    WHERE dri.delivery_receipt_id = ?
    ORDER BY dri.id ASC
", [$id])->fetchAll();

// Create new PDF document
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Empty header
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
$pdf->SetAuthor('Tekstore Computer Parts and Accessories Trading');
$pdf->SetTitle('Delivery Receipt #' . $receipt['receipt_number']);
$pdf->SetSubject('Delivery Receipt');

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 16);

// Title
$pdf->Cell(0, 10, 'DELIVERY RECEIPT', 0, 1, 'C');
$pdf->Ln(5);

// Company information
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Tekstore Computer Parts and Accessories Trading', 0, 1, 'L');
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 5, 'Fast and Quality Business Solution', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Magsaysay Street, Bantug, Roxas, Isabela', 0, 1, 'L');
$pdf->Cell(0, 5, '09166027454', 0, 1, 'L');
$pdf->Cell(0, 5, 'tekstore.solution@gmail.com', 0, 1, 'L');
$pdf->Ln(5);

// Receipt Details and Customer Details in a table
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(95, 7, 'Payment for:', 1, 0, 'L', 1);
$pdf->Cell(95, 7, $receipt['payment_for'] ?? '', 1, 1, 'L', 0);

// Receipt details table
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 7, 'Receipt No:', 1, 0, 'L', 1);
$pdf->Cell(65, 7, $receipt['receipt_number'], 1, 0, 'L');
$pdf->Cell(30, 7, 'Date:', 1, 0, 'L', 1);
$pdf->Cell(65, 7, date('d-M-Y', strtotime($receipt['receipt_date'])), 1, 1, 'L');

// Customer details section
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(95, 7, 'Customer Details:', 1, 0, 'L', 1);
$pdf->Cell(95, 7, '', 1, 1, 'L', 0);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 7, 'Name:', 1, 0, 'L', 1);
$pdf->Cell(65, 7, $receipt['client_name'], 1, 0, 'L');
$pdf->Cell(30, 7, 'Address:', 1, 0, 'L', 1);
$pdf->Cell(65, 7, $receipt['delivery_address'] ?? '', 1, 1, 'L');

$pdf->Cell(30, 7, 'Phone No:', 1, 0, 'L', 1);
$pdf->Cell(65, 7, $receipt['contact_number'] ?? '', 1, 0, 'L');
$pdf->Cell(30, 7, '', 1, 0, 'L', 1);
$pdf->Cell(65, 7, '', 1, 1, 'L');

$pdf->Ln(5);

// Items Table
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(15, 7, 'No.', 1, 0, 'C', 1);
$pdf->Cell(75, 7, 'Description', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Price per Package', 1, 0, 'C', 1);
$pdf->Cell(70, 7, 'TOTAL', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 10);

// Print items
$i = 0;
foreach ($items as $item) {
    $i++;
    $price = $item['unit_price'] ?? $item['final_price'] ?? 0;
    $quantity = $item['quantity'];
    $amount = $item['total_price'] ?? ($price * $quantity);
    
    $pdf->Cell(15, 7, $quantity, 1, 0, 'C');
    $pdf->Cell(75, 7, $item['item_name'], 1, 0, 'L');
    $pdf->Cell(30, 7, '₱' . number_format($price, 2), 1, 0, 'R');
    $pdf->Cell(70, 7, '₱' . number_format($amount, 2), 1, 1, 'R');
}

// Fill empty rows to match the template
for ($j = $i; $j < 10; $j++) {
    $pdf->Cell(15, 7, '', 1, 0, 'C');
    $pdf->Cell(75, 7, '', 1, 0, 'L');
    $pdf->Cell(30, 7, '', 1, 0, 'R');
    $pdf->Cell(70, 7, '', 1, 1, 'R');
}

// Totals section
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(120, 7, 'Subtotal', 1, 0, 'R', 1);
$pdf->Cell(70, 7, '₱' . number_format($receipt['subtotal'] ?? 0, 2), 1, 1, 'R');

$pdf->Cell(120, 7, 'Tax', 1, 0, 'R', 1);
$pdf->Cell(70, 7, '₱' . number_format($receipt['tax_amount'] ?? 0, 2), 1, 1, 'R');

$pdf->Cell(120, 7, 'TOTAL', 1, 0, 'R', 1);
$pdf->Cell(70, 7, '₱' . number_format($receipt['total_amount'] ?? 0, 2), 1, 1, 'R');

// Notes
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'NOTE: ' . ($receipt['notes'] ?? 'If you have any questions about this receipt, please contact 09166027454 | tekstore.solution@gmail.com'), 0, 1, 'L');

// Signature section
$pdf->Ln(10);
$pdf->Cell(95, 0, 'Signature:', 0, 0, 'L');
$pdf->Cell(95, 0, '', 0, 1, 'L');
$pdf->Ln(5);
$pdf->Cell(95, 0, '', 'B', 0, 'L');
$pdf->Cell(95, 0, '', 0, 1, 'L');

$pdf->Ln(5);
$pdf->Cell(95, 0, 'Date:', 0, 0, 'L');
$pdf->Cell(95, 0, '', 0, 1, 'L');
$pdf->Ln(5);
$pdf->Cell(95, 0, '', 'B', 0, 'L');
$pdf->Cell(95, 0, '', 0, 1, 'L');

$pdf->Ln(15);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 0, 'Thank you for your business!', 0, 1, 'C');

// Output the PDF
$pdf->Output('Delivery_Receipt_' . $receipt['receipt_number'] . '.pdf', 'I');