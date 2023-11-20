$(document).ready(function () {

    // Обработчик нажатия на кнопку "Редактировать холодильник"
    $("#editFridgeBtn").on("click", function () {
        // Скрыть текущий список ингредиентов
        // Добавьте код для скрытия или удаления текущего списка ингредиентов

        // Отобразить форму редактирования
        $("#editFridgeForm").show();
    });
    // Автозаполнение
    $(".autocomplete").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "search.php",
                dataType: "json",
                data: {
                    ingredient: request.term
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.name,
                            value: item.id
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            $("#selected_ingredient_id").val(ui.item.value);
        }
    });

    $("#addToFridgeBtn").on("click", function (event) {
        event.preventDefault();

        // Очищаем контейнер сообщения
        $("#messageContainer").html('');

        $.ajax({
            type: "POST",
            url: "add_to_fridge.php",
            data: $("#yourFormId").serialize(),
            success: function (response) {
                var data = JSON.parse(response);
                if (data.success) {
                    // Выводим сообщение об успехе
                    $("#messageContainer").html('<div style="color: green;">' + data.message + '</div>');
                } else {
                    // Выводим сообщение об ошибке
                    $("#messageContainer").html('<div style="color: red;">' + data.message + '</div>');
                }
            },
            error: function () {
                console.error('Ошибка при отправке данных на сервер');
            }
        });
    });
});



function addToFridge() {
    // Очищаем контейнер сообщения
    $("#messageContainer").html('');
    // Получаем значения из формы
    var ingredientId = $("#selected_ingredient_id").val();
    var formData = {
        ingredient_id: ingredientId
    };

    // Отправляем асинхронный запрос на сервер
    $.ajax({
        type: "POST",
        url: "add_to_fridge.php",
        data: formData,
        dataType: "json",
        success: function (data) {
            if (data.success) {
                // Выводим сообщение об успехе
                $("#messageContainer").html('<div style="color: green;">' + data.message + '</div>');

                // Очищаем поле ввода
                $("#addIngredient").val('');
            } else {
                // Выводим сообщение об ошибке
                if (data.notFound) {
                    // Ингредиент не найден в базе данных
                    $("#messageContainer").html('<div style="color: orange;">' + data.message + '</div>');
                } else {
                    // Ошибка при добавлении ингредиента
                    $("#messageContainer").html('<div style="color: red;">' + data.message + '</div>');
                }

                // Очищаем поле ввода в случае ошибки
                $("#addIngredient").val('');
            }
        },
        error: function () {
            console.error('Ошибка при отправке данных на сервер');
        }
    });

    // Предотвращаем стандартное действие формы (перенаправление)
    return false;
}
