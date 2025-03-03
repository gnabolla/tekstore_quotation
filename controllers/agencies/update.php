<?php
// File path: controllers/agencies/update.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($_POST['id']) || empty($_POST['name'])) {
        header("Location: {$baseUrl}/agencies?error=Agency ID and name are required");
        exit;
    }

    $id = (int) $_POST['id'];
    
    // Sanitize inputs
    $name = trim($_POST['name']);
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');

    try {
        // Update the agency
        $db->query(
            "UPDATE agencies 
             SET name = ?, address = ?, email = ?, contact_person = ?, contact_number = ? 
             WHERE id = ?",
            [$name, $address, $email, $contact_person, $contact_number, $id]
        );

        // Redirect with success message
        header("Location: {$baseUrl}/agencies?success=Agency updated successfully");
        exit;
    } catch (PDOException $e) {
        // Handle errors
        header("Location: {$baseUrl}/agencies/edit?id=" . $id . "&error=Database error: " . $e->getMessage());
        exit;
    }
} else {
    // If not a POST request, redirect to agencies list
    header("Location: {$baseUrl}/agencies");
    exit;
}