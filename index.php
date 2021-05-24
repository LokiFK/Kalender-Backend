<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");

    require_once './Auth/Auth.php';
    require './helpers/index.php';
    require './models/UserModel.php';
    require './models/TokenModel.php';

  
    $routes = new Routes();

    /**
     * Add routes here
     */

    $routes->listen();


?>
