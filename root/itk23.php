<!DOCTYPE html>
<html>
<body>

<?php
$usernames = ["nvgbaoitk23", "nqhuyitk23", "nqvuitk23", "tvhsonitk23", "pnminhitk23", "tnhvuitk23", "Invvinh09", "bntsangitk23", "ntbyenitk23", "tqhuyitk23", "dtylinhitk23", "nqthanhitk23", "dlkhuyitk23", "ttaduyenitk23", "ncrinitk23", "pttlinhitk23", "hbquanitk23", "nqduyitk23", "nhriitk23", "dbquyenitk23", "ntlquyenitk23", "ltkhoangitk23", "tnquocitk23"];

$pass = "itk23maidinh";

foreach ($usernames as $username) {
    $hashed_password = password_hash($pass, PASSWORD_ARGON2I);
    
    echo $username . ":" . $hashed_password . PHP_EOL;
}
?>

</body>
</html>
