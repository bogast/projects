<?php
session_start();
include "bdconnect.php";

// Инициализация корзины если не существует
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

// Обработка добавления товара из списка (с чекбоксами)
if(isset($_POST["zakaz"]) && isset($_POST["id"]) && !empty($_POST["id"])){
    $mass_id = $_POST["id"];
    
    foreach($mass_id as $id){
        $id_tovar = intval($id);
        
        // Проверяем, есть ли уже такой товар в корзине
        $found = false;
        foreach($_SESSION['cart'] as &$item){
            if($item['id'] == $id_tovar){
                $item['quantity']++;
                $found = true;
                break;
            }
        }
        
        if(!$found){
            // Получаем информацию о товаре из БД
            $sql = "SELECT name, cena FROM tovars WHERE id = $id_tovar";
            $result = mysqli_query($link, $sql);
            if($row = mysqli_fetch_assoc($result)){
                $_SESSION['cart'][] = array(
                    'id' => $id_tovar,
                    'name' => $row['name'],
                    'cena' => $row['cena'],
                    'quantity' => 1
                );
            }
        }
    }
    
    // Устанавливаем сообщение об успехе
    $_SESSION['cart_message'] = "Товары успешно добавлены в корзину!";
    header("Location: table_tovars.php");
    exit();
}

// Обработка добавления одного товара (с страницы товара)
if(isset($_POST["zakaz_single"])){
    $id = intval($_POST["id_single"]);
    $name = $_POST["name_single"];
    $cena = floatval($_POST["cena_single"]);
    $quantity = intval($_POST["quantity_single"]);
    
    if($quantity > 0){
        // Проверяем, есть ли уже такой товар в корзине
        $found = false;
        foreach($_SESSION['cart'] as &$item){
            if($item['id'] == $id){
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if(!$found){
            $_SESSION['cart'][] = array(
                'id' => $id,
                'name' => $name,
                'cena' => $cena,
                'quantity' => $quantity
            );
        }
    }
    
    header("Location: cart.php");
    exit();
}

// Обработка оформления заказа
if(isset($_POST["zak"])){
    $id_user = isset($_SESSION["userid"]) ? $_SESSION["userid"] : 1;
    
    $data = date("Y-m-d H:i:s");
    $id_order = time();
    
    $ids = $_POST["id_tovar"] ?? array();
    $kol = $_POST["kol"] ?? array();
    $cena = $_POST["cena"] ?? array();
    
    if(!empty($ids)){
        for($i = 0; $i < count($ids); $i++){
            $id_tovar = intval($ids[$i]);
            $quantity = intval($kol[$i]);
            $cost = floatval($cena[$i]) * $quantity;
            
            $sql = "INSERT INTO orders (id_order, id_user, id_tovar, quantity, cost, datatime) 
                    VALUES ('$id_order', '$id_user', '$id_tovar', '$quantity', '$cost', '$data')";
            mysqli_query($link, $sql) or die("Ошибка заказа: " . mysqli_error($link));
        }
    }
    
    // Очищаем корзину
    $_SESSION['cart'] = array();
    
    header("Location: uspex.php?i=4");
    exit();
}

// Обработка удаления товаров из корзины
if(isset($_POST["delete_selected"]) && isset($_POST["delete_id"]) && !empty($_POST["delete_id"])){
    $delete_ids = $_POST["delete_id"];
    foreach($_SESSION['cart'] as $key => $item){
        if(in_array($item['id'], $delete_ids)){
            unset($_SESSION['cart'][$key]);
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Обработка обновления количества
if(isset($_POST["update_cart"]) && isset($_POST["id_tovar"]) && isset($_POST["kol"])){
    $ids = $_POST["id_tovar"];
    $kol = $_POST["kol"];
    
    foreach($_SESSION['cart'] as &$item){
        foreach($ids as $index => $id){
            if($item['id'] == intval($id)){
                $item['quantity'] = intval($kol[$index]);
                if($item['quantity'] < 1){
                    $item['quantity'] = 1;
                }
                break;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

// Если нет POST запроса, перенаправляем
if(empty($_POST)){
    header("Location: table_tovars.php");
    exit();
}
?>