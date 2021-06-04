<?php

    class AuthController {

        public function register(Request $req, Response $res)
        {
            echo $res->view('register');
        }

        public function createUser(Request $req, Response $res)
        {
            $username = $req->getBody()['username'];
            $email = $req->getBody()['email'];
            $password = $req->getBody()['password'];

            $res->json(
                array(
                    'username' => $username,
                    'email' => $email,
                    'password' => $password
                )
            );
        }
    }

?>
