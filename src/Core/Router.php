<?php
declare(strict_types=1);

namespace SUS\Core;

class Router {

    private array $routes = [];
    private string $default_uri = "";
    private Request $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function get(string $uri, string $content) {
        $this->routes["get"][$uri] = $content;
    }

    public function default(string $uri) {
        $this->default_uri = $uri;
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

        $request_method = $this->request->getMethod();
        $request_uri = $this->request->getUri();
        
        // Поиск и обработка запрашиваемого адреса
        // Если в routes нет нужного метода, то пытаемся просто перейти
        // на страницу, с которой был запрос
        if(!array_key_exists($request_method, $this->routes)) {
            $request_method = "get";
            // TODO: Пересмотреть
        }
        if(!array_key_exists($request_uri, $this->routes[$request_method])) {
            $request_uri = $this->default_uri;
        }
    
        $content = $this->routes[$request_method][$request_uri];

        // Вывод
        // TODO: Вызов нужного обработчика запроса
        echo $content;
    }
}