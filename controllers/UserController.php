<?php

    class UserController {

        public function new(Request $req, Response $res){
            $view = $res->view('user/new');
            echo $view;
        }

        public function overview(Request $req, Response $res){
            $view = $res->view('user/overview');
            echo $view; 
        }

        public function profile(Request $req, Response $res){
            $view = $res->view('user/profile');
            echo $view; 
        }

    }

?>
