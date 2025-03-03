<?php
// File path: C:\xampp\htdocs\tekstore_quotation\direct-index.php

// This file serves as a fallback to see if the server can execute PHP files directly
echo "<h1>Tekstore Quotation System - Direct Access Test</h1>";
echo "<p>If you can see this message, PHP is working correctly but there might be an issue with your routing configuration.</p>";

echo "<h2>Environment Information:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server Software: " . $_SERVER["SERVER_SOFTWARE"] . "</li>";
echo "<li>Document Root: " . $_SERVER["DOCUMENT_ROOT"] . "</li>";
echo "<li>Script Filename: " . $_SERVER["SCRIPT_FILENAME"] . "</li>";
echo "<li>Request URI: " . $_SERVER["REQUEST_URI"] . "</li>";
echo "</ul>";

echo "<h2>Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li>Check if .htaccess is being processed (mod_rewrite should be enabled)</li>";
echo "<li>Verify file paths and permissions in your routing configuration</li>";
echo "<li>Ensure all PHP files have the correct syntax without parse errors</li>";
echo "<li>Check that the database configuration is correct and the database exists</li>";
echo "</ol>";

// Test Database Connection
echo "<h2>Database Connection Test:</h2>";
try {
    // Trying to include the Database class
    if (file_exists('Database.php')) {
        require_once 'Database.php';
        echo "<p>Database.php file found.</p>";
        
        if (file_exists('config.php')) {
            $config = require 'config.php';
            echo "<p>config.php file found.</p>";
            
            try {
                $db = new Database($config['database']);
                echo "<p style='color:green;'>Database connection successful!</p>";
            } catch (Exception $e) {
                echo "<p style='color:red;'>Database connection failed: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color:red;'>config.php file not found.</p>";
        }
    } else {
        echo "<p style='color:red;'>Database.php file not found.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

// List key files
echo "<h2>File Availability Check:</h2>";
$criticalFiles = [
    '.htaccess',
    'index.php',
    'router.php',
    'functions.php',
    'Database.php',
    'config.php',
    'controllers/index.php',
    'controllers/404.php',
    'views/index.view.php',
    'views/404.view.php',
    'views/partials/head.php',
    'views/partials/foot.php'
];

echo "<ul>";
foreach ($criticalFiles as $file) {
    echo "<li>" . $file . ": " . (file_exists($file) ? "<span style='color:green;'>Found</span>" : "<span style='color:red;'>Not Found</span>") . "</li>";
}
echo "</ul>";

echo "<p><a href='phpinfo.php'>View PHP Configuration (phpinfo)</a></p>";