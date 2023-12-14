<?php
session_start();
$host = "localhost";
$port = 5432;
$dbname = "users";
$user = "postgres";
$password = "080120";

try {
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Получаем user_id для данного пользователя
            $user_id = $user['user_id'];

            // Установка user_id в сессии
            $_SESSION['user_id'] = $user_id;

            // Перенаправление на ingredients.php
            header('Location: ingredients.php');
            exit();
        } else {
            $errors[] = 'Данные введены неверно';
        }
    } else {
        $errors[] = 'Пользователь с таким email не зарегистрирован';
    }

    if (!empty($errors)) {
        $errorMessage = implode('<br>', $errors);
        header("Location: authorization.html?error=" . urlencode($errorMessage));
        exit();
    }
}
?>
