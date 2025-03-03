<?php
// File path: controllers/agencies/edit.php

require_once 'config/database.php';

// Get agency ID from query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Set page title
$pageTitle = 'Edit Agency';

// Fetch agency data
$agency = null;
if ($id > 0) {
    try {
        $query = "SELECT * FROM agencies WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $agency = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$agency) {
            // Agency not found
            header("Location: /tekstore_quotation/agencies");
            exit;
        }
    } catch (PDOException $e) {
        // Handle error
        $errorMessage = "Database error: " . $e->getMessage();
    }
}

// Include the view file
require 'views/agencies/edit.view.php';