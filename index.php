<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require './helpers/index.php';
    require_once './Auth/Auth.php';
    require './controllers/GeneralController.php';
    require './controllers/AuthController.php';
    require './controllers/UserController.php';
    require './controllers/AdminController.php';

    $routes = new Routes();

    $routes->get('/', 'GeneralController@startPage');

    // --- USER --- \\
    // Appointments
    $routes->get('/user/landingPage', 'UserController@landingPage');
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

    $routes->get('/auth/account/approve', 'AuthController@approve');
    $routes->get('/auth/account/notApproved', 'AuthController@notApproved');
    $routes->post('/auth/account/notApproved', 'AuthController@notApproved');

    $routes->get('/auth/account/resetLink', 'AuthController@resetLink');
    $routes->post('/auth/account/resetLink', 'AuthController@resetLink');
    $routes->get('/auth/account/resetPassword', 'AuthController@resetPassword');
    $routes->post('/auth/account/resetPassword', 'AuthController@resetPassword');

    // Login
    $routes->get('/auth/login', 'AuthController@login');
    $routes->post('/auth/login', 'AuthController@login');

    // Logout
    $routes->get('/auth/logout', 'AuthController@logout');

    //Logout
    $routes->get('/auth/logout', 'AuthController@logout');

    // Permissions
    $routes->get('/api/auth/permissions', 'AuthController@permissions');
    

    // --- ADMIN --- \\
    $routes->get('/admin/landingPage', 'AdminController@landingPage');

    $routes->get('/auth/rooms', 'AdminController@rooms');
    $routes->post('/auth/rooms', 'AdminController@rooms');
    $routes->get('/auth/treatments', 'AdminController@treatments');
    $routes->post('/auth/treatments', 'AdminController@treatments');


    $routes->listen();

?>
