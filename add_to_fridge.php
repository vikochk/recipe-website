<?php
require "db.php";

// Проверка, авторизован ли пользователь
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php"); // Перенаправление на страницу авторизации
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingredient_id = $_POST['ingredient_id'];

    // Получение ID пользователя из сессии
    $user_id = $_SESSION['user_id'];

    // Проверка, существует ли ингредиент в базе
    $stmt = $db->prepare("SELECT * FROM ingredients WHERE ingredient_id = :ingredient_id");
    $stmt->bindParam(':ingredient_id', $ingredient_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Ингредиент существует, добавляем в холодильник
        $fridgeStmt = $db->prepare("INSERT INTO fridge_ingredients (fridge_id, ingredient_id, quantity) VALUES ((SELECT fridge_id FROM user_fridges WHERE user_id = :user_id), :ingredient_id, 1) ON CONFLICT (fridge_id, ingredient_id) DO UPDATE SET quantity = fridge_ingredients.quantity + 1");
        $fridgeStmt->bindParam(':user_id', $user_id);
        $fridgeStmt->bindParam(':ingredient_id', $ingredient_id);

        if ($fridgeStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Ингредиент успешно добавлен в холодильник']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении ингредиента в холодильник']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Ингредиент не найден в базе данных']);
        exit();
    }
}
?>
