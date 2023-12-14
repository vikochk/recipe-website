<?php
// Подключение к базе данных
$host = "localhost"; // Хост PostgreSQL сервера
$port = 5432; // Порт PostgreSQL сервера
$dbname = "users"; // Имя вашей базы данных
$user = "postgres"; // Имя пользователя PostgreSQL
// $password = "080120"; // Пароль пользователя PostgreSQL
$password = "rootroot"; // Пароль пользователя PostgreSQL
ini_set('display_errors', 1);
// echo "Скрипт начал выполнение";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Успешное подключение к базе данных!";
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения к базе данных: ' . $e->getMessage()]);
    die();
}

// Обработка запроса поиска
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingredients'])) {



    // Вывод результатов
    // if (!empty($recipes)) {
    //     foreach ($recipes as $recipe) {
    //         echo "<div class='recipe-item'>";
    //         echo "<img src='{$recipe['photo_link']}' alt='Recipe Image'>";
    //         echo "<h3>{$recipe['recipe_name']}</h3>";
    //         echo "<p>Время приготовления: {$recipe['cook_time']}</p>";
    //         echo "<hr>";
    //         echo "</div>";
    //     }
    // } else {
    //     echo "<p>Нет рецептов с такими ингредиентами.</p>";
    // }

    try {
        // Выводим отладочное сообщение
        // error_log('Ингредиенты для поиска: ' . print_r($searchIngredients, true));
        //
        $searchIngredients = json_decode($_POST['ingredients'], true);

        if (!is_array($searchIngredients)) {
            $searchIngredients = [];
        }

        // Формируем строку для использования в предложении IN в SQL-запросе
        $ingredientPlaceholders = rtrim(str_repeat('?,', count($searchIngredients)), ',');

        // SQL-запрос для поиска рецептов по ингредиентам
        // $sql = "SELECT DISTINCT r.recipe_id, r.recipe_name, r.photo_link, r.cook_time, r.instruction, r.tag
        //     FROM recipies r
        //     JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id
        //     JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
        //     WHERE LOWER(i.ingredient_name) IN (LOWER(" . implode('), LOWER(', array_fill(0, count($searchIngredients), '?')) . "))
        //     ORDER BY r.recipe_name";

        $sql = "SELECT DISTINCT r.recipe_id, r.recipe_name, r.photo_link, r.cook_time, r.instruction, r.tag
        FROM recipies r
        JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id
        JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
        WHERE LOWER(i.ingredient_name) IN (" . implode(', ', array_fill(0, count($searchIngredients), '?')) . ")
        ORDER BY r.recipe_name";


        $stmt = $pdo->prepare($sql);
        $stmt->execute($searchIngredients);
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('Результат SQL-запроса: ' . print_r($recipes, true));
        // Вывод результатов
        $responseData = [];

        if (!empty($recipes)) {
            foreach ($recipes as $recipe) {
                $responseData[] = [
                    'recipe_id' => $recipe['recipe_id'],
                    'recipe_name' => $recipe['recipe_name'],
                    'photo_link' => $recipe['photo_link'],
                    'cook_time' => $recipe['cook_time'],
                ];
            }
        } else {
            $responseData['error'] = 'Нет рецептов с такими ингредиентами.';
        }

        // Устанавливаем заголовок для ответа, указывая, что это JSON
        header('Content-Type: application/json');
        // $logData = '';

        // foreach ($recipes as $recipe) {
        //     $logData .= "Recipe: " . print_r($recipe, true) . "\n";
        // }

        // // Запись в файл
        // file_put_contents('file.log', $logData, FILE_APPEND);


        // Возвращение данных в виде JSON
        // Выводим данные в формате JSON
        echo json_encode($responseData);
    } catch (Exception $e) {
        // Если произошла ошибка, возвращаем JSON с информацией об ошибке
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Произошла ошибка при обработке запроса.']);
    }
}
