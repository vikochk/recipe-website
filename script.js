// script.js

document.addEventListener('DOMContentLoaded', function () {
    var clearButton = document.getElementById('clearButton');
    var addButton = document.getElementById('addButton');
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
});
