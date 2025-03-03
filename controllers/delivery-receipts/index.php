<?php
// File path: controllers/delivery-receipts/index.php

require 'Database.php';
$config = require 'config.php';

// Initialize the database
$db = new Database($config['database']);

// Get all delivery receipts with quotation and agency names
$deliveryReceipts = $db->query("
    SELECT dr.*, q.quote_number, q.client_name, a.name as agency_name
    FROM delivery_receipts dr
    JOIN quotations q ON dr.quotation_id = q.id
    LEFT JOIN agencies a ON q.agency_id = a.id
    ORDER BY dr.created_at DESC
")->fetchAll();

require 'views/delivery-receipts/index.view.php';