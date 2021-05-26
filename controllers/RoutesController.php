<?php

    class RoutesController {

        public function __construct()
        {
            Middleware::auth();
        }


        public function index(Request $req, Response $res)
        {
            $res->send(array('MSG' => 'BULLSHIT'));
        }

        public function auth(Request $req, Response $res)
        {
            $res->send('FOSFJSI');
        }
    }
?>

