<?php
// File path: controllers/quotations/delete.php

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

try {
    // Start transaction
    $db->connection->beginTransaction();
    
    // Delete all quotation items first
    $db->query("DELETE FROM quotation_items WHERE quotation_id = ?", [$id]);
    
    // Then delete the quotation
    $db->query("DELETE FROM quotations WHERE id = ?", [$id]);
    
    // Commit transaction
    $db->connection->commit();
    
    // Redirect with success message
    header("Location: {$baseUrl}/quotations?success=Quotation deleted successfully");
    exit;
} catch (PDOException $e) {
    // Rollback transaction on error
    $db->connection->rollBack();
    
    // Handle errors
    header("Location: {$baseUrl}/quotations?error=Database error: " . $e->getMessage());
    exit;
}