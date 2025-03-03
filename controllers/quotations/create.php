<?php
// File path: controllers/quotations/create.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Get all agencies for the dropdown
$agencies = $db->query("SELECT id, name FROM agencies ORDER BY name ASC")->fetchAll();

// Generate a new quote number (format: QUO-YYYYMMDD-XXX)
$today = date('Ymd');
$lastQuote = $db->query(
    "SELECT quote_number FROM quotations 
     WHERE quote_number LIKE ? 
     ORDER BY quote_number DESC LIMIT 1", 
    ["QUO-$today-%"]
)->fetch();

if ($lastQuote) {
    $lastNumber = (int) substr($lastQuote['quote_number'], -3);
    $newNumber = $lastNumber + 1;
} else {
    $newNumber = 1;
}

$quoteNumber = "QUO-$today-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

require 'views/quotations/create.view.php';