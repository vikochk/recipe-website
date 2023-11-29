<?php
require "db.php";

$data = $_POST;

if (isset($data['do_login'])) {
    $errors = [];

    $email = trim($data['email']);
    $password = $data['password'];

    // Проверка подключения к базе данных
    try {
        $db = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Ошибка подключения к базе данных: ' . $e->getMessage());
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

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

<form method="POST" action="login.php">
    <!-- Ваши поля для ввода данных -->
    <p><strong>Ваша электронная почта</strong>:</p>
    <input type="text" name="email" value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>">
    </p>
    <p><strong>Ваш пароль</strong>:</p>
    <input type="password" name="password">
    </p>
    <p>
        <button type="submit" name="do_login">Войти</button>
    </p>
</form>
