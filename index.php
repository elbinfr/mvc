<?php

require_once "vendor/autoload.php";

require_once "config/global.php";

$action = prepareRoute($_SERVER['REQUEST_URI']);
$route = processRoute($action);
loadRoute($route);
