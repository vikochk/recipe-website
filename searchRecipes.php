<?php
$host = "localhost";
$port = 5432;
$dbname = "users";
$user = "postgres";
$password = "rootroot";
ini_set('display_errors', 1);

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения к базе данных: ' . $e->getMessage()]);
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag'])) {
    try {
        $tagQuery = $_POST['tag'];
        $searchWords = explode(' ', $tagQuery);

        $placeholders = implode(' AND ', array_map(function ($word) {
            return 'LOWER(tag) LIKE LOWER(?)';
        }, $searchWords));
        $params = array_map(function ($word) {
            return '%' . $word . '%';
        }, $searchWords);

        $sql = "SELECT * FROM recipies WHERE $placeholders ORDER BY recipe_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        header('Content-Type: application/json');
        echo json_encode($responseData);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Произошла ошибка при обработке запроса поиска по блюдам: ' . $e->getMessage()]);
    }
}
