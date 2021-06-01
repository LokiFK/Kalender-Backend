<?php

    class RoutesController {
        public function login(Request $req, Response $res)
        {
            $data = [array('a' => 'b', 'c' => 'd'), array('c' => 'f', 'g' => 't', 'a' => 'z')];
            if (true) {
                $table = UI::table($data, ['c', 'a'], UI::TABLE_STYLE_DEFAULT);
                echo $res->view('./public/index.html', array('isWorking' => true, 'table' => $table));
            }
        }
    }
?>

