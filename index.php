<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require './helpers/index.php';
    require_once './Auth/Auth.php';
    require './controllers/GeneralController.php';
    require './controllers/AuthController.php';
    require './controllers/UserController.php';
    require './controllers/AdminController.php';
    require './controllers/FormController.php';

    $routes = new Routes();

    $routes->get('/', 'GeneralController@startPage');

    // --- USER --- \\
    // Appointments
    $routes->get('/user/landingPage', 'UserController@landingPage');
    $routes->get('/user/appointments/new', 'UserController@new');
    $routes->get('/user/appointments/new2', 'UserController@new2');
    $routes->get('/user/appointments/new3', 'UserController@new3');
    $routes->get('/user/appointments/overview', 'UserController@overview');





    // Profile
    $routes->get('/user/profile', 'UserController@profile');
    $routes->post('/user/profile/update', 'UserController@update');
    $routes->post('/user/profile/delete', 'UserController@delete');

    
    // --- AUTH --- \\
    // Register
    $routes->both('/auth/user/create', 'AuthController@createUser');
    
    $routes->both('/auth/account/create', 'AuthController@createAccount');

    $routes->get('/auth/account/approve', 'AuthController@approve');
    $routes->both('/auth/account/notApproved', 'AuthController@notApproved');

    $routes->both('/auth/account/resetLink', 'AuthController@resetLink');
    $routes->both('/auth/account/resetPassword', 'AuthController@resetPassword');

    $routes->both('/auth/account/resetUserdata', 'AuthController@resetUserdata');
    $routes->both('/auth/account/dataReset', 'AuthController@dataReset');

    // Login
    $routes->both('/auth/login', 'AuthController@login');

    // Logout
    $routes->get('/auth/logout', 'AuthController@logout');

    //Logout
    $routes->get('/auth/logout', 'AuthController@logout');

    // Permissions
    $routes->get('/api/auth/permissions', 'AuthController@permissions');
    

    // --- ADMIN --- \\
    $routes->get('/admin/landingPage', 'AdminController@landingPage');

    $routes->both('/admin/rooms', 'AdminController@rooms');
    $routes->both('/admin/room/change', 'AdminController@roomChange');
    $routes->both('/admin/treatments', 'AdminController@treatments');
    $routes->both('/admin/treatment/change', 'AdminController@treatmentChange');
    $routes->both('/admin/appointment/new', 'AdminController@newAppointment');
    $routes->both('/admin/pending', 'AdminController@pending');
    $routes->both('/admin/search/search', 'AdminController@search');
    $routes->both('/admin/search/user', 'AdminController@user');
    $routes->both('/admin/search/userChange', 'AdminController@userChange');
    $routes->both('/admin/search/createUser', 'AdminController@createUser');
    $routes->both('/admin/personalAppointments', 'AdminController@personalAppointments');



    // --- FORM --- \\
    $routes->get('/form/validate', 'FormController@validate');

    $routes->listen();

?>
