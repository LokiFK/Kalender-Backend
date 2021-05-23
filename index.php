<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './models/UserModel.php';
    require './models/TokenModel.php';
    require './models/TestModel.php';
    require './controllers/AuthController.php';

  
    $routes = new Routes();

    $routes->add('route', Routes::METHOD_GET, array('AuthController', 'login'));
    $routes->add('route/new', Routes::METHOD_POST, array('AuthController', 'logout'));

    $routes->listen();


?>
