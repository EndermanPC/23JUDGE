<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Problem</title>
    <link rel="icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script type="text/javascript">
        MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']]
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
    <?php include 'utils/navbar.php'; ?>

    <div class="w3-container">
        <?php
        require 'utils/security.php';
        require 'vendor/autoload.php';
        
        error_reporting(E_ERROR | E_PARSE);

        $name = isset($_COOKIE['username']) ? decrypt(htmlspecialchars($_COOKIE['username'])) : '';
        $pass = isset($_COOKIE['password']) ? decrypt(htmlspecialchars($_COOKIE['password'])) : '';
        if (!auth($name, $pass)) {
            header("Location: login.php");
        }

        require 'utils/env.php';

        $ACSubmissions = [];
        $userDir = 'submissions' . DIRECTORY_SEPARATOR . htmlspecialchars(basename($name));

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
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Invalid username or user directory does not exist.</div>');
        }

        $problem = isset($_GET['problem']) ? htmlspecialchars($_GET['problem']) : '';

        $problemDir = "problems" . DIRECTORY_SEPARATOR . $problem;
        if (!is_dir($problemDir)) {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Problem not found.</div>');
        }

        $Parsedown = new Parsedown();

        $problemFile = $problemDir . DIRECTORY_SEPARATOR . "Problem.md";
        $problemScoreFile = $problemDir . DIRECTORY_SEPARATOR . "Score.cfg";
        $problemRScoreFile = $problemDir . DIRECTORY_SEPARATOR . "RScore.cfg";
        $showTestFile = $problemDir . DIRECTORY_SEPARATOR . "ShowTest.cfg";
        $stopWhenFailFile = $problemDir . DIRECTORY_SEPARATOR . "StopWhenFail.cfg";
        $timeLimitFile = $problemDir . DIRECTORY_SEPARATOR . "TimeLimit.cfg";
        $memoryLimitFile = $problemDir . DIRECTORY_SEPARATOR . "MemoryLimit.cfg";

        function readFileContent($filePath) {
            if (file_exists($filePath)) {
                return file_get_contents($filePath);
            } else {
                echo '<div class="w3-card w3-margin w3-padding w3-red">File not found: ' . htmlspecialchars($filePath) . '</div>';
                exit();
            }
        }

        $problemContent = readFileContent($problemFile);
        $problemScore = readFileContent($problemScoreFile);
        $problemRScore = readFileContent($problemRScoreFile);
        $showTest = readFileContent($showTestFile);
        $stopWhenFail = readFileContent($stopWhenFailFile);
        $timeLimit = readFileContent($timeLimitFile);
        $memoryLimit = readFileContent($memoryLimitFile);
        ?>

        <h1 class="w3-center w3-text-teal"><?php echo $problem; if (in_array(basename($problem), $ACSubmissions)) echo "&nbsp;✔"; ?></h1>
        
        <div>
            <h2>Configuration:</h2>
            <div class="w3-card w3-margin w3-padding w3-round">
                <div>
                    <span><b>Score:</b></span>
                    <span><?php echo $problemScore; ?> Point(s)</span>
                </div>
                <div>
                    <span><b>R-Score:</b></span>
                    <span><?php echo $problemRScore; ?> Point(s)</span>
                </div>
                <div>
                    <span><b>Show Test:</b></span>
                    <span><?php echo $showTest; ?></span>
                </div>
                <div>
                    <span><b>Stop When Fail:</b></span>
                    <span><?php echo $stopWhenFail; ?></span>
                </div>
                <div>
                    <span><b>Time Limit:</b></span>
                    <span><?php echo $timeLimit; ?> Second(s)</span>
                </div>
                <div>
                    <span><b>Memory Limit:</b></span>
                    <span><?php echo $memoryLimit; ?> Kb</span>
                </div>
            </div>
        </div>

        <h2>Problem:</h2>
        <div class="w3-card w3-margin w3-padding w3-round">
            <?php echo $Parsedown->text($problemContent); ?>
        </div>

        <div class="w3-center w3-margin">
            <a class="w3-button w3-blue w3-round w3-margin-right" href="index.php">Back</a>
            <a class="w3-button w3-green w3-round" href="submit.php?problem=<?php echo htmlspecialchars($problem); ?>">Submit</a>
        </div>
    </div>
</body>
</html>
