<?php

    class RoutesController {
        public function login(Request $req, Response $res)
        {
            $data = [array('a' => 'b', 'c' => 'd', 'z' => 'yay'), array('c' => 'f', 'g' => 't', 'a' => 'z')];
            if (true) {
                $table = UI::table($data, ['c', 'a', 'z'], UI::TABLE_STYLE_DEFAULT);
                echo $res->view('index', array('isWorking' => true, 'table' => $table));
            }
        }
    }
?>

