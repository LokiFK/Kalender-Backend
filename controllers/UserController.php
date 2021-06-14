<?php

    class UserController {

        public function new(Request $req, Response $res){
            //$treatments = DB::query("select * from treatment");
            $treatments = [ ["name"=>"b1"], ["name"=>"b1"], ["name"=>"b1"] ];
            $view = $res->view('user/new', array(), array(), [ "treatments"=>$treatments ]);
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
