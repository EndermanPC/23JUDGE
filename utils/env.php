<?php
if (!defined('PHP_EOL')) {
    define('PHP_EOL', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "\r\n" : "\n");
}

if (isset($_COOKIE['contest'])) {
    chdir("contests" . DIRECTORY_SEPARATOR . decrypt($_COOKIE['contest']));
}
?>