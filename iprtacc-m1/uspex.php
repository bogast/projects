<?php
$i = isset($_GET['i']) ? intval($_GET['i']) : 0;

$st = '';
if ($i == 1) $st = "Данные успешно добавлены!";
if ($i == 2) $st = "Записи успешно удалены!";
if($i==3) {$st="Записи успешно обновлены!";}
if($i==4) {$st="Товары успешно добавлены в корзину! Заказ оформлен!";}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Успех</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
        .success { color: green; font-size: 24px; margin-bottom: 20px; }
        a { padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        a:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="success">✅ <?php echo $st; ?></div>
    <p><a href="index.php">На главную</a></p>
    <p><a href="cart.php">Перейти в корзину</a></p>
</body>
</html>