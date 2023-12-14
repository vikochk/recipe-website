<?php
session_start();
$host = "localhost"; // Хост PostgreSQL сервера
$port = 5432; // Порт PostgreSQL сервера
$dbname = "users"; // Имя вашей базы данных
$user = "postgres"; // Имя пользователя PostgreSQL
// $password = "080120"; // Пароль пользователя PostgreSQL
$password = "rootroot"; // Пароль пользователя PostgreSQL


// echo "Recipe ID: " . $recipeId;

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    die();
}

// Выполнение запроса
try {
    // $recipeId = 26; // Здесь укажите нужный вам recipe_id
    $recipeId = isset($_GET['recipe_id']) ? $_GET['recipe_id'] : null;
    $query = "SELECT recipe_name, photo_link, cook_time, instruction, ingredient_name, ingredient_amount 
    FROM recipies 
    INNER JOIN recipe_ingredients ON recipies.recipe_id = recipe_ingredients.recipe_id
    JOIN ingredients ON ingredients.ingredient_id = recipe_ingredients.ingredient_id
    WHERE recipies.recipe_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$recipeId]);

    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    // Получаем все ингредиенты для рецепта
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Ошибка выполнения запроса: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Recipes</title>
    <style>
        .recipe-container-r {
            padding-top: 30px;
            padding-left: 42px;
            padding-right: 42px;
            padding-bottom: 10px;
            height: 70vh;
            overflow-y: auto; /* Полосы прокрутки при необходимости */
            background-image: url('mainground.png'); 
            background-position: center/50px; 
            background-repeat: no-repeat;
            margin-left: 142px;
            position: relative;
        }

        .recipe-title-r {
            margin: 0;
            padding: 0;
            font-size: 32px;
            font-family: Tahoma, sans-serif;
            font-weight: bold;
            color: #553D21;
            max-width: 520px;
            white-space: pre-line;
        }

        .cook-time-r {
            margin-top: 11px;
            font-family: Tahoma, sans-serif;
            font-size: 20px;
            font-weight: bold;
            color: #553D21;
            max-width: 520px;
            height: 23px;
        }

        .section-heading-r {
            margin-top: 10px;
            font-family: Tahoma, sans-serif;
            font-size: 20px;
            font-weight: bold;
            color: #553D21;
            max-width: 162px;
            height: 27px;
        }

        .ingredients-list-r {
            font-size: 16px;
            list-style-type: none;
            color: #553D21;
            padding-left: 0; /* Убираем отступы по левому краю, которые могут быть установлены по умолчанию */
            font-family: Tahoma, sans-serif;
            max-width: 500px;
        }

        .instruction-heading-r {
            margin-top: 10px;
            font-size: 20px;
            font-weight: bold;
            font-family: Tahoma, sans-serif;
            color: #553D21;
            max-width: 162px;
            height: 27px;
        }

        .instruction-text-r {
            font-family: Tahoma, sans-serif;
            font-size: 16px;
            color: #553D21;
            max-width: 1067px;
        }

        .recipe-image-r {
            position: absolute;
            top: 40px;
            left: 572px; /* Регулируйте это значение в зависимости от вашего дизайна */
            width: 527px;
            border-radius: 7px;
            height: 244px;
            object-fit: cover;
        }

    </style>
</head>

<body>
<div class="header">
    <div class="container">
        <div class="header-line">
            <div class="header-logo">
                <a href="index.html"><img src="logo.png" alt=""></a>
            </div>

            <div class="boxContainer">
                <table class="elementsContainer">
                    <tr>
                        <td>
                            <input type="text" placeholder="Поиск по блюдам" class="search">
                        </td>
                        <td>
                            <a href="#">
                                <img src="search.png" alt="">
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="person">
                <a href="authorization.html" title="Личный кабинет">
                    <img src="person.png" alt="">
                </a>
            </div>

        </div>
    </div>
    </div>

    <hr color=#664928 />
    <div class="header-title-reg">Рецепт</div>
    <div class="recipe-container-r">
        <!-- Получаемые из базы данных данные -->
        <h1 class="recipe-title-r"><?php echo $recipe['recipe_name']; ?></h1>
        
        <p class="cook-time-r">Время приготовления: <?php echo $recipe['cook_time']; ?></p>

        <div class="ingredients-section-r">
            <p class="section-heading-r">Ингредиенты:</p>
            <ul class="ingredients-list-r">
                <?php
                // Вставляем ингредиенты из базы данных
                foreach ($ingredients as $ingredient) {
                    echo "<li>{$ingredient['ingredient_name']} - {$ingredient['ingredient_amount']}</li>";
                }
                ?>
            </ul>
        </div>

        <div class="instruction-section-r">
            <p class="instruction-heading-r">Инструкция:</p>
            <p class="instruction-text-r"><?php echo nl2br($recipe['instruction']); ?></p>
        </div>

        <img src="<?php echo $recipe['photo_link']; ?>" alt="Фотография рецепта" class="recipe-image-r">
    </div>
</body>
</html>
    
