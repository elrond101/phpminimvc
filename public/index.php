<?php

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

$router = new Framework\Router();
$router->add('', 'users#index');
$router->add('users', 'users#index');
$router->add('create', 'users#create');
$router->add('update', 'users#update');
$router->add('show', 'users#show');
$router->add('destroy', 'users#destroy');
$router->dispatch($_SERVER['REQUEST_URI']);
