<?php
// File path: controllers/index.php

require 'Database.php';
$config = require 'config.php';

// Initialize the database
$db = new Database($config['database']);

// Get counts for dashboard
$agencyCount = $db->query("SELECT COUNT(*) as count FROM agencies")->fetch()['count'] ?? 0;
$quotationCount = $db->query("SELECT COUNT(*) as count FROM quotations")->fetch()['count'] ?? 0;
$pendingCount = $db->query("SELECT COUNT(*) as count FROM quotations WHERE status = 'pending'")->fetch()['count'] ?? 0;
$approvedCount = $db->query("SELECT COUNT(*) as count FROM quotations WHERE status = 'approved'")->fetch()['count'] ?? 0;

// Get recent quotations
$recentQuotations = $db->query("
    SELECT q.id, q.quote_number, q.quote_date, q.client_name, q.total, q.status, a.name as agency_name
    FROM quotations q
    LEFT JOIN agencies a ON q.agency_id = a.id
    ORDER BY q.created_at DESC
    LIMIT 5
")->fetchAll();

require 'views/index.view.php';