<?php
declare(strict_types=1);

require_once __DIR__ ."/../vendor/autoload.php";

use SUS\Core\Application;

$app = new Application();

$app->getRouter()->get("/", "Index page");
$app->getRouter()->get("/features", "Features page");
$app->getRouter()->get("/about", "About page");
$app->getRouter()->default("/");

$app->run();