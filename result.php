<?php
require 'utils/security.php';
require 'utils/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'utils/env.php';
    
    $htmlContent = $_POST['json'];
    $filePath = urldecode($_POST['path']);
    file_put_contents($filePath, $htmlContent);
}
?>
