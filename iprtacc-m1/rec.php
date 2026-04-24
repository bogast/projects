<?php
session_start(); // Добавлено: нужно для работы с сессиями
include "bdconnect.php";

$hasErrors = false;
$errorMessage = "";

if(isset($_POST["reg"])){
    $name = htmlspecialchars(trim($_POST["name"]));
    $login = htmlspecialchars(trim($_POST["login"]));
    $password = $_POST["password"]; // Не применяем htmlspecialchars к паролю
    
    // Валидация данных
    if(empty($login) || empty($password) || empty($name)) {
        $hasErrors = true;
        $errorMessage = "Заполните все поля!";
    } elseif(strlen($password) < 6) {
        $hasErrors = true;
        $errorMessage = "Пароль должен содержать минимум 6 символов!";
    } else {
        // Исправлено: поле называется id, а не id_user
        $check_stmt = mysqli_prepare($link, "SELECT id FROM users WHERE login = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $login);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $nq = mysqli_num_rows($check_result);
        
        if($nq > 0){
            $hasErrors = true;
            $errorMessage = "Логин уже занят!";
        } else {
            // Хеширование пароля
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Исправлено: порядок полей соответствует таблице (login, hash, name)
            $insert_stmt = mysqli_prepare($link, "INSERT INTO users (login, hash, name) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "sss", $login, $hash, $name);
            
            if(mysqli_stmt_execute($insert_stmt)){
                // Получаем ID последней вставленной записи (поле id)
                $_SESSION["userid"] = mysqli_insert_id($link);
                $_SESSION["logged"] = 1;
                $_SESSION["login"] = $login; // Сохраняем логин в сессию
                $_SESSION["name"] = $name;   // Сохраняем имя в сессию
                
                // Перенаправляем в профиль
                header("Location: profile.php");
                exit(); // Важно: прекращаем выполнение скрипта
            } else {
                $hasErrors = true;
                $errorMessage = "Ошибка регистрации. Попробуйте позже.";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        form { max-width: 300px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 8px; 
            margin: 5px 0 10px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] { 
            width: 100%; 
            padding: 10px; 
            margin-top: 15px;
            background-color: #4CAF50; 
            color: white; 
            border: none; 
            border-radius: 4px;
            cursor: pointer; 
        }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; text-align: center; margin-top: 10px; padding: 10px; background-color: #ffeeee; border-radius: 4px; }
        .success { color: green; text-align: center; margin-top: 10px; }
        h2 { text-align: center; color: #333; }
        .links { text-align: center; margin-top: 20px; }
        a { color: #4CAF50; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Регистрация нового пользователя</h2>
    <form action="" method="post">
        <label for="name">Имя:</label>
        <input type="text" name="name" id="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        
        <label for="login">Логин:</label>
        <input type="text" name="login" id="login" required value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
        
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>
        
        <input type="submit" value="Зарегистрироваться" name="reg">
    </form>
    
    <?php if($hasErrors): ?>
        <div class="error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div class="links">
        <a href="login.php">Уже есть аккаунт? Войдите</a><br><br>
        <a href="index.php">На главную</a>
    </div>
</body>
</html>