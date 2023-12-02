<?php
require "db.php";

$data = $_POST;

if (isset($data['do_login'])) {
    $errors = [];

    $host = "localhost"; // Хост PostgreSQL сервера
    $port = 5432; // Порт PostgreSQL сервера
    $dbname = "users"; // Имя вашей базы данных
    $user = "postgres"; // Имя пользователя PostgreSQL
    $password = "080120"; // Пароль пользователя PostgreSQL

    error_log("Точка 1"); // Первая точка для отладки

    try {
        $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
        error_log("Соединение с базой данных успешно установлено"); // Точка для отладки
    } catch (PDOException $e) {
        echo "Ошибка подключения к базе данных: " . $e->getMessage();
        die();
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    error_log("Точка 2"); // Вторая точка для отладки

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Пароль верный

            // Начало сессии с использованием безопасной случайной строки
            session_start([
                'cookie_lifetime' => 86400,
                'read_and_close' => true,
            ]);
            $_SESSION['user_id'] = $user['user_id'];

            // Перенаправление на страницу поиска ингредиентов
            header('Location: ingredients.php');
            exit(); // Важно прервать выполнение кода после перенаправления
        } else {
            $errors[] = 'Данные введены неверно';
        }
    } else {
        $errors[] = 'Пользователь с таким email не зарегистрирован';
    }

    if (!empty($errors)) {
        echo '<div style="color: red;">' . implode('<br>', $errors) . '</div><hr>';
    }
}
?>
