<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './helpers/DB.php';
    require_once './helpers/Routes.php';
    require_once './helpers/Auth.php';
    require_once './helpers/Queryable.php';
    require_once './helpers/Model.php';
    require './models/UserModel.php';
    require './models/TokenModel.php';
    require './models/TestModel.php';
    require './controllers/AuthController.php';

  
    $routes = new Routes();

    $routes->add('auth', Routes::METHOD_GET, array('AuthController', 'login'));
    $routes->add('auth/new', Routes::METHOD_POST, array('AuthController', 'logout'));

    $routes->listen();


?>
