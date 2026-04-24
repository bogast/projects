
<?php
$host = 'localhost';
$user = 'ifpbkdtb';
$pass = 'T6Nut5';                    
$db   = 'ifpbkdtb_m1';

$link = mysqli_connect($host, $user, $pass, $db);

if (!$link) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}

mysqli_set_charset($link, 'utf8mb4');
?>