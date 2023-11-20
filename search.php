<?php
// Подключение к базе данных
require_once 'db.php';

if (isset($_GET['ingredient'])) {
    $search_term = $_GET['ingredient'] . '%';

    // Используйте подготовленные запросы, чтобы избежать SQL-инъекций
    $query = $db->prepare("SELECT name FROM public.ingredients WHERE name ILIKE :search_term");
    $query->bindParam(':search_term', $search_term);
    $query->execute();

    // Получаем результаты
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Отправляем результаты в формате JSON
    echo json_encode($results);
}
?>
