<?php
// File path: controllers/agencies/update.php

require_once 'config/database.php';

// Base URL
$baseUrl = '/tekstore_quotation';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get agency ID
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        header("Location: {$baseUrl}/agencies");
        exit;
    }
    
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Perform validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Agency name is required";
    }
    
    // If no errors, proceed with update
    if (empty($errors)) {
        try {
            $query = "UPDATE agencies SET name = :name, address = :address, contact_person = :contact_person, phone = :phone, email = :email WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':contact_person', $contact_person);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Redirect to agencies list
            header("Location: {$baseUrl}/agencies");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // If there are errors, go back to the form
    if (!empty($errors)) {
        // Store errors and form data in session
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        
        // Redirect back to the edit form
        header("Location: {$baseUrl}/agencies/edit?id=$id");
        exit;
    }
}

// Redirect to agencies list if not POST request
header("Location: {$baseUrl}/agencies");
exit;
?>