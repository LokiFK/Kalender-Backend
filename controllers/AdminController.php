<?php

    class AdminController {

        public function landingPage(Request $req, Response $res){
            echo $res->view('admin/landingPage');
        }
    }

?>
