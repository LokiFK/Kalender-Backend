<?php

    class UserController{

        public function appointmentMaking(Request $req, Response $res){
          $view = $res->view('user/appointmentMaking',["title"=>"Terminvereinbarung"]);
          $view = $res->view('user/template',['content'=>$view]);
          echo $view; 
        }

        public function appointmentOverview(Request $req, Response $res){
            $view = $res->view('user/appointmentOverview',["title"=>"Terminübersicht"]);
            $view = $res->view('user/template',['content'=>$view]);
            echo $view; 
        }

          public function profile(Request $req, Response $res){
            $view = $res->view('user/profile',["title"=>"Profil"]);
            $view = $res->view('user/template',['content'=>$view]);
            echo $view; 
        }

    }

?>    