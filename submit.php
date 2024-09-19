<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Solution</title>
    <link rel="icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">
    <?php include 'utils/navbar.php'; ?>

    <?php
    require 'utils/security.php';

    error_reporting(E_ERROR | E_PARSE);

    require 'utils/env.php';

    $problem = isset($_GET['problem']) ? htmlspecialchars($_GET['problem']) : '';

    $problemDir = "problems" . DIRECTORY_SEPARATOR . $problem;
    if (!is_dir($problemDir)) {
        die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Problem not found.</div>');
    }

    $problemFile = $problemDir . DIRECTORY_SEPARATOR . "Problem.md";

    if (!file_exists($problemFile)) {
        die('<div class="w3-container w3-red w3-center w3-padding-large w3-round">Error: Problem file not found.</div>');
    }
    ?>
    <div class="w3-container w3-margin w3-round">
        <div class="w3-card w3-padding w3-white w3-round">
            <h1>Submit your solution for <?php echo htmlspecialchars($problem); ?></h1>

            <form class="w3-container" action="judge.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="problem" value="<?php echo htmlspecialchars($problem); ?>">

                <div class="w3-margin-bottom">
                    <label class="w3-text-gray" for="code">Paste C++ Code:</label><br>
                    <textarea class="w3-input w3-border w3-round" id="code" name="code" rows="10" required></textarea>
                </div>

                <div class="w3-margin-bottom">
                    <select class="form-control" id="language" name="language">
                    <option value="54" selected>C++ (GCC 9.2.0)</option>
                        <option value="53">C++ (GCC 8.3.0)</option>
                        <option value="52">C++ (GCC 7.4.0)</option>
                        <option value="76">C++ (Clang 7.0.1)</option>

                        <option value="70">Python (2.7.17)</option>
                        <option value="71">Python (3.8.1)</option>
                        <option value="92">Python (3.11.2)</option>

                        <option value="67">Pascal (FPC 3.0.4)</option>
                    </select>
                </div>

                <div class="w3-margin-bottom">
                    <label class="w3-text-gray" for="file">Or Upload C++ File:</label>
                    <input class="w3-input w3-border w3-round" type="file" id="file" name="file" accept=".cpp,.c++,.cxx,.cc,.c" required>
                </div>
                
                <div class="w3-bar">
                    <a class="w3-button w3-blue w3-round w3-margin-right" href="problem.php?problem=<?php echo htmlspecialchars($problem); ?>">Back</a>
                    <button class="w3-button w3-green w3-round" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = Array.from(
                document.querySelectorAll('input[name=file], textarea[name=code]')
            );

            const inputListener = e => {
                inputs
                .filter(i => i !== e.target)
                .forEach(i => (i.required = !e.target.value.length));
            };

            inputs.forEach(i => i.addEventListener('input', inputListener));
        });
    </script>
</body>
</html>
