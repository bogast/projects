<?php
include "bdconnect.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT tovars.*, categories.category 
            FROM tovars 
            LEFT JOIN categories ON tovars.id_cat = categories.id_cat 
            WHERE tovars.id = $id";

    $result = mysqli_query($link, $sql) or die("Товар не найден");
    $row = mysqli_fetch_assoc($result);
}

if (isset($_POST['red'])) {
    $id   = intval($_POST['id']);
    $cena = floatval($_POST['cena']);
    $kol  = intval($_POST['kol']);

    $sql = "UPDATE tovars SET cena = $cena, kol = $kol WHERE id = $id";
    mysqli_query($link, $sql) or die("Ошибка обновления");

    header("Location: uspex.php?i=3");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование товара</title>
</head>
<body>
<h1>Информация о товаре</h1>

<form action="" method="post">
<table align="center" border="1">
    <tr><td>Идентификатор</td><td><?php echo $row['id']; ?></td></tr>
    <tr><td>Название</td><td><?php echo htmlspecialchars($row['name']); ?></td></tr>
    <tr><td>Категория</td><td><?php echo htmlspecialchars($row['category']); ?></td></tr>
    
    <tr>
        <td>Цена товара</td>
        <td><input type="number" step="0.01" name="cena" value="<?php echo $row['cena']; ?>" required></td>
    </tr>
    <tr>
        <td>Количество на складе</td>
        <td><input type="number" name="kol" value="<?php echo $row['kol']; ?>" required></td>
    </tr>
    <tr>
        <td>Срок годности</td>
        <td><?php echo $row['srok']; ?></td>
    </tr>

    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <tr><td colspan="2" align="center">
        <input type="submit" name="red" value="Сохранить изменения">
    </td></tr>
</table>
</form>

<br><a href="ud_tovars.php">Назад к списку</a>
</body>
</html>