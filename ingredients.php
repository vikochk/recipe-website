<?php
session_start();
require "db.php";

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение содержимого холодильника
$fridgeStmt = $db->prepare("SELECT f.ingredient_id, i.name, f.quantity FROM fridge_ingredients f
                           JOIN ingredients i ON f.ingredient_id = i.ingredient_id
                           WHERE f.fridge_id = (SELECT fridge_id FROM user_fridges WHERE user_id = :user_id)");
$fridgeStmt->bindParam(':user_id', $user_id);
$fridgeStmt->execute();

$fridgeContents = $fridgeStmt->fetchAll(PDO::FETCH_ASSOC);


// Добавление ингредиента в холодильник
if (isset($_POST['add_to_fridge'])) {
    $ingredient_id = $_POST['ingredient_id'];
    $quantity = $_POST['quantity'];

    try {
        // Проверка, существует ли ингредиент в базе
        $stmt = $db->prepare("SELECT * FROM ingredients WHERE name = :ingredient_name");
        $stmt->bindParam(':ingredient_name', $ingredient_id);
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
    } catch (PDOException $e) {
        echo "Ошибка при обработке запроса: " . $e->getMessage();
    }
}
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
<h1>Управление холодильником</h1>

<!-- Вывод содержимого холодильника в виде списка -->
<h2>Содержимое холодильника</h2>

<?php if (empty($fridgeContents)): ?>
<!-- Сообщение, если холодильник пуст -->
<p>Холодильник пуст. Добавьте ингредиенты, чтобы начать!</p>
<?php else: ?>
<ul>
    <?php foreach ($fridgeContents as $ingredient): ?>
        <li><?php echo $ingredient['name'] . ' - ' . $ingredient['quantity']; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>



<button type="button" id="editFridgeBtn">Редактировать холодильник</button>

<!-- Форма редактирования -->
<div id="editFridgeForm" style="display: none;">
    <h2>Редактирование холодильника</h2>
    <!-- Контейнер для сообщений -->
    <div id="messageContainer"></div>
    <!-- Форма для добавления ингредиентов в холодильник -->
    <form method="POST" action="#" onsubmit="return addToFridge()">
        <p>Выберите ингредиент:
            <input type="text" name="ingredient_id" id="addIngredient" class="autocomplete">
            <input type="hidden" name="ingredient_id" id="selected_ingredient_id">
        </p>
        <p>Укажите количество: <input type="number" name="quantity" min="1" value="1"></p>
        <p><button type="submit" name="add_to_fridge">Сохранить изменения</button></p>
    </form>
</div>


<!-- Включаем jQuery и jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- Подключаем ваш скрипт search.js -->
<script src="search.js"></script>

</body>
</html>

