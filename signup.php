<?php
require "db.php";

$data = $_POST;
if (isset($data['do_signup'])) {
    // Инициализируем массив ошибок
    $errors = array();

    $name = '';
    $email = '';
    $password = '';

    $name = trim($data['name']);
    if ($name == '') {
        $errors[] = 'Введите имя';
    }

    $email = trim($data['email']);
    if ($email == '') {
        $errors[] = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Адрес электронной почты указан некорректно';
    }

    $password = $data['password'];
    if ($password == '') {
        $errors[] = 'Введите пароль';
    }

    $password_2 = $data['password_2'];
    if ($password_2 != $password) {
        $errors[] = 'Повторный пароль введен неверно';
    }
    // Проверяем, существует ли почта в базе данных
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $errors[] = 'Данная почта уже зарегистрирована';
    }

    if (empty($errors)) {
        // Регистрация нового пользователя
        $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        if ($stmt->execute()) {
            echo '<div style="color: green;">Вы успешно зарегистрированы</div><hr>';
        } else {
            echo '<div style="color: red;">Ошибка при регистрации пользователя</div><hr>';
        }
//        // Отправляем приветственное письмо
//        $to = $email;
//        $subject = 'Добро пожаловать!';
//        $message = 'Спасибо за регистрацию на нашем сайте, ' . $name . '! Мы рады видеть вас.';
//        $headers = 'From: recipesbookproject@gmail.com' . "\r\n" .
//            'Reply-To: recipesbookproject@gmail.com' . "\r\n" .
//            'X-Mailer: PHP/' . phpversion();
//
//        // Проверяем, удалось ли отправить письмо
//        if (mail($to, $subject, $message, $headers)) {
//            echo '<div style="color: green;">Вы успешно зарегистрированы. Приветственное письмо отправлено на вашу почту.</div><hr>';
//        } else {
//            echo '<div style="color: red;">Ошибка при отправке приветственного письма.</div><hr>';
//        }
    } else {
        echo '<div style="color: red;">' . implode('<br>', $errors) . '</div><hr>';
    }
}
?>

<form action="/signup.php" method="POST">
    <input type="hidden" name="do_signup" value="1">
    <p>
    <p><strong>Ваше имя</strong>:</p>
    <input type="text" name="name" value="<?php echo isset($data['name']) ? htmlspecialchars($data['name']) : ''; ?>">
    </p>
    <p>
    <p><strong>Ваша электронная почта</strong>:</p>
    <input type="text" name="email" value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>">
    </p>
    <p>
    <p><strong>Ваш пароль</strong>:</p>
    <input type="password" name="password">
    </p>
    <p>
    <p><strong>Повторите пароль</strong>:</p>
    <input type="password" name="password_2">
    </p>
    <p>
        <button type="submit">Зарегистрироваться</button>
    </p>
</form>
