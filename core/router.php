<?php

function prepareRoute($request)
{
	$url = parse_url($request, PHP_URL_PATH);

	$indexPosition = strpos($url, 'index.php');

	$urlArray = explode('/', substr ( $url , $indexPosition ));

    if (count($urlArray) < 2) {
        return '/';
    }

	return $urlArray[1];

}

function processRoute($action)
{
	$routes = require_once "config/routes.php";

	if (array_key_exists($action, $routes)) {
		$route = $routes[$action];
	} else {
		die('No existe ruta');
	}

	return $route;
}

function loadRoute($route)
{
	$controller = loadController($route['controller']);
	evaluateMethod($controller, $route['method']);
}

function loadController($controller)
{
	$fileController = "controllers/$controller.php";

	if (!is_file($fileController)) {
		$fileController = "controllers/{ucwords(DEFAULT_CONTROLLER)}Controller.php";
	}

    $controller = "\App\controllers\\$controller";
	$controllerObj = new $controller;

	return $controllerObj;
}

function evaluateMethod($controller, $method)
{
	if (method_exists($controller, $method)) {
		loadMethod($controller, $method);
	} else {
		loadMethod($controller, DEFAULT_ACTION);
	}
}

function loadMethod($controller, $method)
{
	$controller->$method();
}
