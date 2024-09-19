<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users Ranking</title>
    <link rel="icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">
    <?php include 'utils/navbar.php'; ?>

    <div class="w3-container w3-margin">
        <h1 class="w3-center">Ranking</h1>
        <?php
        require 'utils/security.php';

        error_reporting(E_ERROR | E_PARSE);

        require 'utils/env.php';

        $resultsPerPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $files = glob("users" . DIRECTORY_SEPARATOR . "*");

        if ($files === false) {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Error retrieving user files.</div>');
        } else {
            $fileData = [];
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if ($content === false) {
                    die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Error reading file: ' . htmlspecialchars($file) . '</div>');
                } else {
                    $fileData[$file] = floatval($content);
                }
            }

            arsort($fileData);

            $totalResults = count($fileData);
            $totalPages = ceil($totalResults / $resultsPerPage);
            $start = ($currentPage - 1) * $resultsPerPage;
            $fileData = array_slice($fileData, $start, $resultsPerPage, true);

            $currentRank = $start + 1;

            echo "<ul class='w3-ul'>";
            foreach ($fileData as $file => $number) {
                echo "<div class='w3-card w3-margin w3-white w3-round'>";
                
                echo "<li class='w3-padding-16' style='display: flex; justify-content: space-between;'>
                        <span style='flex: 1; text-align: left;'>&nbsp;&nbsp;#$currentRank</span>
                        <span style='flex: 2; text-align: center;";

                if ($currentRank == 1) {
                    echo " color: red;";
                } elseif ($currentRank == 2) {
                    echo " color: orange;";
                } elseif ($currentRank == 3) {
                    echo " color: green;";
                }

                echo "'><b>" . htmlspecialchars(basename($file)) . "</b></span>
                        <span style='flex: 1; text-align: right;'>" . htmlspecialchars($number) . " R-Score&nbsp;&nbsp;</span>
                    </li>";

                echo "</div>";
                $currentRank++;
            }
            echo "</ul>";

            echo "<div class='w3-bar w3-center'>";
            for ($page = 1; $page <= $totalPages; $page++) {
                echo "<a href='?page=$page' class='w3-button w3-border w3-round";
                if ($page == $currentPage) {
                    echo " w3-blue";
                }
                echo "'>$page</a> ";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
