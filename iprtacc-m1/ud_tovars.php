<?php
include "bdconnect.php";

// ====================== ОБРАБОТКА УДАЛЕНИЯ ======================
if (isset($_POST['ud']) && !empty($_POST['ud_id'])) {
    foreach ($_POST['ud_id'] as $id) {
        $id = intval($id);
        mysqli_query($link, "DELETE FROM tovars WHERE id = $id");
    }
    header("Location: uspex.php?i=2");  // i=2 = "Записи успешно удалены"
    exit;
}
// ============================================================

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список товаров — удаление и редактирование</title>
</head>
<body>
    <h3 align="center">Список товаров</h3>

    <form method="post" action="ud_tovars.php">
        <table width="100%" border="2">
            <tr>
                <td>Номер</td>
                <td>Наименование</td>
                <td>Категория</td>
                <td>Цена</td>
                <td>Количество</td>
                <td>Срок годности</td>
                <td>Редактировать</td>
                <td>Удалить</td>
            </tr>

            <?php
            $result = mysqli_query($link, "
                SELECT tovars.*, categories.category 
                FROM tovars 
                LEFT JOIN categories ON tovars.id_cat = categories.id_cat 
                ORDER BY tovars.id
            ");

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['category']) . "</td>
                    <td>{$row['cena']}</td>
                    <td>{$row['kol']}</td>
                    <td>{$row['srok']}</td>
                    <td><a href='update.php?id={$row['id']}'>Редактировать</a></td>
                    <td><input type='checkbox' name='ud_id[]' value='{$row['id']}'></td>
                </tr>";
            }
            ?>
        </table>
        <br>
        <center>
            <input type="submit" name="ud" value="Удалить отмеченные" style="padding:10px 20px; font-size:16px;">
        </center>
    </form>

    <br><a href="index.php">← На главную</a>
</body>
</html>