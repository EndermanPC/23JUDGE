<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Judge Testing</title>
    <link rel="icon" href="/assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<?php include 'utils/navbar.php'; ?>

<div class="w3-container w3-margin">

<?php
require 'utils/security.php';

error_reporting(E_ERROR | E_PARSE);
ini_set('upload_max_filesize', '64KB');
ini_set('precision', 3);

ignore_user_abort(true);
set_time_limit(0);

$problem = filter_input(INPUT_POST, 'problem', FILTER_SANITIZE_STRING) ?? null;
$name = isset($_COOKIE['username']) ? decrypt(htmlspecialchars($_COOKIE['username'])) : null;
$pass = isset($_COOKIE['password']) ? decrypt(htmlspecialchars($_COOKIE['password'])) : null;
if ($name && $pass) {
    if (!auth($name, $pass)) {
        header("Location: login.php");
    }
} else {
    header("Location: login.php");
}

require 'utils/env.php';

if (!empty($_POST['code'])) {
    $codeInput = $_POST['code'];
} else {
    $uploadedFile = $_FILES['file']['tmp_name'] ?? null;
    $filename = $_FILES['file']['name'] ?? null;
    if ($filename) {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($fileExtension, ['cpp', 'c++', 'cxx', 'cc', 'c'])) {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Your submission is not a C++ File!</div>');
        }
    }
}

echo "<h1 class='w3-center w3-text-teal'>Results for " . htmlspecialchars($name) . "</h1>";

echo "<div class='w3-bar w3-margin-bottom'>";
echo "<a class='w3-button w3-blue w3-round' href='/index.php'>Back</a>";
echo "<a class='w3-button w3-blue w3-round w3-margin-left' href='/submit.php?problem=$problem'>Resubmit</a>";
echo "</div>";

echo "<div id='statusBar' class='w3-bar w3-grey w3-round'>";
echo "<h4 id='statusInfo' class='w3-center'>&nbsp;&nbsp;...&nbsp;&nbsp;...&nbsp;&nbsp;...</h4>";
echo "</div><br>";

ob_flush();
flush();

$forbiddenCalls = [
    'system', 'CreateProcess', 'ShellExecute', 'WinExec', 
    'fork', 'exec', 'popen', 'spawnl', 'spawne', 'spawnv', 
    'spawnve', 'spawnvp', 'spawnvpe', 'remove', 'unlink', 
    'rmdir', 'fopen', 'freopen', 'fclose', 'fwrite', 
    'fread', 'ofstream', 'ifstream', 'fstream', 'socket', 'connect', 
    'bind', 'listen', 'accept', 'getaddrinfo', 'gethostbyname', 
    'execve', 'execl', 'execlp', 'execv', 'execvp', 
    '_spawn', '_exec', 'thread', 'CreateThread', 'ExitThread', 
    'kill', 'raise', 'signal', 'mmap', 'munmap', 
    'brk', 'sbrk', 'VirtualAlloc', 'VirtualFree', 
    'LoadLibrary', 'GetProcAddress', 'RegOpenKey', 'RegSetValue', 
    'RegDeleteKey', 'CreateRemoteThread', 'GetSystemInfo', 
    'GetVersionEx', 'uname', 'sleep', 'usleep', 'setitimer', 
    'shmget', 'shmat', 'shmctl', 'pipe', 'mkfifo'
];

function checkForbiddenCalls($code, $forbidden_calls) {
    foreach ($forbidden_calls as $call) {
        if (preg_match('/\b' . preg_quote($call, '/') . '\b/', $code)) {
            return $call;
        }
    }
    return false;
}

