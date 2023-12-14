<?php
$host = "localhost";
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

$data = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    $email = trim($data['email']);
    if (empty($email)) {
        $errors[] = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Адрес электронной почты указан некорректно';
    }
    // Проверяем, существует ли почта в базе данных
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $errors[] = 'Данная почта уже зарегистрирована';
    }

    $password = $data['password'];
    $repeatPassword = $data['repeatPassword'];

    if (empty($password)) {
        $errors[] = 'Введите пароль';
    }

    if ($password !== $repeatPassword) {
        $errors[] = 'Повторный пароль введен неверно';
    }

    if (empty($errors)) {
        // Регистрация нового пользователя с хэшированным паролем
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashedPassword]);

        header("Location: registration.html");
        exit();
    } else {
        $errorMessage = implode('<br>', $errors);
        header("Location: registration.html?error=" . urlencode($errorMessage));
        exit();
    }
}
?>
