<?php
function show_categories(){
    include "bdconnect.php";
    $sql="SELECT * FROM categories";
    $result = mysqli_query($link,$sql) or die("Query failed");
    while( $row = mysqli_fetch_array($result)){
        $array_category[$row["id_cat"]]=$row["category"];
    };
    $str="";
    foreach ($array_category as $key => $value){
        $str=$str. "<option value='".$key."' >".$value."</option>"; 
    }
    return $str;
}

// Функция для вывода товаров с фильтрацией и сортировкой
function show_tovars(){
    include "bdconnect.php";
    
    // Базовый SQL запрос
    $sql = "SELECT tovars.id, tovars.name, categories.category, tovars.cena, tovars.kol, tovars.srok 
            FROM tovars 
            LEFT JOIN categories ON tovars.id_cat = categories.id_cat WHERE 1=1";
    
    // Фильтрация по категориям
    if(isset($_POST["filtr"])){
        $category = $_POST["category"];
        if($category != "Bce" && !empty($category)){
            $category = intval($category);
            $sql .= " AND categories.id_cat = $category";
        }
    }
    
    // Сортировка по цене
    if(isset($_POST["sort"])){
        $cena = $_POST["cena"];
        if($cena == "min"){
            $sql .= " ORDER BY tovars.cena ASC";
        } elseif($cena == "max"){
            $sql .= " ORDER BY tovars.cena DESC";
        } else {
            $sql .= " ORDER BY tovars.id ASC";
        }
    } else {
        $sql .= " ORDER BY tovars.id ASC";
    }
    
    // Поиск по названию
    if(isset($_POST["name"]) && trim($_POST["name"]) !== ''){
        $name = mysqli_real_escape_string($link, trim($_POST["name"]));
        $sql .= " AND tovars.name LIKE '%$name%'";
    }
    
    $result = mysqli_query($link, $sql) or die("Query failed: " . mysqli_error($link));
    
    $str = "";
    while($row = mysqli_fetch_assoc($result)){
        $str .= "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . htmlspecialchars($row["name"]) . "</td>
                    <td>" . htmlspecialchars($row["category"] ?? '—') . "</td>
                    <td>" . $row["cena"] . " ₽</td>
                    <td>" . $row["kol"] . "</td>
                    <td>" . $row["srok"] . "</td>
                    <td><a href='tovar.php?id=" . $row["id"] . "'>Подробнее</a></td>
                    <td><input type='checkbox' name='id[]' value='" . $row["id"] . "' class='tovar-checkbox' data-name='" . htmlspecialchars($row["name"]) . "' data-cena='" . $row["cena"] . "'></td>
                </tr>";
    }
    echo $str;
}

// Функция для отображения корзины
function show_cart(){
    include "bdconnect.php";
    
    if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
        return "<tr><td colspan='6' align='center'>Корзина пуста</td></tr>";
    }
    
    $str = "";
    $total_sum = 0;
    $i = 0;
    
    foreach($_SESSION['cart'] as $item){
        $total = $item['cena'] * $item['quantity'];
        $total_sum += $total;
        
        $str .= "<tr>
                    <td>" . $item['id'] . "</td>
                    <td>" . htmlspecialchars($item['name']) . "</td>
                    <td>
                        <input type='number' name='kol[$i]' value='" . $item['quantity'] . "' min='1' max='99' class='quantity-input' data-id='" . $item['id'] . "' style='width:60px;'>
                    </td>
                    <td>" . $item['cena'] . " ₽</td>
                    <td class='total-" . $item['id'] . "'>" . $total . " ₽</td>
                    <td><input type='checkbox' name='delete_id[]' value='" . $item['id'] . "' class='delete-checkbox'></td>
                </tr>";
        $i++;
    }
    
    $str .= "<tr style='background:#f0f0f0; font-weight:bold;'>
                <td colspan='4' align='right'>Итого:</td>
                <td colspan='2' id='total-sum'>" . $total_sum . " ₽</td>
            </tr>";
    
    echo $str;
}
?>