function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function showResults($testResult, $timeExec, $memExec, $testPreview, $expectedOutput, $programOutput, $showTest) {
    if ($showTest) {  
        echo "<details class='w3-margin-bottom'>";
        echo "<summary class='w3-text-teal'>" . $testResult . "&nbsp;&nbsp;&nbsp;" . $timeExec . "s&nbsp;&nbsp;&nbsp;" . $memExec . "Kb</summary>";
        echo "<p>&nbsp;&nbsp;Input:</p>";
        echo "<pre style='overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; box-sizing: border-box; max-width: 100%;' class='w3-code w3-border w3-light-grey'>" . $testPreview . "</pre>";
        echo "<p>&nbsp;&nbsp;Output:</p>";
        echo "<pre style='overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; box-sizing: border-box; max-width: 100%;' class='w3-code w3-border w3-light-grey'>" . $expectedOutput . "</pre>";
        echo "<p>&nbsp;&nbsp;Program:</p>";
        echo "<pre style='overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; box-sizing: border-box; max-width: 100%;' class='w3-code w3-border w3-light-grey'>" . $programOutput . "</pre>";
        echo "</details>";
    } else {
        echo "<details class='w3-margin-bottom'>";
        echo "<summary class='w3-text-teal'>" . $testResult . "&nbsp;&nbsp;&nbsp;" . $timeExec . "s&nbsp;&nbsp;&nbsp;" . $memExec . "Kb</summary>";
        echo "<p>&nbsp;&nbsp;Overview not available.</p>";
        echo "</details>";
    }
    ob_flush();
    flush();
}

