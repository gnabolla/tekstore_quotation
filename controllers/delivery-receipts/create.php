<?php
// File path: controllers/delivery-receipts/create.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if quotation ID is provided
if (!isset($_GET['quotation_id']) || empty($_GET['quotation_id'])) {
    header("Location: {$baseUrl}/quotations?error=Quotation ID is required");
    exit;
}

$quotationId = (int) $_GET['quotation_id'];

// Get the quotation with agency info
$quotation = $db->query("
    SELECT q.*, a.name as agency_name, a.address as agency_address, a.contact_person, 
           a.contact_number, a.email as agency_email
    FROM quotations q
    LEFT JOIN agencies a ON q.agency_id = a.id
    WHERE q.id = ?
", [$quotationId])->fetch();

// Check if quotation exists and is approved
if (!$quotation) {
    header("Location: {$baseUrl}/quotations?error=Quotation not found");
    exit;
}

if ($quotation['status'] !== 'approved') {
    header("Location: {$baseUrl}/quotations/view?id={$quotationId}&error=Only approved quotations can have delivery receipts");
    exit;
}

// Get quotation items
$items = $db->query("
    SELECT * FROM quotation_items 
    WHERE quotation_id = ?
    ORDER BY id ASC
", [$quotationId])->fetchAll();

// Generate a new receipt number (format: DR-YYYYMMDD-XXX)
$today = date('Ymd');
$lastReceipt = $db->query(
    "SELECT receipt_number FROM delivery_receipts 
     WHERE receipt_number LIKE ? 
     ORDER BY receipt_number DESC LIMIT 1", 
    ["DR-$today-%"]
)->fetch();

if ($lastReceipt) {
    $lastNumber = (int) substr($lastReceipt['receipt_number'], -3);
    $newNumber = $lastNumber + 1;
} else {
    $newNumber = 1;
}

$receiptNumber = "DR-$today-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

require 'views/delivery-receipts/create.view.php';