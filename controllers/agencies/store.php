<?php
// File path: controllers/quotations/store.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required inputs
    if (empty($_POST['quote_number']) || empty($_POST['quote_date']) || empty($_POST['client_name'])) {
        header("Location: {$baseUrl}/quotations/create?error=Quote number, date, and client name are required");
        exit;
    }

    // Check if any items were submitted
    if (empty($_POST['items']) || !is_array($_POST['items'])) {
        header("Location: {$baseUrl}/quotations/create?error=At least one item is required");
        exit;
    }

    // Sanitize inputs
    $quoteNumber = trim($_POST['quote_number']);
    $quoteDate = trim($_POST['quote_date']);
    $agencyId = !empty($_POST['agency_id']) ? (int) $_POST['agency_id'] : null;
    $clientName = trim($_POST['client_name']);
    $clientEmail = trim($_POST['client_email'] ?? '');
    $clientAddress = trim($_POST['client_address'] ?? '');
    $budget = !empty($_POST['budget']) ? (float) $_POST['budget'] : null;
    $total = (float) $_POST['total_amount'];
    $notes = trim($_POST['notes'] ?? '');
    $status = 'pending'; // Default status for new quotations

    try {
        // Start transaction
        $db->connection->beginTransaction();

        // Insert quotation
        $db->query(
            "INSERT INTO quotations (
                quote_number, quote_date, agency_id, client_name, client_email, 
                client_address, budget, total, status, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $quoteNumber, $quoteDate, $agencyId, $clientName, $clientEmail,
                $clientAddress, $budget, $total, $status, $notes
            ]
        );

        // Get the last inserted ID
        $quotationId = $db->connection->lastInsertId();

        // Insert quotation items
        foreach ($_POST['items'] as $item) {
            // Skip empty items
            if (empty($item['item_name'])) {
                continue;
            }

            $itemName = trim($item['item_name']);
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $markupPercentage = (float) $item['markup_percentage'];
            $finalPrice = (float) $item['final_price'];
            $amount = (float) $item['amount'];

            $db->query(
                "INSERT INTO quotation_items (
                    quotation_id, item_name, quantity, unit_price, 
                    markup_percentage, final_price, amount
                ) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $quotationId, $itemName, $quantity, $unitPrice,
                    $markupPercentage, $finalPrice, $amount
                ]
            );
        }

        // Commit transaction
        $db->connection->commit();

        // Redirect with success message
        header("Location: {$baseUrl}/quotations?success=Quotation created successfully");
        exit;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $db->connection->rollBack();
        
        // Handle errors
        header("Location: {$baseUrl}/quotations/create?error=Database error: " . $e->getMessage());
        exit;
    }
} else {
    // If not a POST request, redirect to the form
    header("Location: {$baseUrl}/quotations/create");
    exit;
}