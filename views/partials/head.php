<?php
// File path: views/partials/head.php
// Define the base URL for use throughout the application
$baseUrl = '/tekstore_quotation';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tekstore Quotation System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #343a40;
        }

        .sidebar a {
            color: #f8f9fa;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar a.active {
            background-color: #0d6efd;
        }

        .content {
            padding: 20px;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .navbar-brand small {
            font-size: 12px;
            display: block;
            line-height: 1.2;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $baseUrl ?>/">
                Tekstore
                <small>Computer Parts and Accessories Trading</small>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/quotations">Quotations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/delivery-receipts">Delivery Receipts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/agencies">Agencies</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar pt-3">
                <a href="<?= $baseUrl ?>/" class="<?= strpos($uri, '/') === 0 && $uri === '/' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="<?= $baseUrl ?>/quotations" class="<?= strpos($uri, '/quotations') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Quotations
                </a>
                <a href="<?= $baseUrl ?>/delivery-receipts" class="<?= strpos($uri, '/delivery-receipts') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-truck"></i> Delivery Receipts
                </a>
                <a href="<?= $baseUrl ?>/agencies" class="<?= strpos($uri, '/agencies') === 0 ? 'active' : '' ?>">
                    <i class="fas fa-building"></i> Agencies
                </a>
            </div>
            <div class="col-md-10 content">
                <!-- Content will go here -->