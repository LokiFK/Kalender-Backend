<?php

    class UserController {

        public function new(Request $req, Response $res){
            //$treatments = DB::query("select * from treatment");
            $treatments = [ ["name"=>"b1"], ["name"=>"b2"], ["name"=>"b3"] ];
            $view = $res->view('user/new', array(), array(), [ "treatments"=>$treatments ]);
            echo $view;
        }
        public function new2(Request $req, Response $res){
            /*echo $req->getBody();
            $treatment = "b1";
            if(DB::query("select count(*) as Anzahl from treatment where name=:treatment", [ ":treatment"=>$treatment ])[0]['Anzahl']!=1){
                ErrorUI::error(605, "Behandlung nicht gefunden.");
            }*/
            //$treatments = DB::query("select * from treatment");
            $termine = [ [ "datum"=>"14.06.21", "inner"=>[["start"=>"13:00", "end"=>"13:15" ], ["start"=>"14:00", "end"=>"14:15" ]] ], [ "datum"=>"15.06.21", "inner"=>[["start"=>"13:00", "end"=>"13:15" ], ["start"=>"14:00", "end"=>"14:15" ]] ] ];
            $view = $res->view('user/new2', array(), array(), [ "termine"=>$termine ]);
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

        public function get(Request $req, Response $res) {
            $view = $res->view('user/profile');
            echo $view;
        }

    }

?>
