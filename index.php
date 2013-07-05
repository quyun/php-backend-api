<?php
date_default_timezone_set('Asia/Shanghai');

require 'app/lib/Slim/Slim.php';
require 'app/lib/Controller.php';
require 'app/lib/Backend.class.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'debug' => true,
    'log.enable' => false,
));
$app->config('dbpath', 'app/data/sqlite3.db');

$app->notFound(function () use ($app) {
    $app->response()->status(404);
});

$app->get('/', function () use ($app) {
    $req = $app->request();
    $controllerName = $req->get('c');
    $actionName = $req->get('a');
    if (!$controllerName) $controllerName = 'index';
    if (!$actionName) $actionName = 'index';
    
    $controller_path = "app/controllers/{$controllerName}.php";
    if (is_file($controller_path)) {
        require_once($controller_path);
        
        $className = ucfirst($controllerName).'Controller';
        $methodName = $actionName.'Action';

        if (!class_exists($className)) $app->notFound();
        $controller = new $className($app);
        if (!method_exists($controller, $methodName)) $app->notFound();

        $controller->$methodName();
    } else {
        $app->notFound();
    }
});

$app->run();
