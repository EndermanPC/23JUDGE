<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Contest</title>
    <link rel="icon" href="icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">
    <?php include 'utils/navbar.php'; ?>

    <div class="w3-container w3-margin">
        <?php
        require 'utils/security.php';

        error_reporting(E_ERROR | E_PARSE);
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['contest'])) {
            $contest_name = htmlspecialchars($_GET['contest']);
            $contest_dir = 'contests' . DIRECTORY_SEPARATOR . $contest_name;
            $config_file = $contest_dir . DIRECTORY_SEPARATOR . 'contest.cfg';
            $name = isset($_COOKIE['username']) ? decrypt(htmlspecialchars($_COOKIE['username'])) : '';
            $pass = isset($_COOKIE['password']) ? decrypt(htmlspecialchars($_COOKIE['password'])) : '';
            if (!auth($name, $pass)) {
                header("Location: login.php");
            }

            if (is_dir($contest_dir) && file_exists($config_file)) {
                $config = file_get_contents($config_file);

                echo '<div class="w3-card w3-padding w3-white w3-round">';
                echo '<h2 class="w3-text-teal">' . $contest_name . '</h2>';

                chdir("contests/$contest_name");

                $problemDirs = glob('problems' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
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
                    } else {
                        die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Submissions file does not exist.</div>');
                    }
                }

                echo '<h3>Problem:</h3>';
                foreach ($problemDirs as $dir) {
                    echo '<ul class="w3-ul w3-border w3-round">';
                    echo '<li>';
                    echo '<div class="w3-bar w3-border w3-light-gray w3-round">';
                    echo '<a class="w3-bar-item w3-button" href="problem.php?problem=' . urlencode(basename($dir)) . '">';
                    echo basename($dir);
                    if (in_array(basename($dir), $ACSubmissions)) {
                        echo '&nbsp;✔';
                    }
                    echo '</a>';
                    echo '</div>';
                    echo '</li>';
                    echo '</ul>';
                }                

                if (!isset($_COOKIE['contest'])) {
                    if (trim($config) === "Opening") {
                        echo '<br><p>Status: Opening</p>';
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="contest" value="' . $contest_name . '">';
                        echo '<div class="w3-bar w3-margin-bottom">';
                        echo '<a class="w3-button w3-blue w3-round" href="index.php?view=contests">Back</a>';
                        echo '<button class="w3-button w3-green w3-round w3-margin-left" type="submit">Join Contest</button>';
                        echo '</div>';
                        echo '</form>';
                    } else {
                        echo '<br><p>Status: Closing</p>';
                        echo '<a class="w3-button w3-blue w3-round" href="index.php?view=contests">Back</a>';
                    }
                } else {
                    echo '<br><p>Status: Opening</p>';
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="contest" value="leave">';
                    echo '<div class="w3-bar w3-margin-bottom">';
                    echo '<a class="w3-button w3-blue w3-round" href="index.php?view=contests">Back</a>';
                    echo '<button class="w3-button w3-green w3-round w3-margin-left w3-round" type="submit">Leave Contest</button>';
                    echo '</div>';
                    echo '</form>';
                }

                chdir("/");

                echo '</div>';
            } else {
                die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: The contest you are looking for does not exist.</div>');
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contest'])) {
            $selected_contest = htmlspecialchars($_POST['contest']);
            if ($selected_contest === 'leave') {
                setcookie('contest', '', time() - 3600, "/");
                header('Location: index.php?view=contests');
                exit;
            } else {
                $contest_path = 'contests' . DIRECTORY_SEPARATOR . $selected_contest;

                if (is_dir($contest_path)) {
                    setcookie('contest', encrypt($selected_contest), time() + (10 * 365 * 24 * 60 * 60), "/");
                    mkdir($contest_path . DIRECTORY_SEPARATOR . 'submissions' . DIRECTORY_SEPARATOR . '' . htmlspecialchars(basename($name)));
                    file_put_contents($contest_path . DIRECTORY_SEPARATOR . 'submissions' . DIRECTORY_SEPARATOR . '' . htmlspecialchars(basename($name)) . DIRECTORY_SEPARATOR . "ACSubmissions.txt", "");
                    header('Location: index.php?view=contests');
                    exit;
                } else {
                    die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: The selected contest could not be found.</div>');
                }
            }
        } else {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Please select a contest from the Homepage.</div>');
        }
        ?>
    </div>
</body>
</html>
