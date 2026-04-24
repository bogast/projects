<?php
session_start();
include "bdconnect.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id == 0){
    header("Location: table_tovars_new.php");
    exit();
}

$sql = "SELECT tovars.*, categories.category 
        FROM tovars 
        LEFT JOIN categories ON tovars.id_cat = categories.id_cat 
        WHERE tovars.id = $id";
$result = mysqli_query($link, $sql) or die("Товар не найден");
$row = mysqli_fetch_assoc($result);

if(!$row){
    header("Location: table_tovars_new.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подробнее о товаре</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .product-card { 
            max-width: 500px; 
            margin: 0 auto; 
            padding: 20px; 
            border: 1px solid #ccc; 
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .product-card h2 { color: #4CAF50; text-align: center; }
        .info { margin: 15px 0; padding: 10px; background: #f9f9f9; border-radius: 5px; }
        .info strong { display: inline-block; width: 120px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover { background-color: #45a049; }
        .cart-btn { background-color: #2196F3; margin-left: 10px; }
        .cart-btn:hover { background-color: #0b7dda; }
        .back { background-color: #666; }
        .back:hover { background-color: #555; }
    </style>
</head>
<body>
    <div class="product-card">
        <h2>📦 <?php echo htmlspecialchars($row['name']); ?></h2>
        
        <div class="info">
            <strong>ID товара:</strong> <?php echo $row['id']; ?>
        </div>
        
        <div class="info">
            <strong>Категория:</strong> <?php echo htmlspecialchars($row['category'] ?? '—'); ?>
        </div>
        
        <div class="info">
            <strong>Цена:</strong> <?php echo $row['cena']; ?> ₽
        </div>
        
        <div class="info">
            <strong>Количество на складе:</strong> <?php echo $row['kol']; ?> шт.
        </div>
        
        <div class="info">
            <strong>Срок годности:</strong> <?php echo $row['srok']; ?>
        </div>
        
        <form action="zakaz.php" method="post" style="display: inline;">
            <input type="hidden" name="id_single" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="name_single" value="<?php echo htmlspecialchars($row['name']); ?>">
            <input type="hidden" name="cena_single" value="<?php echo $row['cena']; ?>">
            <label for="quantity">Количество:</label>
            <input type="number" name="quantity_single" value="1" min="1" max="<?php echo $row['kol']; ?>" style="width:60px;">
            <input type="submit" value="🛒 Добавить в корзину" class="btn cart-btn" name="zakaz_single">
        </form>
        
        <a href="table_tovars_new.php" class="btn back">← Назад к списку</a>
    </div>
</body>
</html>