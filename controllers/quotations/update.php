<?php
// File path: controllers/quotations/update.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required inputs
    if (empty($_POST['id']) || empty($_POST['quote_number']) || empty($_POST['quote_date']) || empty($_POST['client_name'])) {
        header("Location: {$baseUrl}/quotations?error=Quotation ID, quote number, date, and client name are required");
        exit;
    }

    // Check if any items were submitted
    if (empty($_POST['items']) || !is_array($_POST['items'])) {
        header("Location: {$baseUrl}/quotations/edit?id=" . $_POST['id'] . "&error=At least one item is required");
        exit;
    }

    $id = (int) $_POST['id'];

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
    $status = $_POST['status'] ?? 'pending';

    try {
        // Start transaction
        $db->connection->beginTransaction();

        // Update quotation
        $db->query(
            "UPDATE quotations SET 
                quote_date = ?, agency_id = ?, client_name = ?, client_email = ?, 
                client_address = ?, budget = ?, total = ?, status = ?, notes = ?
            WHERE id = ?",
            [
                $quoteDate, $agencyId, $clientName, $clientEmail,
                $clientAddress, $budget, $total, $status, $notes, $id
            ]
        );

        // Get existing item IDs to determine which to delete
        $existingItemIds = $db->query("SELECT id FROM quotation_items WHERE quotation_id = ?", [$id])->fetchAll(PDO::FETCH_COLUMN);
        $updatedItemIds = [];

        // Update or insert quotation items
        foreach ($_POST['items'] as $item) {
            // Skip empty items
            if (empty($item['item_name'])) {
                continue;
            }

            $itemId = !empty($item['id']) ? (int) $item['id'] : null;
            $itemName = trim($item['item_name']);
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $markupPercentage = (float) $item['markup_percentage'];
            $finalPrice = (float) $item['final_price'];
            $amount = (float) $item['amount'];

            if ($itemId && in_array($itemId, $existingItemIds)) {
                // Update existing item
                $db->query(
                    "UPDATE quotation_items SET 
                        item_name = ?, quantity = ?, unit_price = ?, 
                        markup_percentage = ?, final_price = ?, amount = ?
                    WHERE id = ? AND quotation_id = ?",
                    [
                        $itemName, $quantity, $unitPrice,
                        $markupPercentage, $finalPrice, $amount,
                        $itemId, $id
                    ]
                );
                $updatedItemIds[] = $itemId;
            } else {
                // Insert new item
                $db->query(
                    "INSERT INTO quotation_items (
                        quotation_id, item_name, quantity, unit_price, 
                        markup_percentage, final_price, amount
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $id, $itemName, $quantity, $unitPrice,
                        $markupPercentage, $finalPrice, $amount
                    ]
                );
            }
        }

        // Delete items that were removed
        $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
        if (!empty($itemsToDelete)) {
            $placeholders = implode(',', array_fill(0, count($itemsToDelete), '?'));
            $db->query(
                "DELETE FROM quotation_items WHERE id IN ($placeholders)",
                $itemsToDelete
            );
        }

        // Commit transaction
        $db->connection->commit();

        // Redirect with success message
        header("Location: {$baseUrl}/quotations/view?id=" . $id . "&success=Quotation updated successfully");
        exit;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $db->connection->rollBack();
        
        // Handle errors
        header("Location: {$baseUrl}/quotations/edit?id=" . $id . "&error=Database error: " . $e->getMessage());
        exit;
    }
} else {
    // If not a POST request, redirect to quotations list
    header("Location: {$baseUrl}/quotations");
    exit;
}