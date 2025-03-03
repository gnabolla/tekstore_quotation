<?php
// File path: controllers/agencies/edit.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: {$baseUrl}/agencies?error=Agency ID is required");
    exit;
}

$id = (int) $_GET['id'];

// Get the agency by ID
$agency = $db->query("SELECT * FROM agencies WHERE id = ?", [$id])->fetch();

// Check if agency exists
if (!$agency) {
    header("Location: {$baseUrl}/agencies?error=Agency not found");
    exit;
}

require 'views/agencies/edit.view.php';