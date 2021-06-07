<?php

    class GeneralController{

        public function startPage(Request $req, Response $res){
          $controller = new UserController(); 
          $controller->appointmentMaking($req,$res);
        }

    }

?>    