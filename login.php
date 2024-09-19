<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Judge Login</title>
    <link rel="icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<?php include 'utils/navbar.php'; ?>

<div class="w3-container w3-card-4 w3-light-grey" style="max-width:400px;margin:auto;margin-top:100px">
    <h2>Login</h2>
    <form method="post" action="">
        <label class="w3-text-black">Username</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" name="username" required>
        
        <label class="w3-text-black">Password</label>
        <input class="w3-input w3-border" type="password" name="password" required>
        
        <button class="w3-button w3-black w3-margin-top" type="submit" name="login">Login</button>
    </form>
    <p class="w3-margin-top">Don't have an account? <a href="https://forms.gle/mkkWbPTxZRtRJBhK6">Register here</a>.</p>
</div>

<?php
require 'utils/security.php';

error_reporting(E_ERROR | E_PARSE);

if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if (auth($username, $password)) {
        $userFile = "users" . DIRECTORY_SEPARATOR . $username;
        if (!file_exists($userFile)) {
            file_put_contents($userFile, "0");
        }

        $userDir = 'submissions' . DIRECTORY_SEPARATOR . basename($username);
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }

        setcookie('username', encrypt($username), time() + (10 * 365 * 24 * 60 * 60), "/");
        setcookie('password', encrypt($password), time() + (10 * 365 * 24 * 60 * 60), "/");

        header("Location: index.php");
    } else {
        echo '<div class="w3-container w3-red w3-center w3-margin-top w3-round">Invalid username or password.</div>';
    }
}
?>

</body>
</html>
