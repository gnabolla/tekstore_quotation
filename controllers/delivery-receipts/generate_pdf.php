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

// Get delivery receipt items with pricing information from quotation items
$items = $db->query("
    SELECT dri.*, qi.unit_price, qi.final_price, qi.amount
    FROM delivery_receipt_items dri
    LEFT JOIN quotation_items qi ON dri.quotation_item_id = qi.id
    WHERE dri.delivery_receipt_id = ?
    ORDER BY dri.id ASC
", [$id])->fetchAll();

// Custom PDF class with header and footer
class MYPDF extends TCPDF {
    public $receipt_number;
    public $receipt_date;
    
    // Page header
    public function Header() {
        // Logo - You can add your logo here
        // $this->Image('path/to/logo.png', 15, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        // Set font
        $this->SetFont('helvetica', '', 10);
        
        // Draw a colored header background
        $this->SetFillColor(240, 240, 240);
        $this->Rect(0, 0, $this->getPageWidth(), 40, 'F');
        
        // Title with nice typography
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 10, 'DELIVERY RECEIPT', 0, false, 'C');
        
        // Company info below title
        $this->SetY(18);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 8, 'Tekstore Computer Parts and Accessories Trading', 0, false, 'C');
        
        $this->SetY(26);
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 6, 'Fast and Quality Business Solution', 0, false, 'C');
        
        $this->SetY(32);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 6, 'Magsaysay Street, Bantug, Roxas, Isabela | 09166027454 | tekstore.solution@gmail.com', 0, false, 'C');
        
        // Add a horizontal line
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, 40, $this->getPageWidth() - 10, 40);
        
        // Reset text color
        $this->SetTextColor(0, 0, 0);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-20);
        
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        
        // Draw line above footer
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY() - 2, $this->getPageWidth() - 10, $this->GetY() - 2);
        
        // Receipt number and date
        $this->Cell(0, 10, 'Receipt #: ' . $this->receipt_number . ' | Date: ' . $this->receipt_date, 0, false, 'L');
        
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R');
        
        // Add a note at the bottom
        $this->SetY(-15);
        $this->Cell(0, 10, 'NOTE: If you have any questions about this invoice, please contact 09166027454 | tekstore.solution@gmail.com', 0, false, 'C');
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

// Set receipt number and date for the footer
$pdf->receipt_number = $receipt['receipt_number'];
$pdf->receipt_date = date('F j, Y', strtotime($receipt['receipt_date']));

// Set default header and footer data
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins - adjusted to prevent overlapping
$pdf->SetMargins(15, 45, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// ---------------------------------------------------------
// RECEIPT DETAILS SECTION
// ---------------------------------------------------------

// Create a layout for receipt info
$pdf->SetY(45);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'RECEIPT INFORMATION', 0, 1, 'L', 0);

// Left column - Receipt Details
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 10, 'Receipt Details', 0, 0, 'L');

// Right column - Client Information
$pdf->Cell(95, 10, 'Client Information', 0, 1, 'L');

// Receipt Details Content
$pdf->SetFont('helvetica', '', 9);
$y_position = $pdf->GetY();
$left_column_text = "Receipt Number: " . $receipt['receipt_number'] . "\n" .
                    "Date: " . date('F j, Y', strtotime($receipt['receipt_date'])) . "\n" .
                    "Quotation #: " . $receipt['quote_number'] . "\n" .
                    "Status: " . ucfirst($receipt['status']) . "\n";

$pdf->MultiCell(90, 6, $left_column_text, 0, 'L', 0, 0, '', '', true, 0, false, true, 24);

// Client Information Content - Setting proper dimensions to avoid overlap
$right_column_text = "Name: " . $receipt['client_name'] . "\n" .
                    ($receipt['delivery_address'] ? "Address: " . $receipt['delivery_address'] . "\n" : "") .
                    ($receipt['contact_number'] ? "Contact: " . $receipt['contact_number'] . "\n" : "") .
                    ($receipt['received_by'] ? "Received By: " . $receipt['received_by'] . "\n" : "");

$pdf->SetXY(105, $y_position);
$pdf->MultiCell(90, 6, $right_column_text, 0, 'L', 0, 1, '', '', true, 0, false, true, 24);

// Make sure we have enough space before starting new section
$pdf->Ln(5);

// Two-column layout for Agency and Delivery information
$pdf->SetFont('helvetica', 'B', 10);

// Left column - Agency Information
$pdf->Cell(90, 8, 'Agency Information', 0, 0, 'L');

// Right column - Delivery Information
$pdf->Cell(95, 8, 'Delivery Information', 0, 1, 'L');

// Content for both columns
$y_position = $pdf->GetY();

