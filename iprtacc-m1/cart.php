<?php
session_start();
include "bdconnect.php";

// Проверяем корзину
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина товаров</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .quantity-input { width: 60px; padding: 5px; text-align: center; }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-order { background-color: #4CAF50; color: white; border: none; }
        .btn-update { background-color: #2196F3; color: white; border: none; }
        .btn-delete { background-color: #f44336; color: white; border: none; }
        .btn-back { background-color: #666; color: white; }
        .btn-home { background-color: #333; color: white; }
        .btn:hover { opacity: 0.8; cursor: pointer; }
        .cart-empty { text-align: center; padding: 50px; color: #666; }
        .total-row { background-color: #f0f0f0; font-weight: bold; font-size: 18px; }
        h1 { color: #333; text-align: center; }
        .actions { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h1> Корзина товаров</h1>
    
    <?php if(empty($_SESSION['cart'])): ?>
        <div class="cart-empty">
            <h3>Ваша корзина пуста</h3>
            <p>Добавьте товары в корзину, чтобы оформить заказ.</p>
            <a href="table_tovars.php" class="btn btn-back">← Перейти к товарам</a>
            <a href="index.php" class="btn btn-home"> На главную</a>
        </div>
    <?php else: ?>
        <form action="zakaz.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>ID товара</th>
                        <th>Наименование</th>
                        <th>Количество</th>
                        <th>Цена за 1 шт.</th>
                        <th>Общая стоимость</th>
                        <th>Удалить</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_sum = 0;
                    foreach($_SESSION['cart'] as $item):
                        $total = $item['cena'] * $item['quantity'];
                        $total_sum += $total;
                    ?>
                    <tr>
                        <td><?php echo $item['id']; ?>
                            <input type="hidden" name="id_tovar[]" value="<?php echo $item['id']; ?>">
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?>
                            <input type="hidden" name="cena[]" value="<?php echo $item['cena']; ?>">
                        </td>
                        <td>
                            <input type="number" name="kol[]" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="quantity-input">
                        </td>
                        <td><?php echo $item['cena']; ?> ₽</td>
                        <td><?php echo $total; ?> ₽</td>
                        <td><input type="checkbox" name="delete_id[]" value="<?php echo $item['id']; ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="4" align="right"><strong>Итого:</strong></td>
                        <td colspan="2"><strong><?php echo $total_sum; ?> ₽</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="actions">
                <input type="submit" name="update_cart" value=" Обновить количество" class="btn btn-update">
                <input type="submit" name="delete_selected" value=" Удалить отмеченные" class="btn btn-delete" onclick="return confirm('Удалить выбранные товары из корзины?')">
                <input type="submit" name="zak" value=" Оформить заказ" class="btn btn-order" onclick="return confirm('Оформить заказ?')">
                <a href="table_tovars.php" class="btn btn-back">← Продолжить покупки</a>
                <a href="index.php" class="btn btn-home"> На главную</a>
            </div>
        </form>
    <?php endif; ?>
</body>
</html>