if ((($codeInput || $uploadedFile) && $problem && $name && isset($_POST['language'])) || isset($_GET['view'])) {
    if (isset($_GET['view'])) {
        $viewSubmission = decrypt(str_replace(" ", "+", $_GET['view']));
        $problem = str_replace(".txt", "", end(explode(" ", $viewSubmission)));
    }

    $userFile = "users" . DIRECTORY_SEPARATOR . $name;
    $userDir = 'submissions' . DIRECTORY_SEPARATOR . $name;

    if (isset($codeInput)) {
        $sourceCode = $codeInput;
    } else {
        $submissionPath = $userDir . DIRECTORY_SEPARATOR . "$problem.cpp";
        move_uploaded_file($uploadedFile, $submissionPath);
        $sourceCode = file_get_contents($submissionPath);
    }

    $checkCodeResult = checkForbiddenCalls($sourceCode, $forbiddenCalls);

    if ($checkCodeResult !== false) {
        die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Your submission use (a) forbidden function(s): ' . $checkCodeResult . '</div>');
    }

    $configDir = "problems" . DIRECTORY_SEPARATOR . basename($problem) . DIRECTORY_SEPARATOR;
    $ProblemScore = file_get_contents($configDir . "Score.cfg");
    $ProblemRScore = file_get_contents($configDir . "RScore.cfg");
    $TimeLimit = file_get_contents($configDir . "TimeLimit.cfg");
    $MemoryLimit = file_get_contents($configDir . "MemoryLimit.cfg");
    $ShowTestConfig = file_get_contents($configDir . "ShowTest.cfg");
    $ShowTest = filter_var($ShowTestConfig, FILTER_VALIDATE_BOOLEAN);
    $StopWhenFailConfig = file_get_contents($configDir . "StopWhenFail.cfg");
    $StopWhenFail = filter_var($StopWhenFailConfig, FILTER_VALIDATE_BOOLEAN);

    $testDir = "problems" . DIRECTORY_SEPARATOR . basename($problem) . DIRECTORY_SEPARATOR . "tests";
    $testFolders = glob($testDir . DIRECTORY_SEPARATOR . "TEST*");
    natsort($testFolders);

    $testPassed = false;
    $testFailed = false;
    $testCE = false;
    $testEFE = false;
    $testTLE = false;
    $testMLE = false;
    $testIE = false;
    $testRTE = false;

    $testResult = 0.0;
    $maxTime = 0.0;
    $maxMem = 0;
    $scorePerTest = $ProblemScore / sizeof($testFolders);
    
    $apiUrl = "https://loving-awaited-mongoose.ngrok-free.app/submissions/batch";
    $languageId = $_POST['language'];
    $submissions = [];
    
    if (!isset($viewSubmission)) {
        foreach ($testFolders as $testFolder) {
            $inputFile = $testFolder . DIRECTORY_SEPARATOR . "input.txt";
            $expectedOutputFile = $testFolder . DIRECTORY_SEPARATOR . "output.txt";
            $inputData = file_get_contents($inputFile);
            $expectedOutput = file_get_contents($expectedOutputFile);
        
            $submissions[] = [
                "source_code" => $sourceCode,
                "language_id" => $languageId,
                "stdin" => $inputData,
                "expected_output" => $expectedOutput,
                "cpu_time_limit" => $TimeLimit,
                "cpu_extra_time" => 0.1,
                "memory_limit" => $MemoryLimit,
            ];
        }       
        
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode(["submissions" => $submissions]),
            ],
        ];
        
        $context  = stream_context_create($options);
        $response = @file_get_contents($apiUrl, false, $context);
        
        if ($response === FALSE) {
            die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: There are no active judges at this time.</div>');
        }

        $responseData = json_decode($response, true);
        $tokens = array_column($responseData, 'token');
        
    } else {
        $tokensFile = file_get_contents($viewSubmission);
        $tokens = explode(",", $tokensFile);
    }

    foreach ($tokens as $index => $token) {
        $resultUrl = "https://loving-awaited-mongoose.ngrok-free.app/submissions/$token";
    
        do {
            sleep(1);
            $result = file_get_contents($resultUrl);
            $resultData = json_decode($result, true);
        } while ($resultData['status']['id'] == 1 || $resultData['status']['id'] == 2);
    
        $testFolder = $testFolders[$index];
        $inputFile = $testFolder . DIRECTORY_SEPARATOR . "input.txt";
        $expectedOutputFile = $testFolder . DIRECTORY_SEPARATOR . "output.txt";
        $inputData = file_get_contents($inputFile);
        $expectedOutput = file_get_contents($expectedOutputFile);
        $programOutput = $resultData['stdout'];
    
        $inputData = truncateText($inputData);
        $expectedOutput = truncateText($expectedOutput);
        $programOutput = truncateText($programOutput);      

        if ($resultData['status']['id'] == 5) {
            showResults(basename($testFolder) . ": <span style='color:gray'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $actualOutput, $ShowTest);
            $testTLE = true;
            $testPassed = false;
    
            if ($StopWhenFail) {
                break;
            }
        } elseif ($resultData['status']['id'] == 11) {
            showResults(basename($testFolder) . ": <span style='color:yellow'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stderr'], $ShowTest);
            $testRTE = true;
            $testPassed = false;
    
            if ($StopWhenFail) {
                break;
            }
        } elseif ($resultData['status']['id'] >= 7 && $resultData['status']['id'] <= 12) {
            showResults(basename($testFolder) . ": <span style='color:blue'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stderr'], $ShowTest);
            $testMLE = true;
            $testPassed = false;
    
            if ($StopWhenFail) {
                break;
            }
        } elseif ($resultData['status']['id'] == 13) {
            showResults(basename($testFolder) . ": <span style='color:purple'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stderr'], $ShowTest);
            $testIE = true;
            $testPassed = false;
    
            if ($StopWhenFail) {
                break;
            }
        } elseif ($resultData['status']['id'] == 3) {
            showResults(basename($testFolder) . ": <span style='color:green'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stdout'], $ShowTest);
            $testPassed = true;
            $testResult += $scorePerTest;
        } elseif ($resultData['status']['id'] == 4) {
            showResults(basename($testFolder) . ": <span style='color:red'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stdout'], $ShowTest);
            $testFailed = true;
            $testPassed = false;
    
            if ($StopWhenFail) {
                break;
            }
        } elseif ($resultData['status']['id'] == 6) {
            showResults(basename($testFolder) . ": <span style='color:red'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stdout'], $ShowTest);
            $testCE = true;
            $testPassed = false;
    
            break;
        } elseif ($resultData['status']['id'] == 14) {
            showResults(basename($testFolder) . ": <span style='color:red'>" . $resultData['status']['description'] . "</span>", $resultData['time'], $resultData['memory'], $inputData, $expectedOutput, $resultData['stdout'], $ShowTest);
            $testEFE = true;
            $testPassed = false;
    
            break;
        } 

        $maxTime += floatval($resultData['time']);
        $maxMem += $resultData['memory'];
    }

    if (!isset($viewSubmission)) {
        if (!file_exists($userDir . DIRECTORY_SEPARATOR . "ACSubmissions.txt")) {
            file_put_contents($userDir . DIRECTORY_SEPARATOR . "ACSubmissions.txt", "");
        }

        $ACSubmissions = [];

        $lines = file($userDir . DIRECTORY_SEPARATOR . "ACSubmissions.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $ACSubmissions[] = $line;
        }

        if ($testPassed && !in_array($problem, $ACSubmissions)) {
            file_put_contents($userFile, (float)file_get_contents($userFile) + $ProblemRScore);
            file_put_contents($userDir . DIRECTORY_SEPARATOR . "ACSubmissions.txt", $problem . PHP_EOL, FILE_APPEND);
        }

        $submitTime = date('s i H d m Y');

        file_put_contents("submissions" . DIRECTORY_SEPARATOR . "Log.txt", $submitTime . " " . $problem . " $name " . "$testResult/" . $ProblemScore . PHP_EOL, FILE_APPEND);
        
        $files = ["submissions" . DIRECTORY_SEPARATOR . "$name" . DIRECTORY_SEPARATOR . "$problem.cpp", "submissions" . DIRECTORY_SEPARATOR . "$name" . DIRECTORY_SEPARATOR . "$problem.exe"];
        $outFiles = glob("submissions". DIRECTORY_SEPARATOR . "$name" . DIRECTORY_SEPARATOR . "*.out");

        $allFiles = array_merge($files, $outFiles);

        foreach ($allFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    echo "<script>";
    echo "var statusBar = document.getElementById('statusBar');";
    echo "var statusInfo = document.getElementById('statusInfo');";
    
    if ($testCE) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;CE&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
    } elseif ($testEFE) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;EFE&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
    } elseif ($testTLE) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;TLE&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
    } elseif ($testRTE) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;RTE&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
        echo "statusBar.classList.remove('w3-grey');";
        echo "statusBar.classList.add('w3-yellow');";
    } elseif ($testMLE) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;MLE&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
        echo "statusBar.classList.remove('w3-grey');";
        echo "statusBar.classList.add('w3-blue');";
    } elseif ($testFailed) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;WA&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
        echo "statusBar.classList.remove('w3-grey');";
        echo "statusBar.classList.add('w3-red');";
    } elseif ($testIE) {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;IE&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
        echo "statusBar.classList.remove('w3-grey');";
        echo "statusBar.classList.add('w3-purple');";
    } else {
        echo "statusInfo.innerHTML = '&nbsp;&nbsp;AC&nbsp;|&nbsp;$testResult/$ProblemScore&nbsp;|&nbsp;" . $maxTime . "s&nbsp;|&nbsp;" . $maxMem . "Kb';";
        echo "statusBar.classList.remove('w3-grey');";
        echo "statusBar.classList.add('w3-green');";
    }
    echo "</script>";
    echo "<br>";
} else {
    die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Submission Failed.</div>');
}

echo "<script>
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>";

if (!isset($viewSubmission)) {
    $tokensJson = json_encode($tokens);

    echo "<script>
    function saveSubmission() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'result.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Submission and path saved successfully');
            }
        };
        var jsonContent = $tokensJson;
        var path = encodeURIComponent('submissions/$name/$submitTime $problem.txt');
        console.log(path);
        xhr.send('json=' + encodeURIComponent(jsonContent) + '&path=' + path);
    }

    window.onload = function() {
        saveSubmission();
    };
    </script>";
}
?>

</div>
</body>
</html>
