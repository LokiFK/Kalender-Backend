<?php

    class UserController{

        public function appointmentMaking(Request $req, Response $res){
          $view = $res->view('user/appointmentMaking');
          $view = $res->view('user/template',['content'=>$view]);
          echo $view; 
        }

        public function appointmentOverview(Request $req, Response $res){
            $view = $res->view('user/appointmentOverview');
            $view = $res->view('user/template',['content'=>$view]);
            echo $view; 
        }

          public function profile(Request $req, Response $res){
            $view = $res->view('user/profile');
            $view = $res->view('user/template',['content'=>$view]);
            echo $view; 
        }

    }

?>    