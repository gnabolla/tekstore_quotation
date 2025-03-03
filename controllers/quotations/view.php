<?php
// File path: controllers/quotations/view.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: {$baseUrl}/quotations?error=Quotation ID is required");
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
    header("Location: {$baseUrl}/quotations?error=Quotation not found");
    exit;
}

// Get quotation items
$items = $db->query("
    SELECT * FROM quotation_items 
    WHERE quotation_id = ?
    ORDER BY id ASC
", [$id])->fetchAll();

require 'views/quotations/view.view.php';