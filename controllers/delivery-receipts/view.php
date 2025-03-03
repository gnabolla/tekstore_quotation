<?php
// File path: controllers/delivery-receipts/view.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: {$baseUrl}/delivery-receipts?error=Delivery Receipt ID is required");
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
    header("Location: {$baseUrl}/delivery-receipts?error=Delivery Receipt not found");
    exit;
}

// Get delivery receipt items
$items = $db->query("
    SELECT dri.*, qi.markup_percentage, qi.final_price, qi.amount
    FROM delivery_receipt_items dri
    LEFT JOIN quotation_items qi ON dri.quotation_item_id = qi.id
    WHERE dri.delivery_receipt_id = ?
    ORDER BY dri.id ASC
", [$id])->fetchAll();

require 'views/delivery-receipts/view.view.php';