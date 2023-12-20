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

    try {
        $searchIngredients = json_decode($_POST['ingredients'], true);

        if (!is_array($searchIngredients) || empty($searchIngredients)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Некорректные данные ингредиентов']);
            die();
        }

        // Исключаем "✖" из списка ингредиентов
        $searchIngredients = array_filter($searchIngredients, function ($ingredient) {
            return $ingredient !== '✖';
        });

        if (empty($searchIngredients)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Нет корректных ингредиентов для поиска']);
            die();
        }

        // Формируем строку для использования в предложении IN в SQL-запросе
        $ingredientPlaceholders = implode(',', array_fill(0, count($searchIngredients), '?'));

        $sql = "SELECT DISTINCT r.recipe_id, r.recipe_name, r.photo_link, r.cook_time, r.instruction, r.tag
        FROM recipies r
        JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id
        JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
        WHERE LOWER(i.ingredient_name) IN (" . implode(', ', array_fill(0, count($searchIngredients), '?')) . ")
        GROUP BY r.recipe_id, r.recipe_name, r.photo_link, r.cook_time, r.instruction, r.tag
        HAVING COUNT(DISTINCT i.ingredient_name) = ?";

        $params = array_merge(array_map('strtolower', $searchIngredients), [count($searchIngredients)]);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        error_log('Результат SQL-запроса: ' . print_r($recipes, true));

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
        echo json_encode($responseData);
    } catch (Exception $e) {
        // Если произошла ошибка, возвращаем JSON с информацией об ошибке
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Ингредиенты Произошла ошибка при обработке запроса поиска по ингредиентам.', 'details' => $e->getMessage()]);
        // echo json_encode(['error' => 'Ингредиенты Произошла ошибка при обработке запроса поиска по ингредиентам.']);
    }
}
