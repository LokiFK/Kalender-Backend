<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './controllers/RoutesController.php';


    $routes = new Routes();

    $routes->get('/route', 'RoutesController@login');

    $routes->listen();


?>
