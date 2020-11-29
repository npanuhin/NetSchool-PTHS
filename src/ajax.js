function urlencode(data) {
    let res = [];
    for (let key in data) {
        if (data.hasOwnProperty(key)) {
            res.push(key + '=' + encodeURIComponent(data[key]));
        }
    }
    return res.join('&');
}

function ajax(method, path, data = {}, success = () => {}, error = () => {}, complete = () => {}) {
    /*
    Функция посылки запроса к файлу на сервере
    method  - тип запроса: GET или POST
    path    - путь к файлу
    data    - словарь данных
    handler - функция-обработчик ответа от сервера
    */

    // Создаём запрос
    let Request = false;
    if (window.XMLHttpRequest) {
        Request = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            Request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (CatchException) {
            Request = new ActiveXObject("Msxml2.XMLHTTP");
        }
    }
    if (!Request) {
        concole.log("XMLHttpRequest");
    }

    // Проверяем существование запроса еще раз
    if (!Request) {
        return;
    }

    // Назначаем пользовательский обработчик
    Request.onreadystatechange = function() {
        // Если обмен данными завершен
        if (Request.readyState == 4) {
            if (Request.status == 200) {
                // Передаем управление обработчику пользователя
                success(Request);
            } else {
                // Оповещаем пользователя о произошедшей ошибке
                error(Request);
            }
            complete(Request);
        }
    }

    // Закодируем данные
    data = urlencode(data);

    // Проверяем, если требуется сделать GET-запрос
    if (method.toLowerCase() == "get" && data.length > 0)
        path += "?" + data;

    // Инициализируем соединение
    Request.open(method, path, true);

    if (method.toLowerCase() == "post") {
        // Если это POST-запрос:
        // Устанавливаем заголовок
        Request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
        // Посылаем запрос
        Request.send(data);
    } else {
        // Если это GET-запрос:
        // Посылаем нуль-запрос
        Request.send(null);
    }
}