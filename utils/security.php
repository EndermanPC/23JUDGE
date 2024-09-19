<?php
session_start();

$timeFrame = 5;
$requestLimit = 5;

$ipAddress = $_SERVER['REMOTE_ADDR'];
$currentTime = time();

if (!isset($_SESSION['request_counts'])) {
    $_SESSION['request_counts'] = [];
}

foreach ($_SESSION['request_counts'] as $time => $count) {
    if ($time < $currentTime - $timeFrame) {
        unset($_SESSION['request_counts'][$time]);
    }
}

if (!isset($_SESSION['request_counts'][$currentTime])) {
    $_SESSION['request_counts'][$currentTime] = 0;
}
$_SESSION['request_counts'][$currentTime]++;

$totalRequests = 0;
foreach ($_SESSION['request_counts'] as $count) {
    $totalRequests += $count;
}

if ($totalRequests > $requestLimit) {
    header('HTTP/1.1 429 Too Many Requests');
    die('Too many requests. Please try again later.');
}
?>
