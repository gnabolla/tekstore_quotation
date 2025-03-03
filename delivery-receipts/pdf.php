<?php
// Start output buffering to prevent any output before headers
ob_start();

// Set error reporting to suppress warnings from being displayed
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include custom TCPDF config instead of the default one
require_once(__DIR__ . '/../includes/tcpdf_custom_config.php');
require_once(__DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php');

// Get the delivery receipt ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid delivery receipt ID");
}

// Your PDF generation code here
// ...

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('TekStore Quotation System');
$pdf->SetAuthor('TekStore');
$pdf->SetTitle('Delivery Receipt #' . $id);
$pdf->SetSubject('Delivery Receipt');
$pdf->SetKeywords('Delivery, Receipt, TekStore');

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Add your delivery receipt content here
// ...

// Clear any buffered output before sending the PDF
ob_end_clean();

// Output the PDF
$pdf->Output('delivery_receipt_' . $id . '.pdf', 'I');
exit;
?>
