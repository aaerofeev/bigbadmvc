<?php

error_reporting(E_ALL);

define('APP_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'));

require_once APP_ROOT . '/vendor/autoload.php';

use Framework\Core\Application;
use Framework\Http\Router;

$config = require_once APP_ROOT . '/config/app.php';

$router = new Router();
$router->get('', 'HomeController@index');

$router->get('login', 'HomeController@login');
$router->get('logout', 'HomeController@logout');
$router->post('login', 'HomeController@storeLogin');

$router->resource('tasks', 'TaskController');
$router->post('tasks/preview', 'TaskController@preview');

$application = new Application($config);
$application->setRouter($router);
echo $application->run();