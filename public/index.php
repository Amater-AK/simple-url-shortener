<?php
declare(strict_types=1);

require_once __DIR__ ."/../vendor/autoload.php";

use SUS\Core\Router;
use SUS\Core\Application;

$router = new Router();
$router->get("/", "Index page");
$router->get("/features", "Features page");
$router->get("/about", "About page");

$app = new Application($router);
$app->run();