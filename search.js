// Функция для отправки асинхронного запроса на сервер
function searchIngredients(query) {
    const resultsContainer = document.getElementById('results');

    // Создаем новый XMLHttpRequest объект
    const xhr = new XMLHttpRequest();

    // Настраиваем запрос
    xhr.open('GET', `search.php?ingredient=${encodeURIComponent(query)}`, true);

    // Устанавливаем обработчик события для ответа
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Обработка JSON-ответа
            const results = JSON.parse(xhr.responseText);

            // Очищаем контейнер результатов
            resultsContainer.innerHTML = '';

            // Вывод результатов
            results.forEach(result => {
                resultsContainer.innerHTML += `<div>${result.name}</div>`;
            });
        } else {
            console.error('Ошибка запроса:', xhr.statusText);
        }
    };

    // Отправляем запрос на сервер
    xhr.send();
}

// Функция для обновления результатов поиска при вводе
function updateResults() {
    const searchInput = document.getElementById('ingredient');
    const query = searchInput.value;

    // Вызываем функцию поиска только если введенное значение не пустое
    if (query.trim() !== '') {
        searchIngredients(query);
    } else {
        // Если введенное значение пустое, очищаем результаты поиска
        const resultsContainer = document.getElementById('results');
        resultsContainer.innerHTML = '';
    }
}

// Функция для обработки выбора ингредиента из результатов поиска
function selectIngredient(ingredientId, ingredientName) {
    document.getElementById('selected_ingredient_id').value = ingredientId;
    document.getElementById('ingredient').value = ingredientName;
    document.getElementById('results').innerHTML = ''; // Очищаем результаты поиска
}
