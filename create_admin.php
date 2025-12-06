<?php
$plain_password = 'admin123'; // choose your password
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hashed_password;
?>
