<?php
session_start();

include "bdconnect.php";
include "func.php";

if(!isset($_SESSION["logged"]) || $_SESSION["logged"] != "1") {
    header("Location: login.php");
    exit();
}

$user = null;
if(isset($_SESSION["userid"])) {
    $stmt = mysqli_prepare($link, "SELECT id, login, name FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["userid"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if(!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Проверка, является ли пользователь администратором (id = 1)
$isAdmin = ($_SESSION["userid"] == "1");

$orders = [];

if($isAdmin) {
    $orders_query = "SELECT o.id_order, o.id_user, o.id_tovar, o.quantity, o.cost, o.datatime, 
                            t.name as tovar_name, u.name as user_name, u.login as user_login
                     FROM orders o 
                     LEFT JOIN tovars t ON o.id_tovar = t.id 
                     LEFT JOIN users u ON o.id_user = u.id 
                     ORDER BY o.datatime DESC";
} else {
    $orders_query = "SELECT o.id_order, o.id_tovar, o.quantity, o.cost, o.datatime, t.name as tovar_name 
                     FROM orders o 
                     LEFT JOIN tovars t ON o.id_tovar = t.id 
                     WHERE o.id_user = " . intval($_SESSION["userid"]) . " 
                     ORDER BY o.datatime DESC";
}

$orders_result = mysqli_query($link, $orders_query);
if($orders_result) {
    while($row = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $row;
    }
}

$grouped_orders = [];
foreach($orders as $order) {
    $order_id = $order['id_order'];
    if(!isset($grouped_orders[$order_id])) {
        $grouped_orders[$order_id] = [
            'datetime' => $order['datatime'],
            'items' => [],
            'total' => 0
        ];
        if($isAdmin && isset($order['user_name'])) {
            $grouped_orders[$order_id]['user_name'] = $order['user_name'];
            $grouped_orders[$order_id]['user_login'] = $order['user_login'];
            $grouped_orders[$order_id]['user_id'] = $order['id_user'];
        }
    }
    $grouped_orders[$order_id]['items'][] = $order;
    $grouped_orders[$order_id]['total'] += $order['cost'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .profile { 
            max-width: 1000px; 
            margin: 0 auto; 
            padding: 20px; 
            border: 1px solid #ccc; 
            border-radius: 10px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .info { margin: 10px 0; padding: 8px; background-color: #f9f9f9; border-radius: 4px; }
        .info strong { display: inline-block; width: 100px; }
        .links { margin-top: 20px; text-align: center; }
        .links a { 
            display: inline-block;
            margin: 5px 10px;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .links a:hover { background-color: #45a049; }
        .admin-badge { 
            background-color: #ff9800; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 15px;
            text-align: center;
        }
        h2 { text-align: center; color: #333; }
        .welcome { text-align: center; color: #4CAF50; }
        
        .orders-section {
            margin-top: 30px;
            border-top: 2px solid #eee;
            padding-top: 20px;
        }
        .orders-section h3 {
            color: #333;
            margin-bottom: 20px;
        }
        .order-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .order-header {
            background: #4CAF50;
            color: white;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .order-header span {
            font-size: 14px;
        }
        .order-header strong {
            font-size: 16px;
        }
        .order-user-info {
            background: #e8f5e9;
            padding: 8px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: #2e7d32;
        }
        .order-items {
            padding: 15px;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items th, .order-items td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-items th {
            background: #e8e8e8;
            font-weight: bold;
        }
        .order-total {
            text-align: right;
            padding: 10px 15px;
            background: #f0f0f0;
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #ddd;
        }
        .no-orders {
            text-align: center;
            padding: 40px;
            color: #666;
            background: #fafafa;
            border-radius: 8px;
        }
        .admin-stats {
            background: #fff3e0;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="profile">
        <h2>Личный кабинет</h2>
        
        <?php if($isAdmin): ?>
            <div class="admin-badge"> Администратор</div>
            <div class="admin-stats">
            </div>
        <?php endif; ?>
        
        <?php if($user): ?>
            <div class="welcome">
                <h3>Здравствуйте, <?php echo htmlspecialchars($user["name"]); ?>!</h3>
            </div>
            
            <div class="info">
                <strong>ID:</strong> <?php echo htmlspecialchars($user["id"]); ?>
            </div>
            
            <div class="info">
                <strong>Логин:</strong> <?php echo htmlspecialchars($user["login"]); ?>
            </div>
            
            <div class="info">
                <strong>Имя:</strong> <?php echo htmlspecialchars($user["name"]); ?>
            </div>
            
   
            <?php if($isAdmin): ?>
                <div class="info" style="background-color: #fff3e0; margin-top: 20px;">
                    <strong> Админ-панель:</strong><br>
                    <a href="admin/users.php" style="display: inline-block; margin-top: 10px;">Управление пользователями</a>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <p style="color: red; text-align: center;">Ошибка: данные пользователя не найдены</p>
        <?php endif; ?>
        
        <div class="orders-section">
            <h3> <?php echo $isAdmin ? 'Все заказы пользователей' : 'Мои заказы'; ?></h3>
            
            <?php if(empty($grouped_orders)): ?>
                <div class="no-orders">
                    <p><?php echo $isAdmin ? 'В системе пока нет заказов' : 'У вас пока нет заказов'; ?></p>
                    <a href="table_tovars.php" style="color: #4CAF50;">Перейти к товарам →</a>
                </div>
            <?php else: ?>
                <?php foreach($grouped_orders as $order_id => $order_data): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <strong>Заказ №<?php echo htmlspecialchars($order_id); ?></strong>
                            <span> <?php echo date('d.m.Y H:i:s', strtotime($order_data['datetime'])); ?></span>
                        </div>
                        
                        <?php if($isAdmin && isset($order_data['user_name'])): ?>
                            <div class="order-user-info">
                                 Пользователь: <strong><?php echo htmlspecialchars($order_data['user_name']); ?></strong> 
                                (логин: <?php echo htmlspecialchars($order_data['user_login']); ?>, ID: <?php echo $order_data['user_id']; ?>)
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-items">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID товара</th>
                                        <th>Наименование</th>
                                        <th>Количество</th>
                                        <th>Стоимость</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($order_data['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id_tovar']; ?></td>
                                        <td><?php echo htmlspecialchars($item['tovar_name'] ?? 'Товар удалён'); ?></td>
                                        <td><?php echo $item['quantity']; ?> шт.</td>
                                        <td><?php echo number_format($item['cost'], 2); ?> ₽</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="order-total">
                            Итого: <?php echo number_format($order_data['total'], 2); ?> ₽
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="links">
            <a href="logout.php">Выйти из аккаунта</a>
            <a href="index.php">На главную</a>
            <a href="cart.php"> Корзина</a>
        </div>
    </div>
</body>
</html>