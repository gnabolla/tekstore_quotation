<?php
// File path: controllers/delivery-receipts/store.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required inputs
    if (empty($_POST['receipt_number']) || empty($_POST['receipt_date']) || empty($_POST['quotation_id'])) {
        header("Location: {$baseUrl}/delivery-receipts/create?quotation_id=".$_POST['quotation_id']."&error=Receipt number, date, and quotation ID are required");
        exit;
    }

    // Check if any items were submitted
    if (empty($_POST['items']) || !is_array($_POST['items'])) {
        header("Location: {$baseUrl}/delivery-receipts/create?quotation_id=".$_POST['quotation_id']."&error=At least one item is required");
        exit;
    }

    // Sanitize inputs
    $receiptNumber = trim($_POST['receipt_number']);
    $receiptDate = trim($_POST['receipt_date']);
    $quotationId = (int) $_POST['quotation_id'];
    $deliveryAddress = trim($_POST['delivery_address'] ?? '');
    $receivedBy = trim($_POST['received_by'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $driverName = trim($_POST['driver_name'] ?? '');
    $vehicleDetails = trim($_POST['vehicle_details'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $status = 'pending'; // Default status for new delivery receipts

    try {
        // Start transaction
        $db->connection->beginTransaction();

        // Insert delivery receipt
        $db->query(
            "INSERT INTO delivery_receipts (
                receipt_number, receipt_date, quotation_id, delivery_address, 
                received_by, contact_number, driver_name, vehicle_details, status, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $receiptNumber, $receiptDate, $quotationId, $deliveryAddress,
                $receivedBy, $contactNumber, $driverName, $vehicleDetails, $status, $notes
            ]
        );

        // Get the last inserted ID
        $deliveryReceiptId = $db->connection->lastInsertId();

        // Insert delivery receipt items
        foreach ($_POST['items'] as $item) {
            // Skip empty items
            if (empty($item['item_name']) || (int)$item['quantity'] <= 0) {
                continue;
            }

            $quotationItemId = !empty($item['quotation_item_id']) ? (int) $item['quotation_item_id'] : null;
            $itemName = trim($item['item_name']);
            $quantity = (int) $item['quantity'];
            $unit = trim($item['unit'] ?? '');
            $remarks = trim($item['remarks'] ?? '');

            $db->query(
                "INSERT INTO delivery_receipt_items (
                    delivery_receipt_id, quotation_item_id, item_name, quantity, unit, remarks
                ) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $deliveryReceiptId, $quotationItemId, $itemName, $quantity, $unit, $remarks
                ]
            );
        }

        // Commit transaction
        $db->connection->commit();

        // Redirect with success message
        header("Location: {$baseUrl}/delivery-receipts/view?id={$deliveryReceiptId}&success=Delivery receipt created successfully");
        exit;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $db->connection->rollBack();
        
        // Handle errors
        header("Location: {$baseUrl}/delivery-receipts/create?quotation_id={$quotationId}&error=Database error: " . $e->getMessage());
        exit;
    }
} else {
    // If not a POST request, redirect to the index
    header("Location: {$baseUrl}/delivery-receipts");
    exit;
}