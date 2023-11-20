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

    try {
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
            if ($updateStmt->execute()) {
                echo "Ингредиент успешно обновлен!";
            } else {
                echo "Ошибка при обновлении ингредиента: " . implode(" ", $updateStmt->errorInfo());
            }
        } else {
            // Если ингредиент не существует, добавляем его
            $insertStmt = $db->prepare("INSERT INTO fridge_ingredients (fridge_id, ingredient_id, quantity) VALUES ((SELECT fridge_id FROM user_fridges WHERE user_id = :user_id), :ingredient_id, :quantity)");
            $insertStmt->bindParam(':user_id', $user_id);
            $insertStmt->bindParam(':ingredient_id', $ingredient_id);
            $insertStmt->bindParam(':quantity', $quantity);
            if ($insertStmt->execute()) {
                echo "Ингредиент успешно добавлен!";
            } else {
                echo "Ошибка при добавлении ингредиента: " . implode(" ", $insertStmt->errorInfo());
            }
        }
    } catch (PDOException $e) {
        echo "Ошибка при выполнении запроса: " . $e->getMessage();
    }
}

// Получаем список ингредиентов для отображения
$ingredientsStmt = $db->prepare("SELECT * FROM ingredients");
$ingredientsStmt->execute();
$ingredients = $ingredientsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск ингредиентов</title>
    <!-- Подключение jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Подключение jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- Подключение вашего скрипта search.js -->
    <script src="search.js"></script>
</head>
<body>
<h1>Личный холодильник</h1>

<!-- Форма для добавления ингредиентов в холодильник -->
<form method="POST" action="ingredients.php" onsubmit="return addToFridge()">
    <p>Выберите ингредиент:
        <input type="text" name="ingredient" id="addIngredient" class="autocomplete">
        <input type="hidden" name="ingredient_id" id="selected_ingredient_id">
    </p>
    <p>Укажите количество: <input type="number" name="quantity" min="1" value="1"></p>
    <p><button type="submit" name="add_to_fridge">Добавить в холодильник</button></p>
</form>

<!-- Включаем jQuery и jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- Подключаем ваш скрипт search.js -->
<script src="search.js"></script>

</body>
</html>

