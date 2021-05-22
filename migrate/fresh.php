<?php

    require_once './helpers/DB.php';
    require_once './helpers/Routes.php';
    require_once './helpers/Auth.php';
    require_once './helpers/Queryable.php';
    require_once './helpers/Model.php';
    require './models/UserModel.php';
    require './models/TokenModel.php';
    require './models/TestModel.php';
    
    Tests::drop('tests');
    Tokens::drop('tokens');
    Users::drop('users');
    
    new Users('users');
    new Tokens('tokens');
    new Tests('tests');
    