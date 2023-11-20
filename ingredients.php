<?php
session_start();
require "db.php";

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Добавление ингредиента в холодильник
if (isset($_POST['add_to_fridge'])) {
    $ingredient_id = $_POST['ingredient_id'];
    $quantity = $_POST['quantity'];

    // Проверяем, существует ли уже такой ингредиент в холодильнике пользователя
    $stmt = $db->prepare("SELECT * FROM fridge_ingredients WHERE fridge_id IN (SELECT fridge_id FROM user_fridges WHERE user_id = :user_id) AND ingredient_id = :ingredient_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':ingredient_id', $ingredient_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Если ингредиент уже существует, обновляем количество
        $updateStmt = $db->prepare("UPDATE fridge_ingredients SET quantity = quantity + :quantity WHERE fridge_id IN (SELECT fridge_id FROM user_fridges WHERE user_id = :user_id) AND ingredient_id = :ingredient_id");
        $updateStmt->bindParam(':user_id', $user_id);
        $updateStmt->bindParam(':ingredient_id', $ingredient_id);
        $updateStmt->bindParam(':quantity', $quantity);
        $updateStmt->execute();
    } else {
        // Если ингредиент не существует, добавляем его
        $insertStmt = $db->prepare("INSERT INTO fridge_ingredients (fridge_id, ingredient_id, quantity) VALUES ((SELECT fridge_id FROM user_fridges WHERE user_id = :user_id), :ingredient_id, :quantity)");
        $insertStmt->bindParam(':user_id', $user_id);
        $insertStmt->bindParam(':ingredient_id', $ingredient_id);
        $insertStmt->bindParam(':quantity', $quantity);
        $insertStmt->execute();
    }
}

// Получаем список ингредиентов для отображения
$ingredientsStmt = $db->prepare("SELECT * FROM ingredients");
$ingredientsStmt->execute();
$ingredients = $ingredientsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML-код для поиска ингредиентов и добавления в холодильник -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск ингредиентов</title>
    <script src="search.js"></script>
</head>
<body>
<h1>Поиск ингредиентов</h1>
<!-- Форма для поиска ингредиентов -->
<form action="search.php" method="get">
    <label for="ingredient">Введите ингредиент:</label>
    <input type="text" name="ingredient" id="ingredient" oninput="updateResults()">
    <input type="submit" value="Поиск">
</form>

<!-- Контейнер для результатов поиска -->
<div id="results"></div>

<!-- Форма для добавления ингредиентов в холодильник -->
<form method="POST" action="ingredients.php" onsubmit="return addToFridge()">
    <p>Выберите ингредиент:
        <select name="ingredient_id">
            <?php foreach ($ingredients as $ingredient): ?>
                <option value="<?= $ingredient['ingredient_id'] ?>"><?= $ingredient['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>Укажите количество: <input type="number" name="quantity" min="1" value="1"></p>
    <p><button type="submit" name="add_to_fridge">Добавить в холодильник</button></p>
</form>

<!-- Ваш HTML-код, например, заголовок страницы -->

</body>
</html>
