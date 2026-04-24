<?php include "bdconnect.php"; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить товар</title>
</head>
<body>
    <h2>➕ Добавление товара на склад</h2>
    <form action="insert_tovars.php" method="post">
        Название: <input type="text" name="name" required style="width:300px"><br><br>
        Категория: 
        <select name="id_cat" required>
            <option value="">— Выберите —</option>
            <?php
            $res = mysqli_query($link, "SELECT * FROM categories ORDER BY category");
            while($cat = mysqli_fetch_assoc($res)){
                echo "<option value='{$cat['id_cat']}'>{$cat['category']}</option>";
            }
            ?>
        </select><br><br>
        Цена: <input type="number" step="0.01" name="cena" required><br><br>
        Количество: <input type="number" name="kol" required><br><br>
        Срок годности: <input type="date" name="srok" required><br><br>
        <input type="submit" value="Добавить товар" style="padding:10px 20px">
    </form>
    <br><a href="index.php">← На главную</a>
</body>
</html>