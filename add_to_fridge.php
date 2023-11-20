<?php
require "db.php";

// Проверка, авторизован ли пользователь
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingredient_id = $_POST['ingredient_id'];
    $quantity = $_POST['quantity'];

// Получение ID пользователя из сессии
    $user_id = $_SESSION['user_id'];

    // Проверка, существует ли ингредиент в холодильнике
    $checkStmt = $db->prepare("SELECT 1 FROM fridge_ingredients WHERE fridge_id = (SELECT fridge_id FROM user_fridges WHERE user_id = :user_id) AND ingredient_id = (SELECT ingredient_id FROM ingredients WHERE name = :ingredient_name)");
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->bindParam(':ingredient_name', $ingredient_id);


    if (!$checkStmt->execute()) {
        // Ошибка выполнения запроса
        $errorInfo = $checkStmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Ошибка выполнения запроса: ' . $errorInfo[2]]);
        exit();
    }

    if ($checkStmt->fetchColumn()) {
        // Ингредиент уже присутствует в холодильнике
        echo json_encode(['success' => false, 'message' => 'Ингредиент уже присутствует в холодильнике']);
        exit();
    }

// Инициализация $fridgeStmt перед использованием
    $fridgeStmt = null;
// Проверка, существует ли ингредиент в базе
    $stmt = $db->prepare("SELECT * FROM ingredients WHERE name = :ingredient_name");
    $stmt->bindParam(':ingredient_name', $ingredient_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Ингредиент существует, получаем его ID
        $ingredient_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $ingredient_id = $ingredient_data['ingredient_id'];

        // Получение количества из POST-запроса
        $quantity = $_POST['quantity'];

        // Инициализация $fridgeStmt перед использованием
        $fridgeStmt = $db->prepare("INSERT INTO fridge_ingredients (fridge_id, ingredient_id, quantity) VALUES ((SELECT fridge_id FROM user_fridges WHERE user_id = :user_id), :ingredient_id, :quantity) ON CONFLICT (fridge_id, ingredient_id) DO UPDATE SET quantity = fridge_ingredients.quantity + :quantity");
        $fridgeStmt->bindParam(':user_id', $user_id);
        $fridgeStmt->bindParam(':ingredient_id', $ingredient_id);
        $fridgeStmt->bindParam(':quantity', $quantity);
    }

    // Вставка ингредиента в холодильник
    if ($fridgeStmt !== null && $fridgeStmt->execute()) {
        // Успешный сценарий
        echo json_encode(['success' => true, 'message' => 'Ингредиент успешно добавлен в холодильник!!!', 'user_id' => $user_id, 'ingredient_id' => $ingredient_id, 'quantity' => $quantity]);
        exit();
    } else {
        // Ошибка при выполнении SQL-запроса
        echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении ингредиента в холодильник!!!', 'quantity' => $quantity]);
        exit();
    }


}

