<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submissions</title>
    <link rel="icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">
    <?php include 'utils/navbar.php'; ?>
    
    <div class="w3-container w3-margin">
        <h1 class="w3-center">Submissions Log</h1>

        <?php
        require 'utils/security.php';

        error_reporting(E_ERROR | E_PARSE);

        if (isset($_COOKIE['contest'])) {
            chdir("contests" . DIRECTORY_SEPARATOR . decrypt($_COOKIE['contest']));
            $realSubmissionsPath = "contests" . DIRECTORY_SEPARATOR . decrypt($_COOKIE['contest']) . DIRECTORY_SEPARATOR . "submissions";
        } else {
            $realSubmissionsPath = "submissions";
        }

        $file = fopen('submissions/Log.txt', 'r');

        if ($file) {
            $entries = [];

            while (($line = fgets($file)) !== false) {
                $line = trim($line);

                list($sec, $min, $hour, $day, $month, $year, $problem, $name, $score) = explode(' ', $line);

                $entries[] = [
                    'time' => sprintf('%04d%02d%02d%02d%02d%02d', $year, $month, $day, $hour, $min, $sec),
                    'sec' => $sec,
                    'min' => $min,
                    'hour' => $hour,
                    'day' => $day,
                    'month' => $month,
                    'year' => $year,
                    'problem' => $problem,
                    'name' => $name,
                    'score' => $score
                ];
            }

            fclose($file);

            usort($entries, function($a, $b) {
                return $b['time'] <=> $a['time'];
            });

            $limit = 10;
            $totalEntries = count($entries);
            $totalPages = ceil($totalEntries / $limit);

            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            if ($currentPage > $totalPages) $currentPage = $totalPages;

            $start = ($currentPage - 1) * $limit;
            $end = min($start + $limit, $totalEntries);

            for ($i = $start; $i < $end; $i++) {
                $entry = $entries[$i];
                $submitTime = $entry['sec'] . " " . $entry['min'] . " " . $entry['hour'] . " " . $entry['day'] . " " . $entry['month'] . " " . $entry['year'];

                echo "<div class='w3-card w3-margin w3-white w3-round'>";
                echo "<ul class='w3-ul'>";
                echo "<li class='w3-padding-16' style='display: flex; justify-content: space-between;'>
                        <span style='flex: 1; text-align: left;'>&nbsp;&nbsp;<a class='w3-text-blue' href='judge.php?view=" . encrypt($realSubmissionsPath . "/" . $entry['name'] . "/" . $submitTime . " " . $entry['problem'] . ".txt") . "'>" . htmlspecialchars($entry['problem']) . "</a></span>
                        <span style='flex: 2; text-align: center'><b>" . htmlspecialchars($entry['name']) . "</b></span>
                        <span style='flex: 1; text-align: right;'>" . htmlspecialchars($entry['score']) . "&nbsp;&nbsp;</span>
                      </li>";
                echo "</ul>";
                echo "</div>";
            }
        ?>
            <div class="w3-bar w3-center">
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <a href="?page=<?php echo $page; ?>" class="w3-button w3-border w3-round <?php echo $page == $currentPage ? 'w3-blue' : ''; ?>">
                    <?php echo $page; ?>
                </a>
            <?php endfor; ?>
            </div>
        
        <?php
        } else {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Unable to open the log file.</div>');
        }
        ?>
    </div>
</body>
</html>
