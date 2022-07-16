<?php
declare(strict_types=1);

namespace SUS\Core;

class Application {
    private Router $router;
    
    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function run() {
        $this->router->resolve();
    }
}