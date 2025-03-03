<?php
// File path: controllers/quotations/edit.php

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

// Get the quotation
$quotation = $db->query("SELECT * FROM quotations WHERE id = ?", [$id])->fetch();

// Check if quotation exists
if (!$quotation) {
    header("Location: {$baseUrl}/quotations?error=Quotation not found");
    exit;
}

// Get quotation items
$items = $db->query("SELECT * FROM quotation_items WHERE quotation_id = ? ORDER BY id ASC", [$id])->fetchAll();

// Get all agencies for the dropdown
$agencies = $db->query("SELECT id, name FROM agencies ORDER BY name ASC")->fetchAll();

require 'views/quotations/edit.view.php';