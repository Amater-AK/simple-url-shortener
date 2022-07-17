<?php
declare(strict_types=1);

namespace SUS\Core;

class Request {

    private string $uri = "";
    private string $method = "";
    private string $query = "";
    private string $body = "";

    public function __construct() {
        // Получение и очистка URI
        $request_uri = trim($_SERVER["REQUEST_URI"], "/");
        $request_uri = "/" .$request_uri;
        $request_uri = filter_var($request_uri, FILTER_SANITIZE_URL);
        if($request_uri === false) {
            $request_uri = "";
            // TODO: Notice: Не получилось очистить Request URI.
        }
        $this->uri = $request_uri;

        // Получение строки параметров запроса
        $query_position = strpos($this->uri, "?");
        if($query_position !== false) {
            $this->uri = substr($this->uri, 0, $query_position);
            $this->query = substr($this->uri, $query_position);
        }

        // Получение и очистка метода запроса
        $request_method = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if($request_method === false) {
            $request_method = "get";
            // TODO: Notice: Не получилось очистить Request Method.
        }
        $this->method = strtolower($request_method);

        // Получение и очистка тела запроса
        // TODO:
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getQuery(): string {
        return $this->query;
    }

    public function getBody(): string {
        return $this->body;
    }
}