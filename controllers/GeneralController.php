<?php

    class GeneralController{

        public function startPage(Request $req, Response $res){
            $controller = new UserController(); 
            $controller->new($req,$res);
        }

    }

?>