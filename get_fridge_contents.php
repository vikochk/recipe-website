<?php
require "db.php";

// Запрос к базе данных для получения ингредиентов в холодильнике пользователя
$stmt = $db->prepare("SELECT * FROM fridge_ingredients WHERE fridge_id = (SELECT fridge_id FROM user_fridges WHERE user_id = :user_id)");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

// Отображение ингредиентов в холодильнике с кнопками удаления и формой редактирования
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<div>' . $row['ingredient_name'] . ' - ' . $row['quantity'] . ' <button class="deleteBtn" data-ingredient-id="' . $row['ingredient_id'] . '">Удалить</button></div>';
}
?>

