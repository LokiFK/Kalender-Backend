<?php

    class UserController{

        public function appointmentMaking(Request $req, Response $res){
            $view = $res->view('user/appointmentMaking', ['title' => 'BLABLABLA'], array(), ['stuff' => ["1", "2", "3"], 'thing' => ["a", "b", "c"]]);
            echo $view;
        }

        public function appointmentOverview(Request $req, Response $res){
            $view = $res->view('user/appointmentOverview',["title"=>"Terminübersicht"]);
            echo $view; 
        }

        public function profile(Request $req, Response $res){
            $view = $res->view('user/profile',["title"=>"Profil"]);
            echo $view; 
        }

    }

?>    