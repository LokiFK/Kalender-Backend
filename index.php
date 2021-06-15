<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './controllers/GeneralController.php';
    require './controllers/AuthController.php';
    require './controllers/UserController.php';


    $routes = new Routes();

    $routes->get('/', 'GeneralController@startPage');

    // --- USER --- \\
    // Appointments
    $routes->get('/user/appointments/new', 'UserController@new');
    $routes->get('/user/appointments/new2', 'UserController@new2');
    $routes->get('/user/appointments/overview', 'UserController@overview');
    $routes->get('/api/user/appointments/termine', 'UserController@termine');

    // Profile
    $routes->get('/user/profile', 'UserController@get');
    $routes->post('/user/profile/update', 'UserController@update');
    $routes->post('/user/profile/delete', 'UserController@delete');

    
    // --- AUTH --- \\
    // Register
    $routes->get('/auth/user/create', 'AuthController@createUser');
    $routes->post('/auth/user/create', 'AuthController@createUser');
    
    $routes->get('/auth/account/create', 'AuthController@createAccount');
    $routes->post('/auth/account/create', 'AuthController@createAccount');

    $routes->get('/auth', 'AuthController@test');

    // Login
    $routes->post('/auth/login', 'AuthController@login');
    $routes->get('/auth/login', 'AuthController@login');

    //Logout
    $routes->get('/auth/logout', 'AuthController@logout');

    // Permissions
    $routes->get('/api/auth/permissions', 'AuthController@permissions');
    

    // --- ADMIN --- \\


    $routes->listen();

?>
