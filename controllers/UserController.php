<?php

    class UserController {

        public function appointmentMaking(Request $req, Response $res){
            $view = $res->view('user/appointmentMaking', array(), array(), ['stuff' => ["1", "2", "3"], 'thing' => ["a", "b", "c"]]);
            echo $view;
            DB::table('users')->where('username = :username', [':username' => 'david'])->get();
        }

        public function appointmentOverview(Request $req, Response $res){
            $view = $res->view('user/appointmentOverview');
            echo $view; 
        }

        public function profile(Request $req, Response $res){
            $view = $res->view('user/profile');
            echo $view; 
        }

    }

?>
