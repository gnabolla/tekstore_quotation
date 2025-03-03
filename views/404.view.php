<?php
// File path: views/404.view.php

// Set base URL 
$baseUrl = '/tekstore_quotation';

include 'partials/head.php' 
?>

<div class="container mt-5 text-center">
    <h1 class="display-1">404</h1>
    <h2>Page Not Found</h2>
    <p class="lead">The page you are looking for does not exist.</p>
    <a href="<?= $baseUrl ?>/" class="btn btn-primary">Go Home</a>
</div>

<?php include 'partials/foot.php' ?>