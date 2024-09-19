<?php
function auth($username, $password) {
    $username = preg_replace('/[^a-zA-Z0-9]/m', '', $username);
    $password = $password;

    $file = fopen("databases/users.txt", "r");
    $found = false;

    while (($line = fgets($file)) !== false) {
        list($storedUser, $storedPass) = explode(":", trim($line));
        
        if ($storedUser == $username && password_verify($password, $storedPass)) {
            $found = true;
            break;
        }
    }
    fclose($file);
    
    if ($found) {
        return true;
    } else {
        return false;
    }
}

function getKeyFromPassword($salt) {
    $password = "NBK@==@JUDGE23@==@ITK@#23NBK";
    return hash_pbkdf2("sha256", $password, $salt, 100000, 32, true);
}

function encrypt($plaintext) {
    $password = "NBK@==@JUDGE23@==@ITK@#23NBK";

    $cipher = "AES-256-CBC";
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_length);
    
    $salt = openssl_random_pseudo_bytes(16);
    
    $key = getKeyFromPassword($salt);
    
    $encrypted = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    
    $encrypted_data = base64_encode($salt . $iv . $encrypted);
    
    return $encrypted_data;
}

function decrypt($encrypted_data) {
    $cipher = "AES-256-CBC";
    $iv_length = openssl_cipher_iv_length($cipher);
    $salt_length = 16;
    
    $data = base64_decode($encrypted_data);
    
    $salt = substr($data, 0, $salt_length);
    $iv = substr($data, $salt_length, $iv_length);
    $encrypted = substr($data, $salt_length + $iv_length);
    
    $key = getKeyFromPassword($salt);
    
    $decrypted = openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    
    return $decrypted;
}
?>