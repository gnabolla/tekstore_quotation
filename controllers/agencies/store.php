<?php
// File path: controllers/agencies/store.php

require 'Database.php';
$config = require 'config.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Initialize the database
$db = new Database($config['database']);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($_POST['name'])) {
        header("Location: {$baseUrl}/agencies/create?error=Agency name is required");
        exit;
    }

    // Sanitize inputs
    $name = trim($_POST['name']);
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');

    try {
        // Insert the new agency
        $db->query(
            "INSERT INTO agencies (name, address, email, contact_person, contact_number) 
             VALUES (?, ?, ?, ?, ?)",
            [$name, $address, $email, $contact_person, $contact_number]
        );

        // Redirect with success message
        header("Location: {$baseUrl}/agencies?success=Agency created successfully");
        exit;
    } catch (PDOException $e) {
        // Handle errors
        header("Location: {$baseUrl}/agencies/create?error=Database error: " . $e->getMessage());
        exit;
    }
} else {
    // If not a POST request, redirect to the form
    header("Location: {$baseUrl}/agencies/create");
    exit;
}