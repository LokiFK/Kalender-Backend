<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './helpers/DB.php';
    require_once './helpers/Routes.php';
    require_once './helpers/Auth.php';
    require './controllers/AuthController.php';

    $db = new DB();
    $routes = new Routes($db);


    $routes->add('auth', Routes::METHOD_GET, array('AuthController', 'login'));
    $routes->add('auth/new', Routes::METHOD_POST, array('AuthController', 'logout'));

    $routes->listen();


?>
