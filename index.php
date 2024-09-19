<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ITK23 Online Judge</title>
    <link rel="icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<?php
require 'utils/security.php';
require 'utils/auth.php';

error_reporting(E_ERROR | E_PARSE);

$name = isset($_COOKIE['username']) ? decrypt(htmlspecialchars($_COOKIE['username'])) : null;
$pass = isset($_COOKIE['password']) ? decrypt(htmlspecialchars($_COOKIE['password'])) : null;
if (!auth($name, $pass)) {
    header("Location: login.php");
}

require 'utils/env.php';

$problemDirs = glob('problems' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
$contestDirs = glob('contests' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

if ($problemDirs === false) {
    die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Failed to read the problem directories.</div>');
}

$ACSubmissions = [];
$userDir = 'submissions' . DIRECTORY_SEPARATOR . '' . htmlspecialchars(basename($name));

if (!empty($name) && is_dir($userDir)) {
    $filePath = $userDir . DIRECTORY_SEPARATOR . "ACSubmissions.txt";

    if (file_exists($filePath)) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Failed to read the submissions file.</div>');
        }

        foreach ($lines as $line) {
            $ACSubmissions[] = $line;
        }
    }
} else {
    header("Location: login.php");
    die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Invalid username or user directory does not exist.</div>');
}

$resultsPerPage = 10;
$totalDirs = array_merge($contestDirs, $problemDirs);
$totalResults = count($totalDirs);
$totalPages = ceil($totalResults / $resultsPerPage);

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($currentPage - 1) * $resultsPerPage;

$paginatedDirs = array_slice($totalDirs, $start, $resultsPerPage);

$viewMode = "problems";
?>
<body>
    <?php include 'utils/navbar.php'; ?>

    <div class="w3-container">
        <?php if ($_GET['view'] === 'contests'): ?>
            <?php if (isset($_COOKIE['contest'])): ?>
                <h1 class="w3-center"><?php echo decrypt($_COOKIE['contest']); ?></h1>
            <?php else: ?>
                <h1 class="w3-center">Contests</h1>
                <?php $viewMode = "contests"; ?>
            <?php endif; ?>
        <?php elseif ($_GET['view'] !== 'contests'): ?>
            <h1 class="w3-center">Problems</h1>
        <?php endif; ?>
        
        <?php
        if (isset($_COOKIE['contest'])) {
            echo '<form action="/contest.php" method="POST">';
            echo '<input type="hidden" name="contest" value="leave">';
            echo '<button class="w3-button w3-green w3-round" type="submit">Leave Contest</button>';
            echo '</form><br><br>';
        }
        ?>
        <ul class="w3-ul w3-border w3-round">
            <?php if (!empty($paginatedDirs)): ?>
                <?php foreach ($paginatedDirs as $dir): ?>
                    <?php if (strpos($dir, 'contests') !== false && $viewMode === "contests"): ?>
                        <li>
                            <div class="w3-bar w3-border w3-round w3-light-gray">
                                <a class="w3-bar-item w3-button" href="contest.php?contest=<?php echo urlencode(basename($dir)); ?>">
                                    <?php echo basename($dir); ?>
                                </a>
                                <span class='w3-bar-item w3-right' style='text-align: right;'><?php echo file_get_contents($dir . DIRECTORY_SEPARATOR . "contest.cfg"); ?></span>
                            </div>
                        </li>
                    <?php elseif (strpos($dir, 'problems') !== false && $viewMode === "problems"): ?>
                        <li>
                            <div class="w3-bar w3-border w3-round w3-light-gray">
                                <a class="w3-bar-item w3-button <?php if (in_array(basename($dir), $ACSubmissions)) echo "w3-green"; ?>" href="problem.php?problem=<?php echo urlencode(basename($dir)); ?>">
                                    <?php echo basename($dir); if (in_array(basename($dir), $ACSubmissions)) echo "&nbsp;✔"; ?>
                                </a>
                                <span class='w3-bar-item w3-right' style='text-align: right;'><?php echo file_get_contents($dir . DIRECTORY_SEPARATOR . "RScore.cfg"); ?>&nbsp;R-Score</span>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Nothings available.</li>
            <?php endif; ?>
        </ul>
        
        <br>

        <div class="w3-bar w3-center">
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <a href="?page=<?php echo $page; ?>" class="w3-button w3-border w3-round <?php echo $page == $currentPage ? 'w3-blue' : ''; ?>">
                    <?php echo $page; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
