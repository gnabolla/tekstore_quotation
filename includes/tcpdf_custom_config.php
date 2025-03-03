<?php
// Check if TCPDF constants are already defined before including the config
if (!defined('PDF_PAGE_FORMAT')) {
    require_once(__DIR__ . '/../vendor/tecnickcom/tcpdf/config/tcpdf_config.php');
} else {
    // Constants are already defined, no need to include the config again
}
?>
