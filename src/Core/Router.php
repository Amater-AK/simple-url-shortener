<?php
declare(strict_types=1);

namespace SUS\Core;

class Router {
    private array $routes;
    //private Request $request;

    /*public function __construct(Request $request) {
        $this->request = $request;
    }*/

    public function get(string $alias, string $content) {
        $this->routes["get"][$alias] = $content;
    }

    public function resolve() {
        // TODO: Проверить алгоритм на нормальную отработку при разных ситуациях
        // Например, с get параметрами ?id=1&title=test
        // Например, features/some/
        // Например, features/some?id=1
        // Например, short_url
        // Например, short_url/some/
        // Например, short_url?id=1
        // Например, short_url/some?id=1

        // TODO: Перенести в Request
        $request_url = trim($_SERVER["REQUEST_URI"], "/");
        $request_url = filter_var($request_url, FILTER_SANITIZE_URL) ?: "";
        if(empty($request_url)) {
            // TODO: throw RequestExeption("Не получилось обработать (sanitize) URI запроса.")
            // Применить значения по умолчанию для Request
        }

        // TODO: Перенести в Request
        $request_query = "";
        $request_query_pos = strpos($request_url, "?");
        if($request_query_pos !== false) {
            $request_query = substr($request_url, $request_query_pos);
            $request_url = substr($request_url, 0, $request_query_pos);
        }
        $request_url = "/" .$request_url;

        // TODO: Перенести в Request
        $request_method = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_ENCODED) ?: "";
        if(empty($request_method)) {
            // TODO: throw RequestExeption("Не получилось обработать (sanitize) метод запроса.")
            // Применить значения по умолчанию для Request
        }

        /*echo "<pre>";
        print_r($_SERVER);
        echo "</pre>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";*/
        
        // Поиск и обработка запрашиваемого адреса
        if(!array_key_exists($request_method, $this->routes)) {
            // По умолчанию для Router
            $request_method = "get";
        }
        if(!array_key_exists($request_url, $this->routes["get"])) {
            // По умолчанию для Router
            $request_url = "/";
        }
        // TODO: Если адреса нет среди заданных, то предполагаем, что это короткая ссылка
        // TODO: Если это и не короткая ссылка, то задаём главную или 404 страницу
    
        $content = $this->routes[$request_method][$request_url];

        // Вывод
        // TODO: Вызов нужного обработчика запроса
        echo $content;
    }
}