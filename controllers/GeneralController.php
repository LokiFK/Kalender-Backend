<?php

    class GeneralController{

        public function startPage(Request $req, Response $res){
            if(Auth::getStatus()>2){
                Path::redirect('/admin/landingPage');
            } else {
                Path::redirect('/user/landingPage');
            }
        }

    }

?>