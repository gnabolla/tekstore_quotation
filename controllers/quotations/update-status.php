<?php
// File path: controllers/quotations/update-status.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (empty($_POST['id']) || empty($_POST['status'])) {
        header("Location: {$baseUrl}/quotations?error=Quotation ID and status are required");
        exit;
    }

    $id = (int) $_POST['id'];
    $status = $_POST['status'];

    // Validate status value
    $validStatuses = ['pending', 'approved', 'declined'];
    if (!in_array($status, $validStatuses)) {
        header("Location: {$baseUrl}/quotations/view?id=$id&error=Invalid status value");
        exit;
    }

    try {
        // Update the status
        $db->query(
            "UPDATE quotations SET status = ? WHERE id = ?",
            [$status, $id]
        );

        // Redirect with success message
        header("Location: {$baseUrl}/quotations/view?id=$id&success=Status updated successfully");
        exit;
    } catch (PDOException $e) {
        // Handle errors
        header("Location: {$baseUrl}/quotations/view?id=$id&error=Database error: " . $e->getMessage());
        exit;
    }
} else {
    // If not a POST request, redirect to quotations list
    header("Location: {$baseUrl}/quotations");
    exit;
}