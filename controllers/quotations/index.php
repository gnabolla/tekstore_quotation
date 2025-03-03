<?php
// File path: controllers/quotations/index.php

require 'Database.php';
$config = require 'config.php';

// Initialize the database
$db = new Database($config['database']);

// Get all quotations with agency names
$quotations = $db->query("
    SELECT q.*, a.name as agency_name
    FROM quotations q
    LEFT JOIN agencies a ON q.agency_id = a.id
    ORDER BY q.created_at DESC
")->fetchAll();

require 'views/quotations/index.view.php';