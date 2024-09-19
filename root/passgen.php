<!DOCTYPE html>
<html>
<body>

<?php
require '/utils/anti_ddos.php';

$pass = $_GET['password'];
echo password_hash($pass, PASSWORD_ARGON2I);
?>

</body>
</html>
