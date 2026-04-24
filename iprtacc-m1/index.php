<?php 
session_start();
include "bdconnect.php"; 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Склад товаров</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .hero { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; padding: 80px 0; }
        .cart-badge {
            background-color: #ff9800;
            color: white;
            border-radius: 20px;
            padding: 5px 15px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- ШАПКА -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"> СкладТоваров</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="vvod_tovars.php"> Добавить товар</a>
                <a class="nav-link" href="table_tovars.php">Каталог</a>
                <a class="nav-link" href="ud_tovars.php"> Управление</a>
                <a class="nav-link" href="cart.php"> Корзина 
                    <?php 
                    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if($cart_count > 0): 
                    ?>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-link" href="rec.php">📝 Регистрация</a>
                <a class="nav-link" href="login.php">🔑 Войти</a>
            </div>
        </div>
    </nav>

    <!-- ГЕРОЙ -->
    <div class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Склад товаров</h1>
            <p class="lead">PHP + MySQL</p>
            <a href="vvod_tovars.php" class="btn btn-light btn-lg">Добавить новый товар</a>
            <a href="table_tovars.php" class="btn btn-outline-light btn-lg ms-2">Смотреть каталог</a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5> Каталог</h5>
                        <p>Фильтры, поиск, сортировка</p>
                        <a href="table_tovars.php" class="btn btn-primary">Перейти</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5>✏️ Редактирование</h5>
                        <p>Цена и количество</p>
                        <a href="ud_tovars.php" class="btn btn-primary">Перейти</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5> Корзина</h5>
                        <p>Оформление заказов</p>
                        <a href="cart.php" class="btn btn-primary">Перейти</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>