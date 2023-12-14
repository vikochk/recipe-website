// // script.js

// document.addEventListener('DOMContentLoaded', function () {
//     var clearButton = document.getElementById('clearButton');
//     var addButton = document.getElementById('addButton');
//     var ingredientsList = document.getElementById('ingredientsList');

//     if (clearButton) {
//         clearButton.addEventListener('click', function (event) {
//             event.preventDefault();
//             var searchIngredient = document.querySelector('.searchIngredient');

//             if (searchIngredient) {
//                 searchIngredient.value = '';
//             }
//         });
//     }

//     if (addButton) {
//         addButton.addEventListener('click', function (event) {
//             event.preventDefault();
//             var searchIngredient = document.querySelector('.searchIngredient').value;

//             if (searchIngredient) {
//                 // Создаем новый элемент для добавленного ингредиента
//                 var newIngredient = document.createElement('div');
//                 newIngredient.className = 'ingredient-item';

//                 var ingredientText = document.createElement('span');
//                 ingredientText.textContent = searchIngredient;

//                 var deleteButton = document.createElement('span');
//                 deleteButton.className = 'delete-button';
//                 deleteButton.textContent = '✖';

//                 deleteButton.addEventListener('click', function () {
//                     ingredientsList.removeChild(newIngredient);
//                 });

//                 newIngredient.appendChild(ingredientText);
//                 newIngredient.appendChild(deleteButton);

//                 // Добавляем новый ингредиент в список
//                 ingredientsList.appendChild(newIngredient);

//                 // Очищаем строку поиска
//                 document.querySelector('.searchIngredient').value = '';
//             }
//         });
//     }
// });

document.addEventListener('DOMContentLoaded', function () {
    var clearButton = document.getElementById('clearButton');
    var addButton = document.getElementById('addButton');
    var findRecipeButton = document.querySelector('.findRecipeButton');
    var ingredientsList = document.getElementById('ingredientsList');

    if (clearButton) {
        clearButton.addEventListener('click', function (event) {
            event.preventDefault();
            var searchIngredient = document.querySelector('.searchIngredient');

            if (searchIngredient) {
                searchIngredient.value = '';
            }
        });
    }

    if (addButton) {
        addButton.addEventListener('click', function (event) {
            event.preventDefault();
            var searchIngredient = document.querySelector('.searchIngredient').value;

            if (searchIngredient) {
                // Создаем новый элемент для добавленного ингредиента
                var newIngredient = document.createElement('div');
                newIngredient.className = 'ingredient-item';

                var ingredientText = document.createElement('span');
                ingredientText.textContent = searchIngredient;

                var deleteButton = document.createElement('span');
                deleteButton.className = 'delete-button';
                deleteButton.textContent = '✖';

                deleteButton.addEventListener('click', function () {
                    ingredientsList.removeChild(newIngredient);
                });

                newIngredient.appendChild(ingredientText);
                newIngredient.appendChild(deleteButton);

                // Добавляем новый ингредиент в список
                ingredientsList.appendChild(newIngredient);

                // Очищаем строку поиска
                document.querySelector('.searchIngredient').value = '';
            }
        });
    }


    // Вызываем функцию searchRecipes при клике на кнопку поиска
    if (findRecipeButton) {
        findRecipeButton.addEventListener('click', searchRecipes);
    }



});
function searchRecipes() {
    // Получаем ингредиенты из вашего списка
    var ingredients = [];
    var ingredientItems = document.querySelectorAll('.ingredient-item span');

    ingredientItems.forEach(function (item) {
        ingredients.push(item.textContent);
    });

    console.log('Отправляем на сервер следующие ингредиенты:', ingredients);

    // Отправляем данные на серверный скрипт
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'searchIngredients.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Получаем ответ от сервера
            var response = JSON.parse(xhr.responseText);

            // Выводим ответ в консоль для отладки
            console.log(response);
            sessionStorage.setItem('searchResults', JSON.stringify(response));

            const ingredientsParam = encodeURIComponent(JSON.stringify(ingredients));
            window.location.href = 'foundRecipes.html' + (ingredients ? '?ingredients=' + ingredientsParam : '');

            // Открываем новую страницу с результатами и передаем параметры в URL
            // window.location.href = 'foundRecipes.html?ingredients=' + encodeURIComponent(JSON.stringify(ingredients));
        }
    };

    // Формируем данные для отправки
    var data = 'ingredients=' + encodeURIComponent(JSON.stringify(ingredients));

    console.log('Data sent to the server:', data);

    // Отправляем запрос
    xhr.send(data);
}


function searchByDish() {
    var dishQuery = document.getElementById('dishSearchInput').value;
    console.log('Запрос по блюдам:', dishQuery);

    // Отправляем запрос на серверный скрипт
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'searchRecipes.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Получаем ответ от сервера
            const response = JSON.parse(xhr.responseText);

            // Выводим ответ в консоль для отладки
            console.log(response);
            sessionStorage.setItem('searchResults', JSON.stringify(response));

            const dishResults = response;
            // Обрабатываем результаты поиска и отображаем их на странице
            const dishResultsParam = encodeURIComponent(JSON.stringify(dishResults));
            window.location.href = 'foundRecipes.html' + (dishResults ? '?tag=' + dishResultsParam : '');

        }
    };

    // Формируем данные для отправки
    var data = 'tag=' + encodeURIComponent(dishQuery);

    // Отправляем запрос
    xhr.send(data);


}
