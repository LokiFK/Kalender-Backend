<?php

    class AdminController {

        public function landingPage(Request $req, Response $res) {
            Middleware::status(3);
            echo $res->view('admin/landingPage');
        }
    }

?>
