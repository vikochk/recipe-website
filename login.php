<?php
require "db.php";

$data = $_POST;

if (isset($data['do_login'])) {
    $errors = array();

    $email = trim($data['email']);
    $password = $data['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Логин существует
        if (password_verify($password, $user['password'])) {
            // Пароль верный
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
    <p><strong>Ваша электронная почта</strong>:</p>
    <input type="text" name="email" value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>">
    </p>
    <p>
    <p><strong>Ваш пароль</strong>:</p>
    <input type="password" name="password">
    </p>
    <p>
    <p>
        <button type="submit" name ="do_login">Войти</button>
    </p>
</form>
