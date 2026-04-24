<?php
session_start();
// Подключение к БД
include("bdconnect.php");

// Проверяем, авторизован ли пользователь, если ДА, отправляем его в личный кабинет
if(isset($_SESSION["logged"]) && $_SESSION["logged"] == "1") {
    header("Location: profile.php");
    exit();
}

$hasErrors = false;

// Аутентификация (проверка логина и пароля)
if(isset($_POST["auth"])) {
    $login = mysqli_real_escape_string($link, $_POST["login"]);
    $password = $_POST["password"];
    $dataSent = isset($_POST["dataSent"]) ? $_POST["dataSent"] : 0;
    
    if($dataSent == 1) {
        // Используем подготовленный запрос для безопасности
        $stmt = mysqli_prepare($link, "SELECT id, login, hash, name FROM users WHERE login = ?");
        mysqli_stmt_bind_param($stmt, "s", $login);
        mysqli_stmt_execute($stmt);
        $q = mysqli_stmt_get_result($stmt);
        $nq = mysqli_num_rows($q);
        
        if($nq == 0) {
            $hasErrors = true;
        } elseif($nq == 1) {
            $mfq = mysqli_fetch_array($q);
            
            // Проверяем пароль через password_verify (так как поле называется hash)
            if(password_verify($password, $mfq["hash"])) {
                $_SESSION["logged"] = 1;
                $_SESSION["userid"] = $mfq["id"]; // Используем поле id
                $_SESSION["login"] = $mfq["login"]; // Сохраняем логин в сессию
                $_SESSION["name"] = $mfq["name"]; // Сохраняем имя в сессию
                
                header("Location: profile.php");
                exit();
            } else {
                $hasErrors = true;
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Вход на сайт</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        form { max-width: 300px; margin: 0 auto; }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 8px; 
            margin: 5px 0 15px 0;
            box-sizing: border-box;
        }
        input[type="submit"] { 
            width: 100%; 
            padding: 10px; 
            background-color: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer; 
        }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; text-align: center; margin-top: 10px; }
        .links { text-align: center; margin-top: 20px; }
        a { margin: 0 10px; text-decoration: none; color: #333; }
        a:hover { text-decoration: underline; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Вход на сайт</h2>
    <form method="POST">
        <input type="text" name="login" placeholder="Логин" required/>
        <input type="password" name="password" placeholder="Пароль" required/>
        <input type="hidden" name="dataSent" value="1"/>
        <input type="submit" value="Войти" name="auth"/>
    </form>
    
    <?php if($hasErrors): ?>
        <div class="error">Вы ввели неправильный логин или пароль</div>
    <?php endif; ?>
    
    <div class="links">
        <a href="reс.php">Регистрация</a><br><br>
        <a href="index.php">На главную</a>
    </div>
</body>
</html>