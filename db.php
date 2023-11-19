<?php
$host = "localhost"; // Хост PostgreSQL сервера
$port = 5432; // Порт PostgreSQL сервера
$dbname = "users"; // Имя вашей базы данных
$user = "postgres"; // Имя пользователя PostgreSQL
$password = "080120"; // Пароль пользователя PostgreSQL

try {
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    die();
}
?>
