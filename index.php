<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './controllers/AuthController.php';

    $routes = new Routes();

    // Register
    $routes->view('/users/register', 'AuthController@register');
    $routes->post('/users/createUser', 'AuthController@createUser');

    // Login
    $routes->view('/users/login', 'AuthController@login');
    $routes->get('/users/getUser', 'AuthController@getUser');


    $routes->get('/test', 'AuthController@test');


    $routes->listen();


?>
