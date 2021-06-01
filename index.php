<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './controllers/RoutesController.php';


    $routes = new Routes();

    /**
     * Add routes here
     */

    $routes->get('/route', 'RoutesCOntroller@login');

    $routes->get('/users/isUsernameAvailable', 'RoutesController@index');
    $routes->post('/users/register', 'RoutesController@auth');
    $routes->post('/users/login', 'RoutesController@auth');
    $routes->post('/users/logout', 'RoutesController@auth');
    $routes->post('/users/profile/changeApprovalEmail', 'RoutesController@auth');
    $routes->get('/users/profile/approveEmail', 'RoutesController@auth');
    $routes->post('/users/profile/changePassword', 'RoutesController@auth');
    $routes->post('/users/profile/update', 'RoutesController@auth');
    $routes->post('/users/profile/delete', 'RoutesController@auth');
    $routes->get('/users/profile/requestPasswordReset', 'RoutesController@auth');
    $routes->post('/users/profile/resetPassword', 'RoutesController@auth');

    $routes->listen();


?>
