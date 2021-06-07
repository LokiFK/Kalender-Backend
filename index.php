<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './controllers/GeneralController.php';
    require './controllers/AuthController.php';
    require './controllers/UserController.php';

    $routes = new Routes();

    //user
    $routes->get('/user/appointmentMaking', 'UserController@appointmentMaking');
    $routes->get('/user/appointmentOverview', 'UserController@appointmentOverview');
    $routes->get('/user/profile', 'UserController@profile');

    // Register
    $routes->get('/auth/register', 'AuthController@register');
    $routes->post('/auth/createUser', 'AuthController@createUser');
    
    $routes->get('/auth/registerAccount', 'AuthController@registerAccount');
    $routes->post('/auth/createAccount', 'AuthController@createAccount');

    // Login
    $routes->get('/auth/login', 'AuthController@login');
    $routes->get('/auth/getUser', 'AuthController@getUser');



    $routes->listen();


?>
