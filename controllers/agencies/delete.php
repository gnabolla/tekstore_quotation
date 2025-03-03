<?php
// File path: controllers/agencies/delete.php

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

try {
    // Check if agency is used in any quotations
    $quotationCount = $db->query(
        "SELECT COUNT(*) as count FROM quotations WHERE agency_id = ?", 
        [$id]
    )->fetch()['count'];
    
    if ($quotationCount > 0) {
        header("Location: {$baseUrl}/agencies?error=Cannot delete agency because it is used in " . $quotationCount . " quotation(s)");
        exit;
    }
    
    // Delete the agency
    $db->query("DELETE FROM agencies WHERE id = ?", [$id]);
    
    // Redirect with success message
    header("Location: {$baseUrl}/agencies?success=Agency deleted successfully");
    exit;
} catch (PDOException $e) {
    // Handle errors
    header("Location: {$baseUrl}/agencies?error=Database error: " . $e->getMessage());
    exit;
}