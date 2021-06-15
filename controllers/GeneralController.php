<?php

    class GeneralController{

        public function startPage(Request $req, Response $res){
            Path::redirect('/user/landingPage');
        }

    }

?>