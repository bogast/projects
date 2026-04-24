<?php
include "bdconnect.php";

$name   = mysqli_real_escape_string($link, $_POST['name'] ?? '');
$id_cat = intval($_POST['id_cat'] ?? 0);
$cena   = floatval($_POST['cena'] ?? 0);
$kol    = intval($_POST['kol'] ?? 0);
$srok   = $_POST['srok'] ?? '';

if ($id_cat == 0 || empty($name)) {
    die("❌ Ошибка: заполните все поля!");
}

$sql = "INSERT INTO tovars (name, id_cat, cena, kol, srok) 
        VALUES ('$name', $id_cat, $cena, $kol, '$srok')";

if (mysqli_query($link, $sql)) {
    header("Location: uspex.php?i=1");
    exit;
} else {
    die("Ошибка: " . mysqli_error($link));
}
?>