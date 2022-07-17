<?php
declare(strict_types=1);

namespace SUS\Core;

use SUS\Core\Request;
use SUS\Core\Router;

class Application {

    private Request $request;
    private Router $router;
    
    public function __construct() {
        $this->request = new Request();
        $this->router = new Router($this->request);

        // Создать потом Response
    }

    public function getRouter(): Router {
        return $this->router;
    }

    public function run() {
        $this->router->resolve();
    }
}