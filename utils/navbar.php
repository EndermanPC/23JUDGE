<?php
require_once 'auth.php';

$name = isset($_COOKIE['username']) ? decrypt(htmlspecialchars($_COOKIE['username'])) : null;
$pass = isset($_COOKIE['password']) ? decrypt(htmlspecialchars($_COOKIE['password'])) : null;

$activatedPage = basename($_SERVER['SCRIPT_NAME']);

function isActive($page) {
    global $activatedPage;
    return $activatedPage === $page ? 'class="active"' : '';
}
?>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="assets/nprogress.css">
</head>

<script src="assets/nprogress.js"></script>
<script>
    NProgress.configure({ showSpinner: false });
    NProgress.configure({ speed: 500 });

    document.addEventListener("DOMContentLoaded", function() {
        NProgress.start();
    });

    window.addEventListener("load", function() {
        NProgress.done();
    });
</script>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/index.php">23JUDGE</a>
        </div>
        <ul class="nav navbar-nav">
            <li <?= isActive('index.php') . ' ' . isActive('problem.php') ?>><a href="/index.php">Problems</a></li>
            <li <?= isActive('contest.php') ?>><a href="/index.php?view=contests">Contests</a></li>
            <li <?= isActive('rank.php') ?>><a href="/rank.php">Ranking</a></li>
            <li <?= isActive('submissions.php') ?>><a href="/submissions.php">Submissions</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <?php if (!$name && !$pass): ?>
            <li><a href="https://forms.gle/mkkWbPTxZRtRJBhK6"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
            <li><a href="/login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
            <?php else: ?>
            <li><a><span class="glyphicon glyphicon-user"></span> <?= $name ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<br><br><br>