<?php
// File path: controllers/agencies/index.php

require 'Database.php';
$config = require 'config.php';

// Initialize the database
$db = new Database($config['database']);

// Get all agencies
$agencies = $db->query("SELECT * FROM agencies ORDER BY name ASC")->fetchAll();

require 'views/agencies/index.view.php';