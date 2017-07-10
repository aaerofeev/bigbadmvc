<?php

return [
    'appName' => 'TodoMVC',
    'controllerNamespace' => 'App\Controllers',
    'viewPath' => APP_ROOT . '/resources/views',
    'storageConnect' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mvc_example;charset=utf8',
        'user' => 'root',
        'password' => 'mysql'
    ],
];