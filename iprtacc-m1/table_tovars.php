<?php 
session_start();
include "bdconnect.php";
include "func.php";

// Инициализация корзины, если не существует
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

// Обработка фильтров и сортировки
$filter_active = false;
$category_filter = 0;
$sort_type = "";
$search_name = "";

if(isset($_POST["filtr"]) || isset($_POST["sort"]) || isset($_POST["search"])){
    if(isset($_POST["category"]) && $_POST["category"] != "Bce"){
        $category_filter = intval($_POST["category"]);
        $filter_active = true;
    }
    
    if(isset($_POST["cena"]) && $_POST["cena"] != "0"){
        $sort_type = $_POST["cena"];
        $filter_active = true;
    }
    
    if(isset($_POST["name"]) && trim($_POST["name"]) != ""){
        $search_name = mysqli_real_escape_string($link, trim($_POST["name"]));
        $filter_active = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Склад товаров -> Информация о товарах</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .filter-panel { 
            background: #f9f9f9; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; }
        .filter-group label { margin-bottom: 5px; font-weight: bold; }
        select, input[type="text"] { padding: 8px; width: 200px; }
        .btn-filter { 
            padding: 8px 20px; 
            background-color: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-filter:hover { background-color: #45a049; }
        .cart-info {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff9800;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            z-index: 100;
        }
        .cart-info:hover { background-color: #f57c00; }
        h3 { color: #333; }
        .add-to-cart-btn {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .add-to-cart-btn:hover { background-color: #0b7dda; }
        .success-msg {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php 
    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    ?>
    <a href="cart.php" class="cart-info">🛒 Корзина (<?php echo $cart_count; ?>)</a>
    
    <h3 align="center">Список товаров</h3>
    
    <!-- Отдельная форма для фильтров -->
    <form action="" method="post" class="filter-panel">
        <div class="filter-group">
            <label for="category">Выбор по категории:</label>
            <select name="category">
                <option value="Bce">Все</option>
                <?php echo show_categories(); ?>
            </select>
        </div>
        <div class="filter-group">
            <input type="submit" value="Фильтр" name="filtr" class="btn-filter">
        </div>
        
        <div class="filter-group">
            <label for="cena">Сортировка по цене:</label>
            <select name="cena">
                <option value="0">Без сортировки</option>
                <option value="min" <?php echo ($sort_type == 'min') ? 'selected' : ''; ?>>По возрастанию (дешёвые сверху)</option>
                <option value="max" <?php echo ($sort_type == 'max') ? 'selected' : ''; ?>>По убыванию (дорогие сверху)</option>
            </select>
        </div>
        <div class="filter-group">
            <input type="submit" value="Сортировать" name="sort" class="btn-filter">
        </div>
        
        <div class="filter-group">
            <label for="name">Поиск по названию:</label>
            <input type="text" name="name" placeholder="Введите название..." value="<?php echo htmlspecialchars($search_name); ?>">
        </div>
        <div class="filter-group">
            <input type="submit" value="Найти" name="search" class="btn-filter">
        </div>
        
        <div class="filter-group">
            <a href="table_tovars.php" class="btn-filter" style="background-color: #666; text-decoration: none; padding: 8px 20px;">Сбросить фильтры</a>
        </div>
    </form>
    
    <!-- Отдельная форма для добавления в корзину -->
    <form action="zakaz.php" method="post">
        <table border="1" width="100%">
            <thead>
                <tr>
                    <th>Номер</th>
                    <th>Наименование</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Количество на складе</th>
                    <th>Срок годности</th>
                    <th>Подробнее</th>
                    <th>Добавить в корзину</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Базовый SQL запрос
                $sql = "SELECT tovars.id, tovars.name, categories.category, tovars.cena, tovars.kol, tovars.srok 
                        FROM tovars 
                        LEFT JOIN categories ON tovars.id_cat = categories.id_cat WHERE 1=1";
                
                // Фильтрация по категориям
                if($category_filter > 0){
                    $sql .= " AND categories.id_cat = $category_filter";
                }
                
                // Поиск по названию
                if($search_name != ""){
                    $sql .= " AND tovars.name LIKE '%$search_name%'";
                }
                
                // Сортировка по цене
                if($sort_type == "min"){
                    $sql .= " ORDER BY tovars.cena ASC";
                } elseif($sort_type == "max"){
                    $sql .= " ORDER BY tovars.cena DESC";
                } else {
                    $sql .= " ORDER BY tovars.id ASC";
                }
                
                $result = mysqli_query($link, $sql) or die("Query failed: " . mysqli_error($link));
                
                if(mysqli_num_rows($result) == 0){
                    echo "<tr><td colspan='8' align='center'>Товары не найдены</td></tr>";
                }
                
                while($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["category"] ?? '—'); ?></td>
                    <td><?php echo $row["cena"]; ?> ₽</td>
                    <td><?php echo $row["kol"]; ?> шт.</td>
                    <td><?php echo $row["srok"]; ?></td>
                    <td><a href="tovar.php?id=<?php echo $row["id"]; ?>">Подробнее</a></td>
                    <td><input type="checkbox" name="id[]" value="<?php echo $row["id"]; ?>"></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <center>
            <input type="submit" value="➕ Добавить выбранные в корзину" name="zakaz" class="add-to-cart-btn">
        </center>
    </form>
    <br>
    <center>
        <a href="index.php">← На главную</a>
    </center>
</body>
</html>