// Left column - Agency content
$pdf->SetFont('helvetica', '', 9);
$agency_text = "";
if (!empty($receipt['agency_name'])) {
    $agency_text .= "Agency: " . $receipt['agency_name'] . "\n";
    if (!empty($receipt['agency_address'])) {
        $agency_text .= "Address: " . $receipt['agency_address'] . "\n";
    }
    if (!empty($receipt['contact_person'])) {
        $agency_text .= "Contact Person: " . $receipt['contact_person'] . "\n";
    }
} else {
    $agency_text = "No agency information available.\n";
}
$pdf->MultiCell(90, 6, $agency_text, 0, 'L', 0, 0, '', '', true, 0, false, true, 24);

// Right column - Delivery details
$pdf->SetXY(105, $y_position);
$delivery_info = "";
if (!empty($receipt['driver_name'])) {
    $delivery_info .= "Driver: " . $receipt['driver_name'] . "\n";
}
if (!empty($receipt['vehicle_details'])) {
    $delivery_info .= "Vehicle: " . $receipt['vehicle_details'] . "\n";
}
if (empty($delivery_info)) {
    $delivery_info = "No specific delivery information provided.\n";
}
$pdf->MultiCell(90, 6, $delivery_info, 0, 'L', 0, 1, '', '', true, 0, false, true, 24);

// Line break before items
$pdf->Ln(5);

// ---------------------------------------------------------
// ITEMS TABLE SECTION
// ---------------------------------------------------------
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'DELIVERED ITEMS', 0, 1, 'L', 0);

// Items table header with subtle styling
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(8, 10, '#', 1, 0, 'C', 1);
$pdf->Cell(65, 10, 'Item Description', 1, 0, 'L', 1);
$pdf->Cell(15, 10, 'Qty', 1, 0, 'C', 1);
$pdf->Cell(15, 10, 'Unit', 1, 0, 'C', 1);
$pdf->Cell(25, 10, 'Unit Price (P)', 1, 0, 'R', 1);
$pdf->Cell(25, 10, 'Amount (P)', 1, 0, 'R', 1);
$pdf->Cell(32, 10, 'Remarks', 1, 1, 'L', 1);

// Reset colors for table rows and use alternating row colors
$pdf->SetFillColor(245, 245, 245);
$pdf->SetFont('helvetica', '', 9);

$fill = false;
$i = 0;
$total_amount = 0;

foreach ($items as $item) {
    $i++;
    $unit_price = isset($item['final_price']) ? $item['final_price'] : 0;
    $amount = $unit_price * $item['quantity'];
    $total_amount += $amount;
    
    $pdf->Cell(8, 8, $i, 1, 0, 'C', $fill);
    $pdf->Cell(65, 8, $item['item_name'], 1, 0, 'L', $fill);
    $pdf->Cell(15, 8, $item['quantity'], 1, 0, 'C', $fill);
    $pdf->Cell(15, 8, $item['unit'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 8, number_format($unit_price, 2), 1, 0, 'R', $fill);
    $pdf->Cell(25, 8, number_format($amount, 2), 1, 0, 'R', $fill);
    $pdf->Cell(32, 8, $item['remarks'], 1, 1, 'L', $fill);
    $fill = !$fill; // Alternate row colors
}

// Add total row
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(103, 8, 'TOTAL', 1, 0, 'R', 1);
$pdf->Cell(25, 8, '', 0, 0, 'C', 0); // Empty cell for spacing
$pdf->Cell(25, 8, number_format($total_amount, 2), 1, 0, 'R', 1);
$pdf->Cell(32, 8, '', 0, 1, 'C', 0); // Empty cell for spacing

// Add notes if they exist
if (!empty($receipt['notes'])) {
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 10, 'Notes:', 0, 1);
    
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(0, 6, $receipt['notes'], 0, 'L');
}

// Add some space before signatures
$pdf->Ln(15);

// ---------------------------------------------------------
// SIGNATURE SECTION
// ---------------------------------------------------------
$pdf->SetDrawColor(150, 150, 150);

// Draw signature lines with better styling and adjusted positioning
$pdf->Line(30, $pdf->GetY(), 85, $pdf->GetY());
$pdf->Line(115, $pdf->GetY(), 170, $pdf->GetY());

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(85, 6, 'Delivered by', 0, 0, 'C');
$pdf->Cell(30, 6, '', 0, 0, 'C'); // Spacing between columns
$pdf->Cell(85, 6, 'Received by', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(85, 6, $receipt['driver_name'] ?: '', 0, 0, 'C');
$pdf->Cell(30, 6, '', 0, 0, 'C'); // Spacing between columns
$pdf->Cell(85, 6, $receipt['received_by'] ?: '', 0, 1, 'C');

$pdf->Cell(85, 4, 'Date: ________________', 0, 0, 'C');
$pdf->Cell(30, 4, '', 0, 0, 'C'); // Spacing between columns
$pdf->Cell(85, 4, 'Date: ________________', 0, 1, 'C');

// Add a signature verification note
$pdf->Ln(8);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 4, 'This receipt is valid without stamp.', 0, 1, 'C');
$pdf->Cell(0, 4, 'Thank you for your business!', 0, 1, 'C');

// Output the PDF
$pdf->Output('Delivery_Receipt_' . $receipt['receipt_number'] . '.pdf', 'I');